@extends('layouts.electronicsApp', ['page' => 'View Commitment'])

@section('content')
<div class="page-content">
    <div class="card  border-0">
        <!-- Header -->
        <div class="card-header text-start py-3">
            <h3 class="card-title mb-0 fw-bold">Commitment Information</h3>
        </div>

        <!-- Commitment Information -->
        <div class="card-body">
            <div class="card border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">

                        <!-- Party & Salesman -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Party Name</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $item->client->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Salesman</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $item->agent->name ?? 'N/A' }}</p>
                        </div>

                        <!-- Amounts -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Total Amount</h6>
                            <p class="fw-bold fs-6">Rs. {{ number_format($item->total_amount, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Received Amount</h6>
                            @php
                            $totalPaidAmount = $item->transactions->where('pc_id', $item->id)->sum('pay_amount');
                            @endphp
                            <p class="fw-bold fs-6">Rs. {{ number_format($totalPaidAmount, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Pending Amount</h6>
                            @php
                            $totalPendingAmount = $item->total_amount - $totalPaidAmount;
                            @endphp
                            <p class="fw-bold fs-6">Rs. {{ number_format($totalPendingAmount, 2) }}</p>
                        </div>

                        <!-- Department -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Department</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $item->department }}</p>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Status</h6>
                            <p
                                class="fw-bold
                                {{ $item->status === 'Complete' ? 'text-success' : ($item->status === 'Overdue' ? 'text-danger' : ($item->status === 'Failed' ? 'text-secondary' : 'text-warning')) }}">
                                {{ ucfirst($item->status) }}
                            </p>
                        </div>

                        <!-- Entry Date -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Entry Date</h6>
                            <p class="fw-bold fs-6 text-dark">{{
                                \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</p>
                        </div>

                        <!-- Commitment Details -->
                        <div class="col-md-12">
                            <h6 class="text-muted">Commitment Details</h6>
                            @php
                            $words = explode(' ', $item->remarks);
                            $preview = implode(' ', array_slice($words, 0, 5));
                            $showMore = count($words) > 5;
                            @endphp
                            <p id="shortText{{ $item->id }}" class="text-dark" data-bs-toggle="tooltip"
                                title="{{ $item->remarks }}">
                                {{ $preview }}{{ $showMore ? '...' : '' }}
                                @if ($showMore)
                                <a href="javascript:void(0);" class="text-primary fw-semibold"
                                    onclick="showFullText({{ $item->id }})">More</a>
                                @endif
                            </p>
                            <p id="fullText{{ $item->id }}" class="text-dark" style="display: none;">
                                {{ $item->remarks }}
                                @if ($showMore)
                                <a href="javascript:void(0);" class="text-danger fw-semibold"
                                    onclick="hideFullText({{ $item->id }})">Show
                                    Less</a>
                                @endif
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Installments Table -->
            <div class="mt-4">
                <div class="text-start mb-4">
                    <h3 class="fw-bold">Installments</h3>
                    <p class="text-muted">Track and manage all installment details easily</p>
                </div>

                <div class="table-responsive rounded">
                    <table class="table align-middle table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">Entry Date</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Commitment Age</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->activeInstallments as $installment)
                            <tr class="text-center">
                                <td class="text-center fw-medium">
                                    {{ $installment->created_at->format('Y-m-d') }}
                                </td>
                                <td class="text-center">
                                    @if($installment->paid_amount > 0)
                                    <del class="text-muted text-decoration-line-through">
                                        {{ number_format($installment->amount, 2) }}
                                    </del><br>
                                    <span class="fw-bold text-success">
                                        {{ number_format($installment->amount - $installment->paid_amount, 2) }}
                                    </span>
                                    @else
                                    <span class="fw-bold">{{ number_format($installment->amount, 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">
                                    {{ \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d') }}
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold
                                        {{ $installment->status === 'Complete' ? 'text-success' :
                                           ($installment->status === 'Overdue' ? 'text-danger' : 'text-warning') }}">
                                        {{ $installment->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                    $dueDate = \Carbon\Carbon::parse($installment->due_date);
                                    $now = \Carbon\Carbon::today();
                                    $daysExceeded = $now->diffInDays($dueDate, false);
                                    @endphp
                                    @if($installment->status === 'Complete')
                                    <span class="text-success fw-semibold">✔ Completed</span>
                                    @elseif($daysExceeded < 0) <span class="text-danger fw-semibold">
                                        Overdue by {{ abs($daysExceeded) }} days
                                        </span>
                                        @else
                                        <span class="text-dark fw-bold">
                                            {{ $daysExceeded }} days left
                                        </span>
                                        @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Failed Installments History Table -->
            @if($item->failedInstallments->count() > 0)
            <div class="mt-4">
                <div class="text-start mb-4">
                    <h2 class="fw-bold">Failed Installments History</h2>
                    <p class="text-muted">List of failed installments for this commitment</p>
                </div>

                <div class="table-responsive rounded">
                    <table class="table align-middle table-hover">
                        <thead class="bg-dark text-white">
                            <tr class="text-center">
                                <th class="text-center">Entry Date</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->failedInstallments as $failedInstallment)
                            <tr class="text-center">
                                <td class="text-center fw-medium">
                                    {{ $failedInstallment->created_at->format('Y-m-d') }}
                                </td>
                                <td class="text-center fw-bold">
                                    Rs. {{ number_format($failedInstallment->amount, 2) }}
                                </td>
                                <td class="text-center text-secondary">
                                    {{ \Carbon\Carbon::parse($failedInstallment->due_date)->format('Y-m-d') }}
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-danger">
                                        {{ $failedInstallment->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Transactions Table -->
            <div class="mt-4">
                <div class="text-start mb-4">
                    <h2 class="fw-bold mt-5">Transactions</h2>
                    <p class="text-muted">All commitment-related payments are listed below</p>
                </div>

                <div class="table-responsive rounded">
                    <table class="table align-middle table-hover">
                        <thead class="bg-dark text-white">
                            <tr class="text-center">
                                <th class="text-center">Party</th>
                                <th class="text-center">Pay Amount</th>
                                <th class="text-center">Salesman</th>
                                <th class="text-center">Remarks</th>
                                <th class="text-center">Transaction Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\Transactions::where('client_id', $item->client_id)
                            ->where('agent_id', $item->agent_id)
                            ->where('pc_id', $item->id)
                            ->where('type', 'commitment')
                            ->get() as $transaction)
                            <tr class="text-center">
                                <td class="fw-bold text-dark text-center">
                                    {{ $transaction->client->name ?? 'N/A' }}
                                </td>
                                <td class="fw-bold text-success text-center">
                                    Rs. {{ number_format($transaction->pay_amount, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">
                                        {{ $transaction->agent->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $transaction->remarks ?? 'No Remarks' }}
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $transaction->created_at->format('Y-m-d') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    🚫 No transactions found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-end">
            <a href="{{ route('electronics.clients.show', $item->client->id) }}" class="btn btn-danger">Back</a>
        </div>
    </div>
</div>

<!-- JavaScript for Show/Hide Remarks -->
<script>
    function showFullText(id) {
        document.getElementById('shortText' + id).style.display = 'none';
        document.getElementById('fullText' + id).style.display = 'block';
    }

    function hideFullText(id) {
        document.getElementById('shortText' + id).style.display = 'block';
        document.getElementById('fullText' + id).style.display = 'none';
    }
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('customJs')
<script>
    $(function() {
        $("#CommitmentTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#CommitmentTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection