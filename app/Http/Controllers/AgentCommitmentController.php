<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Commitment;
use Illuminate\Http\Request;

class AgentCommitmentController extends Controller
{

    public function super($agentId)
    {
        // Fetch agent details
        $agent = Agent::findOrFail($agentId);

        // Get all commitments by this agent
        $commitments = Commitment::where('agent_id', $agentId)->get();

        // Initialize counters
        $commitmentCount = $commitments->count();
        $dueDateUpdateCount = 0;

        // Count due date updates per commitment
        foreach ($commitments as $commitment) {
            $updates = Commitment::where('id', $commitment->id)
                ->orderBy('updated_at', 'asc')
                ->pluck('due_date');

            $previousDate = null;
            foreach ($updates as $date) {
                if ($previousDate !== null && $previousDate != $date) {
                    $dueDateUpdateCount++;
                }
                $previousDate = $date;
            }
        }

        return view('dashboard.superDashboard', compact('agent', 'commitmentCount', 'dueDateUpdateCount'));
    }
}
