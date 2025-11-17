@extends('layouts.app', ['page' => 'View Commitment'])


@section('content')
<div class="page-content">
    <div class="card border-0">
        <!-- Header -->
        <div class="card-header text-start">
            <h3 class="mb-0 p-3 fw-bold">Commitment Information</h3>
        </div>

        <!-- Commitment Information -->
        <div class="card-body">
            <div class="card  border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row gy-4 gx-5">
                        <!-- gy adds vertical spacing, gx for horizontal -->

                        <!-- Party & Salesman -->
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Party Name</h6>
                            <p class="fw-semibold text-dark">{{ $item->client->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Salesman</h6>
                            <p class="fw-semibold text-dark">{{ $item->agent->name ?? 'N/A' }}</p>
                        </div>

                        <!-- Amounts -->
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Total Amount</h6>
                            <p class="fw-semibold">Rs. {{ number_format($item->total_amount, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Received Amount</h6>
                            @php
                            $totalPaidAmount = $item->transactions->where('pc_id', $item->id)->sum('pay_amount');
                            @endphp
                            <p class="fw-semibold">Rs. {{ number_format($totalPaidAmount, 2) }}</p>
                        </div>

                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Pending Amount</h6>
                            @php
                            $totalPendingAmount = $item->total_amount - $totalPaidAmount;
                            @endphp
                            <p class="fw-semibold">Rs. {{ number_format($totalPendingAmount, 2) }}</p>
                        </div>

                        <!-- Department -->
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Department</h6>
                            <p class="fw-semibold text-dark">{{ $item->department()->first() ?
                                $item->department()->first()->name : 'N/A' }}</p>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Status</h6>
                            <p class="fw-bold
                            {{ $item->status === 'Complete' ? 'text-success' :
                               ($item->status === 'Overdue' ? 'text-danger' :
                               ($item->status === 'Failed' ? 'text-secondary' : 'text-warning')) }}">
                                {{ ucfirst($item->status) }}
                            </p>
                        </div>

                        <!-- Entry Date -->
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Entry Date</h6>
                            <p class="fw-semibold text-dark">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}
                            </p>
                        </div>

                        <!-- Commitment Details -->
                        <div class="col-md-12">
                            <h6 class="text-muted">Commitment Details</h6>
                            @php
                            $words = explode(' ', $item->remarks);
                            $isLong = count($words) > 5;
                            $preview = implode(' ', array_slice($words, 0, 5));
                            @endphp

                            @if($isLong)
                            <p id="shortText{{ $item->id }}" class="text-dark lh-lg" data-bs-toggle="tooltip"
                                title="{{ $item->remarks }}">
                                {{ $preview }}...
                                <a href="javascript:void(0);" class="text-primary fw-semibold"
                                    onclick="showFullText({{ $item->id }})">More</a>
                            </p>
                            <p id="fullText{{ $item->id }}" class="text-dark lh-lg" style="display: none;">
                                {{ $item->remarks }}
                                <a href="javascript:void(0);" class="text-danger fw-semibold"
                                    onclick="hideFullText({{ $item->id }})">Show
                                    Less</a>
                            </p>
                            @else
                            <p class="text-dark lh-lg">{{ $item->remarks }}</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            <!-- Installments Table -->
            <div class="mt-6">
                <!-- Header -->
                <div class="text-start mb-4">
                    <h3 class="fw-bold">
                        Installments Overview
                    </h3>
                    <p class="text-muted">Track, manage, and update your commitments with ease</p>
                </div>

                <!-- Table -->
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gradient bg-primary text-white">
                            <tr>
                                <th class="text-center">Entry Date</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Commitment Age</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($item->activeInstallments as $installment)
                            <tr class="table-row-hover text-center">
                                <!-- Entry Date -->
                                <td class="text-center fw-semibold">
                                    {{ $installment->created_at->format('Y-m-d') }}
                                </td>

                                <!-- Amount -->
                                <td class="text-center">
                                    @if($installment->paid_amount > 0)
                                    <del class="text-muted">
                                        {{ number_format($installment->amount, 2) }}
                                    </del><br>
                                    <span class="fw-bold text-success">
                                        {{ number_format($installment->amount - $installment->paid_amount, 2) }}
                                    </span>
                                    @else
                                    <span class="fw-bold text-dark">
                                        {{ number_format($installment->amount, 2) }}
                                    </span>
                                    @endif
                                </td>

                                <!-- Due Date -->
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d') }}
                                </td>

                                <!-- Status -->
                                <td class="text-center">
                                    <span class="fw-bold
                                    {{ $installment->status === 'Complete' ? 'text-success' :
                                       ($installment->status === 'Overdue' ? 'text-danger' : 'text-warning') }}">
                                        {{ ucfirst($installment->status) }}
                                    </span>
                                </td>

                                <!-- Commitment Age -->
                                <td class="text-center">
                                    @php
                                    $dueDate = \Carbon\Carbon::parse($installment->due_date);
                                    $now = \Carbon\Carbon::today();
                                    $daysExceeded = $now->diffInDays($dueDate, false);
                                    @endphp

                                    @if($installment->status === 'Complete')
                                    <span class="text-success fw-semibold">
                                        ✔ Completed
                                    </span>
                                    @elseif($daysExceeded < 0) <span class="text-danger fw-semibold">
                                        Overdue by {{ abs($daysExceeded) }} days
                                        </span>
                                        @else
                                        <span class="text-primary fw-semibold">
                                            {{ $daysExceeded }} days left
                                        </span>
                                        @endif
                                </td>

                                <!-- Action -->
                                <td class="text-center">
                                    @if(in_array($installment->status, ['Pending', 'Overdue']))
                                    <button class="btn btn-success px-2 " data-bs-toggle="modal"
                                        data-bs-target="#payModal{{ $installment->id }}">
                                        <i class="fas fa-hand-holding-usd me-1 text-white"></i> Pay
                                    </button>
                                    @else
                                    <span class="badge bg-success px-3 py-2 text-white">Paid</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    🚫 No Installments found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Extra Styling -->
            <style>
                .table-row-hover:hover {
                    background-color: #f8f9fa;
                    transition: 0.2s ease-in-out;
                }
            </style>

            <!-- Failed Installments History Table -->
            @if($item->failedInstallments->count() > 0)
            <div style="margin-top: 30px;" class="my-4">
                <!-- Header -->
                <div class="text-start mb-4">
                    <h2 class="fw-bold">
                        <i class="fas fa-exclamation-circle me-2"></i> Failed Installments History
                    </h2>
                    <p class="text-muted">Detailed record of all failed installments for this commitment</p>
                </div>

                <!-- Table -->
                <div class="card shadow border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-danger text-white">
                                    <tr class="thead-dark">
                                        <th class="text-center">Entry Date</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->failedInstallments as $failedInstallment)
                                    <tr class="table-light">
                                        <td class="text-center fw-semibold">
                                            {{ $failedInstallment->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="text-center fw-bold text-danger">
                                            Rs. {{ number_format($failedInstallment->amount, 2) }}
                                        </td>
                                        <td class="text-center text-secondary">
                                            {{ \Carbon\Carbon::parse($failedInstallment->due_date)->format('Y-m-d') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-3 py-2 bg-gradient bg-danger text-white">
                                                <i class="fas fa-times-circle me-1 text-white"></i> {{
                                                ucfirst($failedInstallment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Transactions Table -->
            <div class="mt-4">
                <div class="text-start mb-4">
                    <h2 class="fw-bold">Transactions</h2>
                    <p class="text-muted">Commitment-related payments overview</p>
                </div>

                <div class="table-responsive rounded">
                    <table class="table table-striped table-hover align-middle text-center mb-0">
                        <thead class="bg-primary text-white">
                            <tr class="thead-dark">
                                <th class="text-center">Party</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Salesman</th>
                                <th class="text-center">Remarks</th>
                                <th class="text-center">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\Transactions::where('client_id', $item->client_id)
                            ->where('agent_id', $item->agent_id)
                            ->where('pc_id', $item->id)
                            ->where('type', 'commitment')
                            ->get() as $transaction)
                            <tr class="text-center">
                                <td class="fw-semibold text-dark">
                                    {{ $transaction->client->name ?? 'N/A' }}
                                </td>
                                <td class="fw-bold">
                                    Rs. {{ number_format($transaction->pay_amount, 2) }}
                                </td>
                                <td>
                                    <span class="text-center fw-semibold">
                                        {{ $transaction->agent->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center fw-semibold">
                                    {{ $transaction->remarks ?? '—' }}
                                </td>
                                <td class="text-center fw-semibold">
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
            <a href="{{ route('commitments.index') }}" class="btn btn-danger">Back</a>
        </div>
    </div>
</div>

<!-- Payment Modals -->
@foreach($item->activeInstallments as $installment)
<div class="modal fade" id="payModal{{ $installment->id }}" tabindex="-1"
    aria-labelledby="payModalLabel{{ $installment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header d-flex justify-content-between align-items-center gap-3">
                <h5 class="modal-title" id="payModalLabel{{ $installment->id }}">
                    <i class="fas fa-wallet me-2"></i> Partial Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="{{ route('installments.pay', $installment->id) }}" method="POST">
                @csrf
                <input type="hidden" name="installment_id" value="{{ $installment->id }}">
                <input type="hidden" name="pc_id" value="{{ $item->id }}">
                <div class="modal-body text-start p-4">
                    <div class="form-group mb-4">
                        <label for="pay_amount_{{ $installment->id }}" class="fw-bold">
                            <i class="fas fa-money-bill-wave me-1"></i> Amount to Pay
                        </label>
                        <input type="number" class="form-control rounded" id="pay_amount_{{ $installment->id }}"
                            name="pay_amount" placeholder="Enter Amount" min="1"
                            max="{{ $installment->amount - $installment->paid_amount }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="remarks_{{ $installment->id }}" class="fw-bold">
                            <i class="fas fa-comment me-1"></i> Remarks
                        </label>
                        <textarea class="form-control rounded" id="remarks_{{ $installment->id }}" name="remarks"
                            placeholder="Enter Remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fas fa-check me-1"></i> Confirm Payment
                    </button>
                    <button type="button" class="btn btn-danger fw-bold" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

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
