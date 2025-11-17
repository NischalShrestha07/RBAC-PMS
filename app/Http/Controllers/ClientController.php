<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Department;
use App\Models\Pdc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['commitments.installments', 'pdcs.installments', 'commitments.agent', 'pdcs.agent']);

        // Apply filters
        if ($request->filled('filter_client') || $request->filled('name')) {
            $clientName = $request->filter_client ?? $request->name;
            $query->where('name', 'LIKE', '%' . $clientName . '%');
        }

        if ($request->has('address') && !empty($request->address)) {
            $query->where('address', 'LIKE', '%' . $request->address . '%');
        }

        if ($request->has('salesman') && !empty($request->salesman)) {
            $salesman = $request->input('salesman');
            $query->where(function ($q) use ($salesman) {
                $q->whereHas('commitments.agent', function ($q2) use ($salesman) {
                    $q2->where('name', $salesman);
                })->orWhereHas('pdcs.agent', function ($q2) use ($salesman) {
                    $q2->where('name', $salesman);
                });
            });
        }

        if (request()->has('department') && request('department')) {
            $query->where('department_id', request('department'));
        }

        $clients = $query->paginate(10);

        $allClients = Client::orderBy('name')->pluck('name');

        $agents = Agent::all()->pluck('name')->unique();
        $notifications = $this->getNotifications();
        $departments = Department::where('is_active', true)->get();

        // Calculate active amounts for each client
        foreach ($clients as $client) {
            $activeCommitmentAmount = 0;
            foreach ($client->commitments as $commitment) {
                foreach ($commitment->installments as $installment) {
                    if (!in_array($installment->status, ['Complete', 'Failed'])) {
                        $activeCommitmentAmount += ($installment->amount - $installment->paid_amount);
                    }
                }
            }

            $activePdcAmount = 0;
            foreach ($client->pdcs as $pdc) {
                foreach ($pdc->installments as $installment) {
                    if (!in_array($installment->status, ['Complete', 'Failed'])) {
                        $activePdcAmount += ($installment->amount - $installment->paid_amount);
                    }
                }
            }

            $client->active_commitment_amount = $activeCommitmentAmount;
            $client->active_pdc_amount = $activePdcAmount;
        }

        return view('clients.index', compact('clients', 'notifications', 'agents', 'allClients', 'departments'));
    }

    public function indexElectrical(Request $request)
    {
        $department = Auth::user()->role;

        $query = Client::with([
            'commitments.agent',
            'pdcs.agent',
            'commitments.installments',
            'pdcs.installments'
        ])
            ->where(function ($q) use ($department) {
                $q->whereHas('commitments.agent', fn($q2) => $q2->where('department', $department))
                    ->orWhereHas('pdcs.agent', fn($q2) => $q2->where('department', $department));
            });

        // Apply search filters
        if ($request->filled('filter_client') || $request->filled('name')) {
            $clientName = $request->filter_client ?? $request->name;
            $query->where('name', 'LIKE', '%' . $clientName . '%');
        }

        if ($request->filled('address')) {
            $query->where('address', 'LIKE', '%' . $request->address . '%');
        }

        if ($request->filled('salesman')) {
            $salesman = $request->salesman;
            $query->where(function ($q) use ($salesman) {
                $q->whereHas('commitments.agent', fn($q2) => $q2->where('name', $salesman))
                    ->orWhereHas('pdcs.agent', fn($q2) => $q2->where('name', $salesman));
            });
        }

        $clients = $query->paginate(10);

        $allClients = Client::orderBy('name')->where('department', $department)->pluck('name');

        $agents = Agent::where('department', $department)->pluck('name')->unique();
        $notifications = $this->getNotifications();

        foreach ($clients as $client) {
            $activeCommitmentAmount = 0;
            foreach ($client->commitments as $commitment) {
                foreach ($commitment->installments as $installment) {
                    if (!in_array($installment->status, ['Complete', 'Failed'])) {
                        $activeCommitmentAmount += ($installment->amount - $installment->paid_amount);
                    }
                }
            }

            $activePdcAmount = 0;
            foreach ($client->pdcs as $pdc) {
                foreach ($pdc->installments as $installment) {
                    if (!in_array($installment->status, ['Complete', 'Failed'])) {
                        $activePdcAmount += ($installment->amount - $installment->paid_amount);
                    }
                }
            }

            $client->active_commitment_amount = $activeCommitmentAmount;
            $client->active_pdc_amount = $activePdcAmount;
        }

        return view('clients.indexElectrical', compact('clients', 'notifications', 'agents', 'allClients'));
    }

    public function indexElectronics(Request $request)
    {
        $department = Auth::user()->role;

        $query = Client::with([
            'commitments.agent',
            'pdcs.agent',
            'commitments.installments',
            'pdcs.installments'
        ])
            ->where(function ($q) use ($department) {
                $q->whereHas('commitments.agent', fn($q2) => $q2->where('department', $department))
                    ->orWhereHas('pdcs.agent', fn($q2) => $q2->where('department', $department));
            });

        // Apply search filters
        if ($request->filled('filter_client') || $request->filled('name')) {
            $clientName = $request->filter_client ?? $request->name;
            $query->where('name', 'LIKE', '%' . $clientName . '%');
        }

        if ($request->filled('address')) {
            $query->where('address', 'LIKE', '%' . $request->address . '%');
        }

        if ($request->filled('salesman')) {
            $salesman = $request->salesman;
            $query->where(function ($q) use ($salesman) {
                $q->whereHas('commitments.agent', fn($q2) => $q2->where('name', $salesman))
                    ->orWhereHas('pdcs.agent', fn($q2) => $q2->where('name', $salesman));
            });
        }

        $clients = $query->paginate(10);

        $allClients = Client::orderBy('name')->where('department', $department)->pluck('name');

        $agents = Agent::where('department', $department)->pluck('name')->unique();
        $notifications = $this->getNotifications();

        // Calculate active amounts
        foreach ($clients as $client) {
            $activeCommitmentAmount = 0;
            foreach ($client->commitments as $commitment) {
                foreach ($commitment->installments as $installment) {
                    if (!in_array($installment->status, ['Complete', 'Failed'])) {
                        $activeCommitmentAmount += ($installment->amount - $installment->paid_amount);
                    }
                }
            }

            $activePdcAmount = 0;
            foreach ($client->pdcs as $pdc) {
                foreach ($pdc->installments as $installment) {
                    if (!in_array($installment->status, ['Complete', 'Failed'])) {
                        $activePdcAmount += ($installment->amount - $installment->paid_amount);
                    }
                }
            }

            $client->active_commitment_amount = $activeCommitmentAmount;
            $client->active_pdc_amount = $activePdcAmount;
        }

        return view('clients.indexElectronics', compact('clients', 'notifications', 'agents', 'allClients'));
    }

    public function create()
    {
        $user = Auth::user();
        $notifications = $this->getNotifications();
        $departments = Department::where('is_active', true)->get();
        $agents = ($user->role === 'Super Admin')
            ? Agent::with('department')->get() // Load department relationship
            : Agent::with('department')->where('department', $user->role)->get();

        return view('clients.create', compact('notifications', 'agents', 'departments'));
    }

    public function getClientDetails(Client $client)
    {
        $client->load('agent');

        return response()->json([
            'success' => true,
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'agent_id' => $client->agent_id,
                'agent_name' => $client->agent ? $client->agent->name : null,
                'department' => $client->department,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            // 'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            // 'company' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'agent_id' => 'required|exists:agents,id',
        ]);

        $client = new Client();
        $client->name = $request->input('name');
        $client->address = $request->input('address');
        // $client->email = $request->input('email');
        $client->phone = $request->input('phone');
        // $client->company = $request->input('company');
        $client->department_id = $request->department_id;
        $client->agent_id = $request->agent_id;
        $client->save();

        return redirect()->route('clients.index')->with('success', 'Client created successfully!');
    }

    public function show(Client $client)
    {
        $clients = Client::all();
        $agents = Agent::all();
        $pdcs = Pdc::with('installments')->get();
        $client->load(['commitments.installments', 'commitments.followups', 'pdcHistory.installments', 'pdcHistory.followups']);
        $notifications = $this->getNotifications();

        return view('clients.show', compact('client', 'pdcs', 'notifications', 'clients', 'agents'));
    }

    public function showElectrical(Client $client)
    {
        $clients = Client::all();
        $agents = Agent::all();
        $pdcs = Pdc::with('installments')->get();
        $client->load(['commitments.installments', 'commitments.followups', 'pdcHistory.installments', 'pdcHistory.followups']);
        $notifications = $this->getNotifications();

        return view('clients.showElectrical', compact('client', 'pdcs', 'notifications', 'clients', 'agents'));
    }

    public function showElectronics(Client $client)
    {
        $clients = Client::all();
        $agents = Agent::all();
        $pdcs = Pdc::with('installments')->get();
        $client->load(['commitments.installments', 'commitments.followups', 'pdcHistory.installments', 'pdcHistory.followups']);
        $notifications = $this->getNotifications();

        return view('clients.showElectronics', compact('client', 'pdcs', 'notifications', 'clients', 'agents'));
    }

    public function edit(Client $client)
    {
        $notifications = $this->getNotifications();
        $departments = Department::where('is_active', true)->get();
        $user = Auth::user();
        $agents = ($user->role === 'Super Admin')
            ? Agent::with('department')->get() // Load department relationship
            : Agent::with('department')->where('department', $user->role)->get();
        return view('clients.edit', compact('client', 'notifications', 'agents', 'departments'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            // 'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            // 'company' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments:id',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        $client->update([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            // 'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            // 'company' => $request->input('company'),
            'department' => $request->input('department'),
            'department_id' => $request->department_id,
            'agent_id' => $request->agent_id,
        ]);

        $client->commitments()->update([
            'name' => $request->name,
        ]);


        return redirect()->route('clients.index')->with('success', 'Client updated successfully!');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully!');
    }
}
