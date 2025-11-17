<?php

namespace App\Http\Controllers;

use App\Models\Commitment;
use App\Models\Transactions;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getTransactions($agentId)
    {
        $transactions = Transactions::with(['client', 'agent', 'user'])
            ->where('agent_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->limit(10) // Fetch last 10 transactions
            ->get();

        return response()->json($transactions);
    }
}
