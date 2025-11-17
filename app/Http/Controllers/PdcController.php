<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Commitment;
use App\Models\Department;
use App\Models\Pdc;
use App\Models\PdcFollowup;
use App\Models\PdcInstallment;
use App\Models\Transactions;
use App\Models\FailedPdcInstallment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PdcController extends Controller
{

    public function index(Request $request)
    {
        $this->updateData();

        $user = Auth::user();
        $recentCommitments = Commitment::where('created_by', Auth::id())
            ->whereIn('status', ['Pending', 'Overdue'])
            ->take(5)
            ->get();

        // Use getAccessibleData for proper data scoping
        $query = $this->getAccessibleData(Pdc::class)
            ->with(['agent', 'client', 'installments'])
            ->whereIn('status', ['Pending', 'Overdue']);

        $filterDate = $request->get('filter_date', 'all');
        $filterAgent = $request->get('filter_agent', null);
        $filterClient = $request->get('filter_client', null);
        $filterStatus = $request->get('filter_status', null);
        $filterDepartment = $request->get('filter_department', null);

        if ($filterDate !== 'all') {
            $startDate = now();
            $endDate = now();

            $query->whereHas('installments', function ($q) use ($filterDate, $startDate, $endDate, $request) {
                switch ($filterDate) {
                    case 'today':
                        $q->whereDate('due_date', $startDate);
                        break;
                    case 'this_week':
                        $startDate = now()->startOfWeek();
                        $endDate = now()->endOfWeek();
                        $q->whereBetween('due_date', [$startDate, $endDate]);
                        break;
                    case 'this_month':
                        $startDate = now()->startOfMonth();
                        $endDate = now()->endOfMonth();
                        $q->whereBetween('due_date', [$startDate, $endDate]);
                        break;
                    case 'custom':
                        $startDate = $request->get('start_date');
                        $endDate = $request->get('end_date');
                        if ($startDate && $endDate) {
                            $q->whereBetween('due_date', [$startDate, $endDate]);
                        }
                        break;
                }
            });
        }

        // Department filter - now using department_id
        if ($filterDepartment) {
            $query->where('department_id', $filterDepartment);
        }

        if ($filterAgent) {
            $query->whereHas('agent', function ($q) use ($filterAgent) {
                $q->where('name', 'like', '%' . $filterAgent . '%');
            });
        }

        if ($filterClient) {
            $query->whereHas('client', function ($q) use ($filterClient) {
                $q->where('name', 'like', '%' . $filterClient . '%');
            });
        }

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        $query->addSelect([
            'next_due_date' => PdcInstallment::select('due_date')
                ->whereColumn('pdc_id', 'pdcs.id')
                ->where('status', '!=', 'Complete')
                ->orderBy('due_date', 'asc')
                ->limit(1)
        ]);
        // Subquery: display_amount (Overdue first, then Pending)
        //     $query->addSelect([
        //         'display_amount' => PdcInstallment::selectRaw('
        //     CASE
        //         WHEN SUM(CASE WHEN status = "Overdue" THEN (amount - COALESCE(CAST(data1 AS DECIMAL(20,2)),0)) ELSE 0 END) > 0
        //         THEN SUM(CASE WHEN status = "Overdue" THEN (amount - COALESCE(CAST(data1 AS DECIMAL(20,2)),0)) ELSE 0 END)
        //         ELSE SUM(CASE WHEN status = "Pending" THEN (amount - COALESCE(CAST(data1 AS DECIMAL(20,2)),0)) ELSE 0 END)
        //     END
        // ')->whereColumn('pdc_id', 'pdcs.id')
        //     ]);
        $query->addSelect([
            'display_amount' => PdcInstallment::selectRaw('
        CASE
            WHEN SUM(CASE WHEN status = "Overdue" THEN (amount - COALESCE(CAST(data1 AS DECIMAL(20,2)),0)) ELSE 0 END) > 0
                THEN SUM(CASE WHEN status = "Overdue" THEN (amount - COALESCE(CAST(data1 AS DECIMAL(20,2)),0)) ELSE 0 END)
            ELSE (
                SELECT (amount - COALESCE(CAST(data1 AS DECIMAL(20,2)),0))
                FROM pdc_installments
                WHERE pdc_installments.pdc_id = pdcs.id
                  AND status = "Pending"
                ORDER BY due_date ASC
                LIMIT 1
            )
        END
    ')->whereColumn('pdc_id', 'pdcs.id')
        ]);




        $query->orderByRaw("
        CASE
            WHEN next_due_date IS NULL THEN 3  -- no due date → lowest priority
            WHEN next_due_date < CURDATE() THEN 0  -- overdue → highest priority
            WHEN next_due_date = CURDATE() THEN 1  -- due today
            ELSE 2  -- upcoming
        END
    ")->orderBy('next_due_date', 'asc');

        $pdcs = $query->paginate(10)->appends(request()->query());

        // Get commitments with proper data scoping
        $commitments = $this->getAccessibleData(Commitment::class)
            ->with('pdcs')
            ->whereIn('status', ['Pending', 'Overdue'])
            ->get();

        // Get agents with proper data scoping
        $agents = $this->getAccessibleData(Agent::class)->get();

        // Get departments for filter
        $departments = \App\Models\Department::active()->get();

        // Use a single unified view
        $view = 'pdc.superPdc';

        $notifications = $this->getNotifications();
        $clients = Client::orderBy('name')->get();

        $pdc = Pdc::with('installments')->whereIn('status', ['Pending', 'Overdue'])->get();
        $transactions = Transactions::with(['client', 'agent', 'user'])->get();

        return view($view, compact(
            'recentCommitments',
            'transactions',
            'pdc',
            'clients',
            'pdcs',
            'commitments',
            'agents',
            'departments',
            'notifications'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $clients = Client::orderBy('name')->get();
        $agents = $user->role === 'Super Admin' ? Agent::all() : Agent::where('department', $user->role)->get();
        $notifications = $this->getNotifications();
        $departments = Department::where('is_active', true)->get();
        return view('pdc.partials.super.pdcs.add-pdcs', compact('clients', 'agents', 'notifications', 'departments'));
    }

    //For Pdc
    public function showPdc($id)
    {
        $notifications = $this->getNotifications();
        $pdc = Pdc::with(['client', 'agent', 'installments', 'transactions'])->findOrFail($id);
        return view('pdc.partials.super.pdcs.super-pdcs-show', compact('pdc', 'notifications'));
    }

    //For Client
    public function show($id)
    {
        $notifications = $this->getNotifications();
        $pdc = Pdc::with(['client', 'agent', 'installments', 'transactions'])->findOrFail($id);
        return view('clients.partials.pdc.pdc-show', compact('pdc', 'notifications'));
    }

    public function showPdcElectrical($id)
    {
        $notifications = $this->getNotifications();
        $pdc = Pdc::with(['client', 'agent', 'installments', 'transactions'])->findOrFail($id);
        return view('clients.partials.pdc.electricalPdc-show', compact('pdc', 'notifications'));
    }

    public function showPdcElectronics($id)
    {
        $notifications = $this->getNotifications();
        $pdc = Pdc::with(['client', 'agent', 'installments', 'transactions'])->findOrFail($id);
        return view('clients.partials.pdc.electronicsPdc-show', compact('pdc', 'notifications'));
    }


    public function pay(Request $request, $id)
    {
        $request->validate([
            'pay_amount' => 'required|numeric|min:1',
            'remarks' => 'nullable|string',
        ]);


        $installment = PdcInstallment::findOrFail($id);
        $remainingAmount = $installment->amount - $installment->data1;
        $pc_id = $request->pc_id;

        if ($request->pay_amount > $remainingAmount) {
            return back()->with('error', 'Payment exceeds pending amount.');
        }


        $installment->data1 += $request->pay_amount;
        $installment->status = ($installment->data1 >= $installment->amount) ? 'Complete' : 'Pending';
        $installment->save();

        Transactions::create([
            'pc_id' => $pc_id,
            'type' => 'pdc',
            'installment_id' => $installment->id,
            'client_id' => $installment->pdc->client_id,
            'agent_id' => $installment->pdc->agent_id,
            'pay_amount' => $request->pay_amount,
            'login_uid' => Auth::id(),
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Partial payment recorded successfully.');
    }


    public function getPdcData($id)
    {
        $pdcData = PdcInstallment::where('commitment_id', $id)->get();
        return response()->json($pdcData);
    }


    public function store(Request $request)
    {
        $request->validate([
            'commitment_id' => 'nullable|exists:commitments,id',
            'name' => 'nullable|string',
            'commitment_amount' => 'required|numeric',
            'agent_id' => 'nullable',
            'cheque_number' => 'nullable|string',
            'department' => 'nullable',
            'department_id' => 'nullable|exists:departments,id',
            'bank_name' => 'nullable|string',
            'cheque_date' => 'nullable|date',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',

            'pdc_amounts' => 'nullable|array',
            'pdc_amounts.*.amount' => 'nullable|numeric',
            'pdc_amounts.*.date' => 'nullable|date',
            'pdc_amounts.*.status' => 'nullable|string',
            'pdc_amounts.*.chequeno' => 'nullable',
            'pdc_amounts.*.bankName' => 'nullable',
        ]);


        $client_id = $request->client_id;

        if (!$client_id && $request->new_client) {
            $client = Client::create(['name' => $request->new_client]);
            $client_id = $client->id;
        }

        $payment = new Pdc();
        $payment->commitment_id = $request->commitment_id;

        $payment->name =  $request->new_client ?: Client::find($client_id)->name;

        $payment->agent_id = $request->agent_id;
        $payment->client_id = $request->client_id;
        $payment->commitment_amount = $request->commitment_amount;
        $payment->cheque_number = $request->cheque_number;
        $payment->bank_name = $request->bank_name;
        $payment->department = $request->department;
        $payment->department_id = $request->department_id;
        $payment->cheque_date = $request->cheque_date;
        $payment->status = $request->status;
        $payment->remarks = $request->remarks;
        $payment->created_by = Auth::id();


        $payment->save();

        if (!empty($request->pdc_amounts) && is_array($request->pdc_amounts)) {
            foreach ($request->pdc_amounts as $pdcData) {
                $payment->installments()->create([
                    'amount' => $pdcData['amount'],
                    'bankName' => $pdcData['bankName'],
                    'chequeno' => $pdcData['chequeno'] ?? null,
                    'due_date' => $pdcData['date'],
                    'status' => $pdcData['status'],
                ]);
            }
        }
        $this->updateData();
        return redirect()->route('pdcs.index')->with('success', 'Payment recorded successfully.');
    }

    //For Pdc
    public function editPdc($id)
    {
        $pdc = Pdc::with(['client', 'agent', 'activeInstallments', 'failedInstallments'])->findOrFail($id);
        $clients = Client::all();
        $agents = Agent::all();
        $notifications = $this->getNotifications();
        $departments = Department::where('is_active', true)->get();
        return view('pdc.partials.super.pdcs.super-pdcs-edit', compact('pdc', 'clients', 'agents', 'notifications', 'departments'));
    }

    //For Client
    public function edit($id)
    {
        $pdc = Pdc::with(['client', 'agent', 'activeInstallments', 'failedInstallments'])->findOrFail($id);
        $clients = Client::all();
        $agents = Agent::all();
        $departments = Department::where('is_active', true)->get();
        $notifications = $this->getNotifications();
        return view('clients.partials.pdc.pdc-edit', compact('pdc', 'clients', 'agents', 'notifications', 'departments'));
    }
    public function update(Request $request, $id)
    {

        $request->validate([
            'commitment_id' => 'nullable|exists:commitments,id',
            'agent_id' => 'required|exists:agents,id',
            'name' => 'nullable|string',
            'commitment_amount' => 'required|numeric',
            'cheque_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'cheque_date' => 'nullable|date',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
            'department' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'client_id' => 'required|exists:clients,id',
            'installments' => 'nullable|array',
            'installments.*.id' => 'nullable|exists:pdc_installments,id',
            'installments.*.chequeno' => 'required|string',
            'installments.*.bankName' => 'required|string',
            'installments.*.amount' => 'required|numeric',
            'installments.*.due_date' => 'required|date',
            'installments.*.status' => 'required|string|in:Pending,Complete,Overdue',
        ]);

        $pdc = Pdc::findOrFail($id);

        $client_id = $request->input('client_id');
        $name = $request->input('name') ?? Client::find($client_id)->name;

        $pdc->update([
            'commitment_id' => $request->commitment_id,
            'name' => $name,
            'commitment_amount' => $request->commitment_amount,
            'cheque_number' => $request->cheque_number,
            // 'department' => $request->department,
            'department_id' => $request->department_id,
            'bank_name' => $request->bank_name,
            'remarks' => $request->remarks,
            'agent_id' => $request->agent_id,
            'client_id' => $client_id,
            'status' => $request->status,
            'cheque_date' => $request->cheque_date ?? $pdc->cheque_date,
        ]);

        if ($request->has('installments') && is_array($request->installments)) {
            $pending = false;
            $overdue = false;

            // Track existing installment IDs to identify deleted ones
            $submittedInstallmentIds = array_filter(array_column($request->installments, 'id'));
            $existingInstallmentIds = $pdc->activeInstallments->pluck('id')->toArray();

            foreach ($request->installments as $installmentData) {
                // Skip if required fields are missing
                if (!isset($installmentData['chequeno'], $installmentData['bankName'], $installmentData['amount'], $installmentData['due_date'], $installmentData['status'])) {
                    continue;
                }

                $data = [
                    'chequeno' => $installmentData['chequeno'],
                    'bankName' => $installmentData['bankName'],
                    'amount' => $installmentData['amount'],
                    'due_date' => $installmentData['due_date'],
                    'status' => $installmentData['status'],
                ];

                if (!empty($installmentData['id']) && $installment = $pdc->activeInstallments()->find($installmentData['id'])) {
                    // Update existing installment
                    $installment->update($data);
                } else {
                    // Create new installment
                    $installment = $pdc->installments()->create($data);
                }

                if ($data['status'] === 'Pending') $pending = true;
                if ($data['status'] === 'Overdue') $overdue = true;

                if ($data['status'] === 'Complete') {
                    $transaction = Transactions::where('installment_id', $installment->id)->first();

                    if ($transaction) {
                        $transaction->update([
                            'pay_amount' => $data['amount'],
                            'remarks' => $request->input('remarks', 'Installment payment updated'),
                        ]);
                    } else {
                        Transactions::create([
                            'pc_id' => $pdc->id,
                            'type' => 'pdc',
                            'installment_id' => $installment->id,
                            'client_id' => $pdc->client_id,
                            'agent_id' => $pdc->agent_id,
                            'pay_amount' => $data['amount'],
                            'login_uid' => Auth::id(),
                            'remarks' => $request->input('remarks', 'Installment payment completed'),
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            // Delete installments that were removed from the form
            $deletedInstallmentIds = array_diff($existingInstallmentIds, $submittedInstallmentIds);
            if (!empty($deletedInstallmentIds)) {
                $pdc->installments()->whereIn('id', $deletedInstallmentIds)->delete();
            }

            // Update PDC status based on installments
            if ($pdc->status !== 'Failed') {
                if ($overdue) {
                    $pdc->status = 'Overdue';
                } elseif ($pending) {
                    $pdc->status = 'Pending';
                } else {
                    $pdc->status = 'Complete';
                }
            }
            $pdc->save();
        }

        $this->updateData();
        return redirect()->back()->with('success', 'Payment updated successfully.');
    }

    public function destroy($id)
    {
        $agent = Pdc::find($id);

        if (!$agent) {
            return redirect()->back()->with('error', 'Payment not found or already deleted!');
        }

        $agent->delete();
        return redirect()->back()->with('success', 'Payment deleted successfully!');
    }


    // public function followupPdc(Request $request, $id)
    // {

    //     $item = Pdc::with('followups')->findOrFail($id);
    //     $followupCount = $item->followups->count();
    //     $followup = new PdcFollowup();
    //     $followup->pdc_id = $item->id;
    //     $followup->followup_date = $request->followup_date;
    //     $followup->comments = $request->comments;
    //     $followup->status = 'Pending';
    //     $followup->original_due_date = $item->due_date ?? now();
    //     $followup->followup_count = $followupCount + 1;
    //     $followup->next_due_date = $request->followup_date;


    //     try {
    //         $followup->save();
    //         $followup->status = 'Overdue';
    //         $item->save();

    //         return back()->with('success', 'Follow-up Added Successfully!');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Error occurred: ' . $e->getMessage());
    //     }
    // }

    public function followupPdc(Request $request, $id)
    {
        $request->validate([
            'followup_date' => 'required|date',
            'comments' => 'required|string',
            'installment_id' => 'nullable|exists:pdc_installments,id',
        ]);

        $pdc = Pdc::with('followups')->findOrFail($id);

        $installment = null;
        if ($request->installment_id) {
            $installment = PdcInstallment::where('pdc_id', $id)->findOrFail($request->installment_id);
            $followupCount = $installment->followups()->count(); // Per-installment count
        } else {
            $followupCount = $pdc->followups->count(); // PDC-level count
        }

        $followup = new PdcFollowup();
        $followup->pdc_id = $pdc->id;
        $followup->pdc_installment_id = $request->installment_id;  // Fixed: Matches model fillable and migration
        $followup->followup_date = $request->followup_date;
        $followup->comments = $request->comments;
        $followup->status = 'pending';
        $followup->original_due_date = $installment ? $installment->due_date : $pdc->due_date;
        $followup->followup_count = $followupCount + 1;
        $followup->next_due_date = $request->followup_date;  // Now fillable
        $followup->save();

        if ($installment) {
            $installment->status = 'Overdue';
            $installment->due_date = $request->followup_date; // extend due date
            $installment->save();

            // Optionally check if all installments are complete
            $pdc->status = $pdc->installments()->whereNotIn('status', ['Complete'])->exists()
                ? 'Overdue'
                : 'Complete';
        } else {
            $pdc->status = 'Overdue';
            $pdc->due_date = $request->followup_date;
        }

        $pdc->save();

        return back()->with('success', 'PDC Follow-up Added Successfully!');
    }


    public function updatePdcTransaction(Request $request, $id)
    {
        $pdc = Pdc::findOrFail($id);
        $oldAmount = $pdc->amount;

        $pdc->update([
            'amount' => $request->amount,
            'status' => $request->status
        ]);

        Transactions::create([
            'pc_id' => $pdc->id,
            'type' => 'pdc',
            'installment_id' => $request->installment_id ?? null,
            'client_id' => $pdc->client_id,
            'agent_id' => $pdc->agent_id,
            'pay_amount' => $request->amount - $oldAmount,
            'login_uid' => Auth::id(),
            'remarks' => "PDC updated. Old Amount: $oldAmount, New Amount: {$request->amount}"
        ]);

        return response()->json(['message' => 'PDC updated successfully']);
    }

    public function updateData()
    {
        $pdcs = Pdc::with(['installments', 'failedInstallments'])->get(); // Preload for efficiency

        foreach ($pdcs as $pdc) {
            $pending = false;
            $overdue = false;

            foreach ($pdc->installments as $installment) {
                $dueDate = Carbon::parse($installment->due_date);
                $today = Carbon::today();

                if ($installment->status !== 'Complete') {
                    if ($dueDate->lt($today)) {
                        $installment->status = 'Overdue';
                        $installment->save();
                        $overdue = true;
                    } else {
                        $pending = true;
                    }
                }
            }

            if ($pdc->installments->isEmpty() && $pdc->failedInstallments->count() > 0) {
                $pdc->status = 'Failed';
            } elseif ($overdue) {
                $pdc->status = 'Overdue';
            } elseif ($pending) {
                $pdc->status = 'Pending';
            } else {
                $pdc->status = 'Complete';
            }

            $pdc->save();
        }
    }

    public function failPdc($id)
    {
        $pdc = Pdc::with('installments')->findOrFail($id);

        $hasPaidInstallments = $pdc->installments->where('data1', '>', 0)->count() > 0; // Assuming 'data1' is the paid amount field

        if ($hasPaidInstallments) {
            return redirect()->back()->with('error', 'Cannot fail PDC: Some installments have been paid.');
        }

        DB::transaction(function () use ($pdc) {
            foreach ($pdc->installments as $installment) {
                FailedPdcInstallment::create([
                    'pdc_id' => $pdc->id,
                    'amount' => $installment->amount,
                    'chequeno' => $installment->chequeno,
                    'bankName' => $installment->bankName,
                    'due_date' => $installment->due_date,
                    'status' => 'Failed',
                ]);
                $installment->delete();
            }

            $pdc->status = 'Failed';
            $pdc->commitment_amount = 0; // Reset amount
            $pdc->save();
        });

        return redirect()->back()->with('success', 'PDC marked as failed successfully.');
    }

    public function failPdcInstallment(Request $request, $installmentId)
    {
        $installment = PdcInstallment::findOrFail($installmentId);

        if ($installment->data1 > 0) { // Assuming 'data1' is the paid amount field
            return redirect()->back()->with('error', 'Cannot fail installment: It has been partially or fully paid.');
        }

        DB::transaction(function () use ($installment) {
            // Move to failed_pdc_installments
            FailedPdcInstallment::create([
                'pdc_id' => $installment->pdc_id,
                'amount' => $installment->amount,
                'chequeno' => $installment->chequeno,
                'bankName' => $installment->bankName,
                'due_date' => $installment->due_date,
                'status' => 'Failed',
            ]);

            // Delete the original installment
            $installment->delete();

            // Update PDC totals
            $pdc = Pdc::find($installment->pdc_id);
            $totalAmount = $pdc->activeInstallments()->sum('amount');
            $pdc->commitment_amount = $totalAmount;
            $pdc->save();

            // Update PDC status
            if ($pdc->status !== 'Failed') {
                $hasPending = $pdc->activeInstallments()->where('status', 'Pending')->exists();
                $hasOverdue = $pdc->activeInstallments()->where('status', 'Overdue')->exists();
                $pdc->status = $hasOverdue ? 'Overdue' : ($hasPending ? 'Pending' : 'Complete');
                $pdc->save();
            }
        });

        return redirect()->back()->with('success', 'Installment marked as failed successfully.');
    }
}
