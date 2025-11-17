<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\CheckMenu;
use App\Models\ChequeMenu;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChequeMenuController extends Controller
{


    public function index(Request $request)
    {
        $user = Auth::user();

        // Use getAccessibleData for proper data scoping with potential filters
        $chequeLocationsQuery = $this->getAccessibleData(ChequeMenu::class)
            ->with(['client', 'agent'])
            ->latest();

        // Apply filters if needed (you can add filter parameters to the method)
        if ($request->has('filter_client') && $request->filter_client) {
            $chequeLocationsQuery->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->filter_client . '%');
            });
        }

        if ($request->has('filter_agent') && $request->filter_agent) {
            $chequeLocationsQuery->whereHas('agent', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->filter_agent . '%');
            });
        }

        $chequeLocations = $chequeLocationsQuery->paginate(10);

        // Get clients and agents with proper data scoping
        $clients = $this->getAccessibleData(Client::class)->orderBy('name')->get();
        $agents = $this->getAccessibleData(Agent::class)->orderBy('name')->get();

        $notifications = $this->getNotifications();

        // Use permission-based view selection with Spatie roles
        $roleName = $user->roles->first()->name;

        // For even better practice, consider using a single view with role-based components
        $view = 'chequeLocation.index';

        return view($view, compact('chequeLocations', 'clients', 'agents', 'notifications', 'request'));
    }
    public function create()
    {
        $clients = Client::all(); // Assuming a Client model
        $agents = Agent::all();   // Assuming an Agent model
        $notifications = $this->getNotifications();

        return view('chequeLocation.addCheque', compact('clients', 'notifications', 'agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'cheque_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'agent_id' => 'required|exists:agents,id',
            'location' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:255',
        ]);

        ChequeMenu::create($request->all());

        return redirect()->back()->with('success', 'Cheque location stored successfully!');
    }

    public function edit($id)
    {
        $cheque = ChequeMenu::with(['client', 'agent'])->findOrFail($id);
        // Optionally, fetch clients and agents for dropdowns if they can be changed
        $clients = \App\Models\Client::all(); // Adjust model name as needed
        $agents = \App\Models\Agent::all();   // Adjust model name as needed
        $notifications = $this->getNotifications();

        return view('chequeLocation.editCheque', compact('cheque', 'notifications', 'clients', 'agents'));
    }

    public function update(Request $request, $id)
    {
        try {
            $cheque = ChequeMenu::findOrFail($id);

            $validated = $request->validate([
                'cheque_no' => 'required|string|max:255',
                'client_id' => 'required|exists:clients,id',
                'agent_id' => 'required|exists:agents,id',
                'amount' => 'required|numeric|min:0',
                'location' => 'required|string|',
                'status' => 'nullable|',
            ]);

            $cheque->update($validated);

            return redirect()->route('cheque.locations.index')->with('success', 'Cheque updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating cheque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update cheque. Please try again.');
        }
    }

    // Delete a cheque location
    public function destroy($id)
    {
        try {
            $cheque = ChequeMenu::findOrFail($id);
            $cheque->delete();

            return redirect()->route('cheque.locations.index')->with('success', 'Cheque deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting cheque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete cheque. Please try again.');
        }
    }
}
