<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Commitment;
use App\Models\Pdc;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $recentCommitments = Commitment::where('created_by', Auth::id())->latest()->take(5)->get();
        $query = Commitment::query()->latest();
        $commitments = $query->with('agent')->latest()->get();

        // Use getAccessibleData for proper data scoping
        $agentQuery = $this->getAccessibleData(Agent::class);

        // Apply filters
        if (request()->has('filter_salesman') && request('filter_salesman')) {
            $agentQuery->where('name', 'like', '%' . request('filter_salesman') . '%');
        }

        if (request()->has('contact') && request('contact')) {
            $agentQuery->where('contact', 'like', '%' . request('contact') . '%');
        }

        if (request()->has('address') && request('address')) {
            $agentQuery->where('address', 'like', '%' . request('address') . '%');
        }

        if (request()->has('area') && request('area')) {
            $agentQuery->where('area', 'like', '%' . request('area') . '%');
        }

        if (request()->has('department') && request('department')) {
            $agentQuery->where('department_id', request('department'));
        }

        $agents = $agentQuery->latest()->paginate(10);
        $notifications = $this->getNotifications();

        // Get departments for filter dropdown
        $departments = \App\Models\Department::active()->get();

        // Use unified view for all roles
        return view('agents.superAgents', compact(
            'agents',
            'notifications',
            'recentCommitments',
            'commitments',
            'departments'
        ));
    }

    public function show($id)
    {
        $agent = Agent::with([
            'commitments.installments',
            'commitments.client',
            'commitments.transactions',
            'commitments.followups',
            'commitments.failedInstallments',
            'pdcs.installments',
            'pdcs.client',
            'pdcs.transactions',
            'pdcs.followups',
            'pdcs.failedInstallments'
        ])->findOrFail($id);

        $notifications = $this->getNotifications();

        // Get ALL commitments
        $allCommitments = $agent->commitments()
            ->with(['client', 'installments', 'transactions', 'followups', 'failedInstallments'])
            ->latest()
            ->get();

        // Get ALL PDCs
        $allPdcs = $agent->pdcs()
            ->with(['client', 'installments', 'transactions', 'followups', 'failedInstallments'])
            ->latest()
            ->get();

        // Filtered data for different sections
        $activeCommitments = $allCommitments->whereNotIn('status', ['Complete', 'Failed']);
        $activePdcs = $allPdcs->whereNotIn('status', ['Complete', 'Failed']);
        $historyCommitments = $allCommitments->whereIn('status', ['Complete', 'Failed']);
        $historyPdcs = $allPdcs->whereIn('status', ['Complete', 'Failed']);

        return view('agents.agent-show', compact(
            'agent',
            'notifications',
            'allCommitments',
            'allPdcs',
            'activeCommitments',
            'activePdcs',
            'historyCommitments',
            'historyPdcs'
        ));
    }

    public function showElectrical($id)
    {
        $agent = Agent::with([
            'commitments.installments',
            'commitments.client',
            'commitments.transactions',
            'commitments.followups',
            'commitments.failedInstallments',
            'pdcs.installments',
            'pdcs.client',
            'pdcs.transactions',
            'pdcs.followups',
            'pdcs.failedInstallments'
        ])->findOrFail($id);

        $notifications = $this->getNotifications();

        // Get ALL commitments
        $allCommitments = $agent->commitments()
            ->with(['client', 'installments', 'transactions', 'followups', 'failedInstallments'])
            ->latest()
            ->get();

        // Get ALL PDCs
        $allPdcs = $agent->pdcs()
            ->with(['client', 'installments', 'transactions', 'followups', 'failedInstallments'])
            ->latest()
            ->get();

        // Filtered data for different sections
        $activeCommitments = $allCommitments->whereNotIn('status', ['Complete', 'Failed']);
        $activePdcs = $allPdcs->whereNotIn('status', ['Complete', 'Failed']);
        $historyCommitments = $allCommitments->whereIn('status', ['Complete', 'Failed']);
        $historyPdcs = $allPdcs->whereIn('status', ['Complete', 'Failed']);

        return view('agents.agent-showElectrical', compact(
            'agent',
            'notifications',
            'allCommitments',
            'allPdcs',
            'activeCommitments',
            'activePdcs',
            'historyCommitments',
            'historyPdcs'
        ));
    }

    public function showElectronics($id)
    {
        $agent = Agent::with([
            'commitments.installments',
            'commitments.client',
            'commitments.transactions',
            'commitments.followups',
            'commitments.failedInstallments',
            'pdcs.installments',
            'pdcs.client',
            'pdcs.transactions',
            'pdcs.followups',
            'pdcs.failedInstallments'
        ])->findOrFail($id);

        $notifications = $this->getNotifications();

        // Get ALL commitments
        $allCommitments = $agent->commitments()
            ->with(['client', 'installments', 'transactions', 'followups', 'failedInstallments'])
            ->latest()
            ->get();

        // Get ALL PDCs
        $allPdcs = $agent->pdcs()
            ->with(['client', 'installments', 'transactions', 'followups', 'failedInstallments'])
            ->latest()
            ->get();

        // Filtered data for different sections
        $activeCommitments = $allCommitments->whereNotIn('status', ['Complete', 'Failed']);
        $activePdcs = $allPdcs->whereNotIn('status', ['Complete', 'Failed']);
        $historyCommitments = $allCommitments->whereIn('status', ['Complete', 'Failed']);
        $historyPdcs = $allPdcs->whereIn('status', ['Complete', 'Failed']);

        return view('agents.agent-showElectronics', compact(
            'agent',
            'notifications',
            'allCommitments',
            'allPdcs',
            'activeCommitments',
            'activePdcs',
            'historyCommitments',
            'historyPdcs'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|numeric',
            'department_id' => 'required|exists:departments,id',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'area' => 'nullable|string',
        ]);

        try {
            $agent = new Agent();
            $agent->name = $request->name;
            $agent->contact = $request->contact;
            $agent->email = $request->email;
            $agent->address = $request->address;
            $agent->area = $request->area;
            $agent->department_id = $request->department_id; // Use department_id instead of department
            $agent->created_by = Auth::id();
            $agent->save();

            return redirect()->back()->with('success', 'Agent created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating agent: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|numeric',
            'department_id' => 'required|exists:departments,id',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'area' => 'nullable|string',
        ]);

        $agent = Agent::findOrFail($id);

        // Ensure the user can only update their own agents or has permission
        if (Auth::id() !== $agent->created_by && !Auth::user()->can('edit all agents')) {
            return redirect()->back()->with('error', 'Unauthorized to update this agent!');
        }

        try {
            $agent->name = $request->name;
            $agent->contact = $request->contact;
            $agent->email = $request->email;
            $agent->address = $request->address;
            $agent->area = $request->area;
            $agent->department_id = $request->department_id; // Use department_id
            $agent->save();

            return redirect()->back()->with('success', 'Agent updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating agent: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $agent = Agent::find($id); // Use find() instead of findOrFail()

        if (!$agent) {
            return redirect()->back()->with('error', 'Agent not found or already deleted!');
        }

        $agent->delete();
        return redirect()->back()->with('success', 'Agent deleted successfully!');
    }
}
