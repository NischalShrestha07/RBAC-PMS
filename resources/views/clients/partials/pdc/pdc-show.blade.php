@extends('layouts.superApp',['page' => 'View Pdcs'])

@section('customCss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection


@section('content')
<div class="page-content">
    <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header text-start py-3">
            <h3 class="card-title mb-0 fw-bold">Post Dated PDC Information</h3>
        </div>

        <div class="card-body">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body px-4 py-4">
                    <!-- Key Info Section -->
                    <div class="row g-4">

                        <!-- Party -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Party Name</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $pdc->client->name ?? 'N/A' }}</p>
                        </div>

                        <!-- PDC Amount -->
                        <div class="col-md-3">
                            <h6 class="text-muted">PDC Amount</h6>
                            <p class="fw-bold fs-6">Rs. {{ number_format($pdc->commitment_amount, 2) }}</p>
                        </div>

                        <!-- Received Amount -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Received Amount</h6>
                            @php
                            $totalPaidAmount = $pdc->transactions->where('pc_id', $pdc->id)->sum('pay_amount');
                            @endphp
                            <p class="fw-bold fs-6">Rs. {{ number_format($totalPaidAmount, 2) }}</p>
                        </div>

                        <!-- Pending Amount -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Pending Amount</h6>
                            @php
                            $totalPendingAmount = $pdc->commitment_amount - $totalPaidAmount;
                            @endphp
                            <p class="fw-bold fs-6">Rs. {{ number_format($totalPendingAmount, 2) }}</p>
                        </div>

                        <!-- Salesman -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Salesman</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $pdc->agent->name ?? 'No Agent Assigned' }}</p>
                        </div>

                        <!-- PDC Date -->
                        <div class="col-md-3">
                            <h6 class="text-muted">PDC Date</h6>
                            <p class="fw-bold fs-6 text-dark">
                                @php
                                $oldestIncompleteInstallment = $pdc->activeInstallments
                                ->filter(fn($i) => $i->status !== 'Complete')
                                ->sortBy('due_date')
                                ->first();
                                @endphp
                                {{ $oldestIncompleteInstallment->due_date ?? 'N/A' }}
                            </p>
                        </div>

                        <!-- Department -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Department</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $pdc->department }}</p>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Status</h6>
                            <span
                                class="fw-bold
                                {{ $pdc->status === 'Complete' ? 'text-success' : ($pdc->status === 'Overdue' ? 'text-danger' : ($pdc->status === 'Failed' ? 'text-secondary' : 'text-warning')) }}">
                                {{ ucfirst($pdc->status) }}
                            </span>
                        </div>

                        <!-- Entry Date -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Entry Date</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $pdc->created_at->format('Y-m-d') }}</p>
                        </div>

                        <!-- Pdc Details -->
                        <div class="col-md-9">
                            <h6 class="text-muted">Pdc Details</h6>
                            @php
                            $words = $pdc->remarks ? explode(' ', $pdc->remarks) : [];
                            $preview = $words ? implode(' ', array_slice($words, 0, 5)) : 'No remarks';
                            $showMore = count($words) > 5;
                            @endphp
                            <p id="shortText{{ $pdc->id }}" class="text-dark" data-bs-toggle="tooltip"
                                title="{{ $pdc->remarks ?? 'No remarks' }}">
                                {{ $preview }}{{ $showMore ? '...' : '' }}
                                @if ($showMore)
                                <a href="javascript:void(0);" class="text-primary fw-semibold"
                                    onclick="showFullText({{ $pdc->id }})">More</a>
                                @endif
                            </p>
                            <p id="fullText{{ $pdc->id }}" class="text-dark" style="display: none;">
                                {{ $pdc->remarks ?? 'No remarks' }}
                                @if ($showMore)
                                <a href="javascript:void(0);" class="text-danger fw-semibold"
                                    onclick="hideFullText({{ $pdc->id }})">Show
                                    Less</a>
                                @endif
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Installments -->
            <div class="mt-5">
                <div class="text-start mb-4">
                    <h3 class="fw-bold">Installments</h3>
                    <p class="text-muted">Track and manage all installment details easily</p>
                </div>
                <div class="table-responsive">
                    <table id="installmentsTable" class="table table-hover align-middle shadow-sm rounded-3">
                        <thead>
                            <tr>
                                <th class="text-center">Entry Date</th>
                                <th class="text-center">Cheque No</th>
                                <th class="text-center">Bank</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Commitment Age</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pdc->activeInstallments as $installment)
                            <tr class="text-center">
                                <td>{{ $installment->created_at->format('Y-m-d') }}</td>
                                <td>{{ $installment->chequeno }}</td>
                                <td>{{ $installment->bankName }}</td>
                                <td>
                                    @if($installment->data1 > 0)
                                    <del>{{ number_format($installment->amount, 2) }}</del><br>
                                    <strong class="text-success">{{ number_format($installment->amount -
                                        $installment->data1, 2)
                                        }}</strong>
                                    @else
                                    {{ number_format($installment->amount, 2) }}
                                    @endif
                                </td>
                                <td>{{ $installment->due_date }}</td>
                                <td class="text-center">
                                    <span
                                        class="fw-bold
                                        {{ $installment->status === 'Complete' ? 'text-success' : ($installment->status === 'Overdue' ? 'text-danger' : 'text-warning') }}">
                                        {{ $installment->status }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                    $dueDate = \Carbon\Carbon::parse($installment->due_date);
                                    $now = \Carbon\Carbon::today();
                                    $daysExceeded = $now->diffInDays($dueDate, false);
                                    @endphp
                                    @if($installment->status === 'Complete')
                                    <span class="text-success fw-semibold">Complete</span>
                                    @elseif($daysExceeded < 0) <span class="text-danger fw-semibold">Overdue by
                                        {{
                                        abs($daysExceeded) }} days</span>
                                        @else
                                        <span class="text-dark fw-semibold">{{ $daysExceeded }} days left</span>
                                        @endif
                                </td>
                                <td>
                                    @if(in_array($installment->status, ['Pending', 'Overdue']))
                                    <button type="button" class="btn btn-success text-white" title="Partial Payment"
                                        data-bs-toggle="modal" data-bs-target="#payModal{{ $installment->id }}"
                                        aria-label="Pay Installment">
                                        <i class="fas fa-hand-holding-usd me-1 text-white"></i>Pay
                                    </button>
                                    @else
                                    <span class="text-success fw-bold">Completed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    🚫 No Active Installments found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Failed Installments -->
            <div class="mt-5">
                <div class="text-start mb-4">
                    <h3 class="fw-bold">Failed Installments</h3>
                    <p class="text-muted">View all failed installment details for this PDC</p>
                </div>
                <div class="table-responsive">
                    <table id="failedInstallmentsTable" class="table table-hover align-middle shadow-sm rounded-3">
                        <thead>
                            <tr>
                                <th class="text-center">Entry Date</th>
                                <th class="text-center">Cheque No</th>
                                <th class="text-center">Bank</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\FailedPdcInstallment::where('pdc_id', $pdc->id)->get()
                            as $failedInstallment)
                            <tr class="text-center">
                                <td>{{ $failedInstallment->created_at->format('Y-m-d') }}</td>
                                <td>{{ $failedInstallment->chequeno ?? 'N/A' }}</td>
                                <td>{{ $failedInstallment->bankName ?? 'N/A' }}</td>
                                <td>{{ number_format($failedInstallment->amount, 2) }}</td>
                                <td>{{ $failedInstallment->due_date }}</td>
                                <td class="text-center">
                                    <span class="text-danger fw-bold">
                                        {{ $failedInstallment->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    🚫 No Failed Installments found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transactions -->
            <div class="mt-5">
                <div class="text-start">
                    <h2 class="mb-3 fw-bold text-start">Transactions</h2>
                    <p class="text-muted">All PDC-related payments are listed below</p>
                </div>
                <div class="table-responsive">
                    <table id="transactionsTable" class="table table-hover align-middle shadow-sm rounded-3">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center">Party</th>
                                <th class="text-center">Amount Paid</th>
                                <th class="text-center">Salesman</th>
                                <th class="text-center">Remarks</th>
                                <th class="text-center">Transaction Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\Transactions::where('client_id', $pdc->client_id)
                            ->where('agent_id', $pdc->agent_id)
                            ->where('pc_id', $pdc->id)
                            ->where('type', 'pdc')
                            ->get() as $transaction)
                            <tr class="text-center">
                                <td>{{ $transaction->client->name ?? 'N/A' }}</td>
                                <td class="fw-bold text-success">Rs. {{ number_format($transaction->pay_amount,2)}}
                                </td>
                                <td>{{ $transaction->agent->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->remarks ?? 'No Remarks' }}</td>
                                <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
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
            <div class="card-footer bg-light text-end py-3">
                <a href="{{ route('clients.show', $pdc->client->id) }}" class="btn btn-danger px-4">Back</a>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modals -->
@foreach($pdc->activeInstallments as $installment)
<div class="modal fade" id="payModal{{ $installment->id }}" tabindex="-1" role="dialog"
    aria-labelledby="payModalLabel{{ $installment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header p-3">
                <h5 class="modal-title" id="payModalLabel{{ $installment->id }}">
                    <i class="fas fa-wallet me-2"></i> Partial Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="{{ route('pdcInstallments.pay', $installment->id) }}" method="POST">
                @csrf
                <input type="hidden" name="installment_id" value="{{ $installment->id }}">
                <input type="hidden" name="pc_id" value="{{ $pdc->id }}">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="pay_amount_{{ $installment->id }}" class="fw-bold">
                            <i class="fas fa-money-bill-wave me-1"></i> Amount to Pay
                        </label>
                        <input type="number" class="form-control rounded" id="pay_amount_{{ $installment->id }}"
                            name="pay_amount" placeholder="Enter Amount" min="1"
                            max="{{ $installment->amount - ($installment->data1 ?? 0) }}" required>
                        @error('pay_amount')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="remarks_{{ $installment->id }}" class="fw-bold">
                            <i class="fas fa-comment me-1"></i> Remarks
                        </label>
                        <textarea class="form-control rounded" id="remarks_{{ $installment->id }}" name="remarks"
                            placeholder="Enter Remarks" rows="3"></textarea>
                        @error('remarks')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fas fa-check me-1 text-white"></i> Confirm Payment
                    </button>
                    <button type="button" class="btn btn-danger fw-bold" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1 text-white"></i> Cancel
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

@endsection

@section('customJs')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script>
    $(function() {
    $("#installmentsTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#installmentsTable_wrapper .col-md-6:eq(0)');

    $("#failedInstallmentsTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#failedInstallmentsTable_wrapper .col-md-6:eq(0)');

    $("#transactionsTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#transactionsTable_wrapper .col-md-6:eq(0)');
});
</script>
@endsection