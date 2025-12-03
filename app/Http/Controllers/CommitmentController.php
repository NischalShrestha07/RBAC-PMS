<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Commitment;
use App\Models\CommitmentFollowup;
use App\Models\Pdc;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommitmentsExport;
use App\Models\Client;
use App\Models\Department;
use App\Models\FailedInstallment;
use App\Models\Installment;
use App\Models\PdcInstallment;
use App\Models\Transactions;
use Illuminate\Support\Facades\DB;

class CommitmentController extends Controller
{

    public function index(Request $request)
    {
        $this->updateData();

        $user = Auth::user();
        $filterDate = $request->get('filter_date', 'all');
        $filterAgent = $request->get('filter_agent');
        $filterClient = $request->get('filter_client');
        $filterStatus = $request->get('filter_status');
        $filterDepartment = $request->get('filter_department');

        // Use getAccessibleData from base controller for proper data scoping
        $query = $this->getAccessibleData(Commitment::class)
            ->with(['agent', 'client'])
            ->whereIn('status', ['Pending', 'Overdue']);

        // Date filters via installments
        if ($filterDate !== 'all') {
            $query->whereHas('installments', function ($q) use ($filterDate, $request) {
                switch ($filterDate) {
                    case 'today':
                        $q->whereDate('due_date', now());
                        break;
                    case 'this_week':
                        $q->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $q->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()]);
                        break;
                    case 'custom':
                        $startDate = $request->get('start_date');
                        $endDate   = $request->get('end_date');
                        if ($startDate && $endDate) {
                            $q->whereBetween('due_date', [$startDate, $endDate]);
                        }
                        break;
                }
            });
        }

        // Department filter
        if ($filterDepartment) {
            $query->where('department_id', $filterDepartment);
        }

        // Agent filter
        if ($filterAgent) {
            $query->whereHas(
                'agent',
                fn($q) =>
                $q->where('name', 'like', '%' . $filterAgent . '%')
            );
        }

        // Client filter
        if ($filterClient) {
            $query->whereHas(
                'client',
                fn($q) =>
                $q->where('name', 'like', '%' . $filterClient . '%')
            );
        }

        // Status filter
        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        // Subquery for next_due_date
        $query->addSelect([
            'next_due_date' => Installment::select('due_date')
                ->whereColumn('commitment_id', 'commitments.id')
                ->where('status', '!=', 'Complete')
                ->orderBy('due_date', 'asc')
                ->limit(1)
        ]);

        // Subquery: display_amount (Overdue first, then Pending)
        // $query->addSelect([
        //     'display_amount' => Installment::selectRaw('
        //     CASE
        //         WHEN SUM(CASE WHEN status = "Overdue" THEN (amount-paid_amount) ELSE 0 END) > 0
        //         THEN SUM(CASE WHEN status = "Overdue" THEN (amount-paid_amount) ELSE 0 END)
        //         ELSE SUM(CASE WHEN status = "Pending" THEN (amount-paid_amount) ELSE 0 END)
        //     END
        // ')->whereColumn('commitment_id', 'commitments.id')
        // ]);

        $query->addSelect([
            'display_amount' => Installment::selectRaw('
        CASE
            WHEN SUM(CASE WHEN status = "Overdue" THEN (amount - paid_amount) ELSE 0 END) > 0
                THEN SUM(CASE WHEN status = "Overdue" THEN (amount - paid_amount) ELSE 0 END)
            ELSE (
                SELECT (amount - paid_amount)
                FROM installments
                WHERE installments.commitment_id = commitments.id
                  AND status = "Pending"
                ORDER BY due_date ASC
                LIMIT 1
            )
        END
    ')->whereColumn('commitment_id', 'commitments.id')
        ]);


        // Ordering
        $query->orderByRaw("
        CASE
            WHEN next_due_date IS NULL THEN 3
            WHEN next_due_date < CURDATE() THEN 0
            WHEN next_due_date = CURDATE() THEN 1
            ELSE 2
        END
    ")->orderBy('next_due_date', 'asc');

        // Paginate
        $commitments = $query->paginate(10)->appends(request()->query());

        // Get agents based on accessible data
        $agents = $this->getAccessibleData(Agent::class)->get();

        // Use a single view for all roles - permission-based access
        $view = 'commits&pdc.superCommitments';

        // Recent commitments for current user
        $recentCommitments = $this->getAccessibleData(Commitment::class)
            ->where('created_by', $user->id)
            ->whereIn('status', ['Pending', 'Overdue'])
            ->latest()
            ->take(5)
            ->get();

        $notifications = $this->getNotifications();
        $clients       = Client::orderBy('name')->get();
        $parties       = Client::with('commitments')->get();
        $transactions  = Transactions::with(['client', 'agent', 'user'])->get();
        $departments = \App\Models\Department::active()->get(); // Add this for department filter

        return view($view, compact(
            'commitments',
            'transactions',
            'parties',
            'clients',
            'notifications',
            'recentCommitments',
            'agents',
            'departments'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $notifications = $this->getNotifications();
        $clients = Client::with('agent')->orderBy('name')->get(); // Added with('agent')
        $agents = ($user->role === 'Super Admin')
            ? Agent::all()
            : Agent::where('department', $user->role)->get();

        $departments = Department::where('is_active', true)->get();
        return view('commits&pdc.partials.super.commitments.add-commitment', compact('clients', 'agents', 'notifications', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'pending_amount' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'nullable',
            'agent_id' => 'required',
            'remarks' => 'nullable',
            'department_id' => 'nullable|exists:departments,id',
            'client_id' => 'nullable|exists:clients,id',
            'new_client' => 'nullable|string'

        ]);

        $client_id = $request->client_id;
        if (!$client_id && $request->new_client) {
            $client = Client::create(['name' => $request->new_client]);
            $client_id = $client->id;

            // Log client creation
            $this->logActivity('create', "Created new client: {$request->new_client}", $client);
        }
        $pending_amount = $request->total_amount - ($request->paid_amount ?? 0);

        $commitment = new Commitment();
        $commitment->name = $request->new_client ?: Client::find($client_id)->name;

        $commitment->department_id = $request->department_id;

        $client = Client::find($request->client_id);
        if ($client) {
            $commitment->name = $client->name;
        }


        $commitment->total_amount = $request->total_amount;
        $commitment->paid_amount = $request->paid_amount ?? 0;
        $commitment->pending_amount = $pending_amount;

        $commitment->due_date = $request->due_date ?? '0001-01-01';
        $commitment->agent_id = $request->agent_id;
        $commitment->client_id = $client_id;
        $commitment->status = $request->status ?? 'Pending';
        $commitment->remarks = $request->remarks;
        $commitment->created_by = Auth::id();

        $commitment->save();

        if ($request->has('installments')) {
            $pending = false;
            $overdue = false;
            foreach ($request->installments as $installment) {
                if ($installment['status'] == 'Pending') {
                    $pending = true;
                }
                if ($installment['status'] == 'Overdue') {
                    $overdue = true;
                }

                Installment::create([
                    'commitment_id' => $commitment->id,
                    'amount' => $installment['amount'],
                    'due_date' => $installment['due_date'],
                    'status' => $installment['status'],
                ]);
            }


            if ($overdue) {
                $commitment->status = 'Overdue';
            } elseif ($pending) {
                $commitment->status = 'Pending';
            } else {
                $commitment->status = 'Complete';
            }
            $commitment->save();
        }
        // Log commitment creation
        $this->logActivity('create', "Created commitment for client: {$commitment->name}", $commitment);

        $this->updateData();

        return redirect()->route('commitments.index')->with('success', 'Commitment created successfully!');
    }

    public function markAsPaid($installmentId)
    {
        $installment = Installment::findOrFail($installmentId);
        $installment->update(['status' => 'Paid']);


        $commitment = $installment->commitment;
        $commitment->paid_amount += $installment->amount;
        $commitment->pending_amount -= $installment->amount;
        $commitment->save();

        return back()->with('success', 'Installment marked as paid.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'agent_id' => 'required|exists:agents,id',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $commitment = Commitment::with('installments')->findOrFail($id);

        // Store old data for logging
        $oldData = $commitment->getAttributes();
        $oldInstallments = $commitment->installments->toArray();

        $client_id = $request->input('client_id');
        $name = Client::find($client_id)->name;

        // Update commitment
        $commitment->update([
            'name' => $name,
            'department_id' => $request->department_id,
            'agent_id' => $request->input('agent_id'),
            'client_id' => $client_id,
            'due_date' => $request->input('due_date') ?? '0001-01-01',
            'remarks' => $request->input('remarks'),
        ]);

        // Handle installments
        if ($request->has('installments')) {
            $submittedIds = collect($request->installments)->pluck('id')->filter()->all();
            $deletedInstallments = $commitment->installments()->whereNotIn('id', $submittedIds)->get();

            // Log deleted installments
            foreach ($deletedInstallments as $installment) {
                $this->logActivity('delete', "Deleted installment: {$installment->amount} due {$installment->due_date}", $installment);
            }

            $commitment->installments()->whereNotIn('id', $submittedIds)->delete();

            $pending = false;
            $overdue = false;

            foreach ($request->installments as $installmentData) {
                $data = [
                    'amount' => $installmentData['amount'],
                    'due_date' => $installmentData['due_date'],
                    'status' => $installmentData['status'],
                ];

                if (isset($installmentData['id']) && $installment = $commitment->installments()->find($installmentData['id'])) {
                    $installment->update($data);
                    $this->logActivity('update', "Updated installment: {$installment->amount} due {$installment->due_date}", $installment);
                } else {
                    $installment = $commitment->installments()->create($data);
                    $this->logActivity('create', "Created installment: {$installment->amount} due {$installment->due_date}", $installment);
                }

                if ($data['status'] === 'Pending') $pending = true;
                if ($data['status'] === 'Overdue') $overdue = true;
            }

            // Update totals based on active installments
            $totalAmount = $commitment->activeInstallments()->sum('amount');
            $paidAmount = $commitment->activeInstallments()->sum('paid_amount');
            $commitment->total_amount = $totalAmount;
            $commitment->pending_amount = $totalAmount - $paidAmount;

            $oldStatus = $commitment->getOriginal('status');
            $commitment->status = $overdue ? 'Overdue' : ($pending ? 'Pending' : 'Complete');
            $commitment->save();

            // Log status change if changed
            if ($oldStatus !== $commitment->status) {
                $this->logStatusChange($commitment, $oldStatus, $commitment->status);
            }
        }

        // Log commitment update
        $this->logActivity('update', "Updated commitment: {$commitment->name}", $commitment);

        $this->updateData();

        return redirect()->back()->with([
            'success' => 'Commitment updated successfully!',
            'commitment_id' => $commitment->id
        ]);
    }

    public function pay(Request $request, $id)
    {
        $request->validate([
            'pay_amount' => 'required|numeric|min:1',
            'remarks' => 'nullable|string',
        ]);


        $installment = Installment::findOrFail($id);
        $remainingAmount = $installment->amount - $installment->paid_amount;
        $pc_id = $request->pc_id;

        if ($request->pay_amount > $remainingAmount) {
            $this->logActivity('payment', "Failed payment attempt: Amount {$request->pay_amount} exceeds pending {$remainingAmount}", $installment, [], [], 'failed');
            return back()->with('error', 'Payment exceeds pending amount.');
        }

        $oldPaidAmount = $installment->paid_amount;
        $installment->paid_amount += $request->pay_amount;
        $installment->status = ($installment->paid_amount >= $installment->amount) ? 'Complete' : 'Pending';
        $installment->save();

        $commitment = $installment->commitment;

        Transactions::create([
            'pc_id' => $pc_id,
            'type' => 'commitment',
            'installment_id' => $installment->id,
            'client_id' => $installment->commitment->client_id,
            'agent_id' => $installment->commitment->agent_id,
            'pay_amount' => $request->pay_amount,
            'login_uid' => Auth::id(),
            'remarks' => $request->remarks,
        ]);

        // Log payment
        $this->logActivity('payment', "Payment of {$request->pay_amount} received for installment", $installment, [
            'paid_amount' => $oldPaidAmount,
            'status' => $installment->getOriginal('status')
        ], [
            'paid_amount' => $installment->paid_amount,
            'status' => $installment->status
        ]);

        return back()->with('success', 'Partial payment recorded successfully.');
    }

    public function show($id)
    {
        $item = Commitment::with(['client', 'agent', 'transactions', 'activeInstallments', 'failedInstallments'])->findOrFail($id);
        $notifications = $this->getNotifications();
        return view('clients.partials.commitment.commitment-show', compact('item', 'notifications'));
    }

    public function showCommitment($id)
    {
        $item = Commitment::with(['client', 'agent', 'transactions', 'activeInstallments', 'failedInstallments', 'department'])->findOrFail($id);
        $notifications = $this->getNotifications();
        return view('commits&pdc.partials.super.commitments.super-commitment-show', compact('item', 'notifications'));
    }

    //for Electronics
    public function showCommitmentElectronics($id)
    {
        $item = Commitment::with(['client', 'agent', 'transactions', 'activeInstallments', 'failedInstallments'])->findOrFail($id);
        $notifications = $this->getNotifications();
        return view('clients.partials.commitment.electronicsCommitment-show', compact('item', 'notifications'));
    }

    //for electrical
    public function showCommitmentElectrical($id)
    {
        $item = Commitment::with(['client', 'agent', 'transactions', 'activeInstallments', 'failedInstallments'])->findOrFail($id);
        $notifications = $this->getNotifications();
        return view('clients.partials.commitment.electricalCommitment-show', compact('item', 'notifications'));
    }


    public function edit($id)
    {
        $item = Commitment::with(['client', 'agent', 'activeInstallments', 'failedInstallments'])->findOrFail($id);
        $clients = Client::all();
        $agents = Agent::all();
        $departments = Department::where('is_active', true)->get();
        $notifications = $this->getNotifications();
        return view('clients.partials.commitment.commitment-edit', compact('item', 'clients', 'agents', 'notifications', 'departments'));
    }

    public function editCommitment($id)
    {
        $item = Commitment::with(['client', 'agent', 'activeInstallments', 'failedInstallments'])->findOrFail($id);
        $clients = Client::all();
        $agents = Agent::all();
        $notifications = $this->getNotifications();
        $departments = Department::all();
        return view('commits&pdc.partials.super.commitments.super-commitment-edit', compact('item', 'clients', 'agents', 'notifications', 'departments'));
    }

    public function failCommitment($id)
    {
        $commitment = Commitment::with('installments')->findOrFail($id);
        // Check if any installment has been paid
        $hasPaidInstallments = $commitment->installments->where('paid_amount', '>', 0)->count() > 0;

        if ($hasPaidInstallments) {
            $this->logActivity('failed', "Failed to mark commitment as failed: Some installments have been paid", $commitment, [], [], 'failed');
            return redirect()->back()->with('error', 'Cannot fail commitment: Some installments have been paid.');
        }

        // Mark commitment as failed and move all installments to failed_installments
        DB::transaction(function () use ($commitment) {
            foreach ($commitment->installments as $installment) {
                FailedInstallment::create([
                    'commitment_id' => $commitment->id,
                    'amount' => $installment->amount,
                    'due_date' => $installment->due_date,
                    'status' => 'Failed',
                ]);

                // Log each failed installment
                $this->logActivity('failed', "Moved installment to failed: {$installment->amount} due {$installment->due_date}", $installment);

                $installment->delete();
            }

            $oldStatus = $commitment->status;
            $commitment->status = 'Failed';
            $commitment->total_amount = 0;
            $commitment->pending_amount = 0;
            $commitment->save();

            // Log commitment failure
            $this->logStatusChange($commitment, $oldStatus, 'Failed');
            $this->logActivity('failed', "Marked commitment as failed", $commitment);
        });

        return redirect()->back()->with('success', 'Commitment marked as failed successfully.');
    }

    public function failInstallment(Request $request, $installmentId)
    {
        $installment = Installment::findOrFail($installmentId);

        if ($installment->paid_amount > 0) {
            return redirect()->back()->with('error', 'Cannot fail installment: It has been partially or fully paid.');
        }

        DB::transaction(function () use ($installment) {
            // Move to failed_installments
            FailedInstallment::create([
                'commitment_id' => $installment->commitment_id,
                'amount' => $installment->amount,
                'due_date' => $installment->due_date,
                'status' => 'Failed',
            ]);

            // Delete the original installment
            $installment->delete();

            // Update commitment totals
            $commitment = Commitment::find($installment->commitment_id);
            $totalAmount = $commitment->activeInstallments()->sum('amount');
            $paidAmount = $commitment->activeInstallments()->sum('paid_amount');
            $commitment->total_amount = $totalAmount;
            $commitment->pending_amount = $totalAmount - $paidAmount;

            // Update commitment status
            $hasPending = $commitment->activeInstallments()->where('status', 'Pending')->exists();
            $hasOverdue = $commitment->activeInstallments()->where('status', 'Overdue')->exists();
            $commitment->status = $hasOverdue ? 'Overdue' : ($hasPending ? 'Pending' : 'Complete');
            $commitment->save();
        });

        return redirect()->back()->with('success', 'Installment marked as failed successfully.');
    }

    public function destroy($id)
    {
        $commitment = Commitment::find($id);

        if (!$commitment) {
            return redirect()->back()->with('error', 'Payment not found or already deleted!');
        }

        $commitment->delete();
        return redirect()->back()->with('success', 'Payment deleted successfully!');
    }

    // public function followup(Request $request, $id)
    // {
    //     $item = Commitment::with('followups')->findOrFail($id);
    //     $followupCount = $item->followups->count();
    //     $followup = new CommitmentFollowup();
    //     $followup->commitment_id = $item->id;
    //     $followup->followup_date = $request->followup_date;
    //     $followup->comments = $request->comments;
    //     $followup->status = 'Pending';
    //     $followup->original_due_date = $item->due_date;
    //     $followup->followup_count = $followupCount + 1;
    //     $followup->next_due_date = $request->followup_date;
    //     $followup->save();
    //     $item->status = 'Overdue';
    //     $item->save();
    //     return back()->with('success', 'Follow-up Added Successfully!');
    // }
    public function followup(Request $request, $id)
    {
        $request->validate([
            'followup_date' => 'required|date',
            'comments' => 'required|string',
            'installment_id' => 'nullable|exists:installments,id', // Added
        ]);

        $item = Commitment::with('followups')->findOrFail($id);

        $installment = null;
        if ($request->installment_id) {
            $installment = Installment::where('commitment_id', $id)->findOrFail($request->installment_id);
            $followupCount = $installment->followups()->count(); // Per installment count
        } else {
            $followupCount = $item->followups->count(); // Commitment level count
        }

        $followup = new CommitmentFollowup();
        $followup->commitment_id = $item->id;
        $followup->installment_id = $request->installment_id;
        $followup->followup_date = $request->followup_date;
        $followup->comments = $request->comments;
        $followup->status = 'pending'; // Lowercase to match enum
        $followup->original_due_date = $installment ? $installment->due_date : $item->due_date;
        $followup->followup_count = $followupCount + 1;
        $followup->next_due_date = $request->followup_date;
        $followup->save();

        if ($installment) {
            $installment->status = 'Overdue';
            $installment->due_date = $request->followup_date; // Extend due date
            $installment->save();
            // Optionally update commitment status based on all installments
            $item->status = $item->installments()->whereNotIn('status', ['Complete'])->exists() ? 'Overdue' : 'Complete';
        } else {
            $item->status = 'Overdue';
            $item->due_date = $request->followup_date; // If no installment, update commitment due date
        }
        $item->save();

        return back()->with('success', 'Follow-up Added Successfully!');
    }
}
