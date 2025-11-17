@extends('layouts.superApp',['page' => 'View Pdcs'])



@section('content')
<div class="">
    <div class="card border-0">
        <!-- Header -->
        <div class="card-header text-start py-3">
            <h3 class="card-title mb-0 fw-bold">Post Dated PDC Information</h3>
        </div>

        <div class="card-body">
            <div class="card  border-0 rounded-3">
                <div class="card-body">
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
                                $oldestIncompleteInstallment = $pdc->installments
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
                            <p class="fw-bold fs-6 text-dark">{{ $pdc->department()->first() ?
                                $pdc->department()->first()->name : '--' }}</p>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Status</h6>
                            <p
                                class="fw-bold
                                                                                                                        @if ($pdc->status == 'Complete') text-success
                                                                                                                        @elseif($pdc->status == 'Overdue') text-danger
                                                                                                                        @elseif($pdc->status == 'Failed') text-secondary
                                                                                                                        @else text-warning @endif">
                                {{ ucfirst($pdc->status) }}
                            </p>
                        </div>

                        <!-- Entry Date -->
                        <div class="col-md-3">
                            <h6 class="text-muted">Entry Date</h6>
                            <p class="fw-bold fs-6 text-dark">{{ $pdc->created_at->format('Y-m-d') }}</p>
                        </div>

                        <!-- Commitment Details -->
                        <div class="col-md-9">
                            <h6 class="text-muted fw-semibold">Commitment Details</h6>
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
                <div class="text-start mb-2">
                    <h3 class="fw-bold">Installments</h3>
                    <p class="text-muted">Track and manage all installment details easily</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle  rounded-3">
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
                            @forelse($pdc->installments as $installment)
                            <tr class="text-center">
                                <td class="fw-semibold">{{ $installment->created_at->format('Y-m-d') }}</td>
                                <td class="fw-semibold">{{ $installment->chequeno }}</td>
                                <td class="fw-semibold">{{ $installment->bankName }}</td>
                                <td class="fw-semibold">
                                    @if($installment->data1 > 0)
                                    <del>{{ number_format($installment->amount, 2) }}</del><br>
                                    <strong class="text-success">{{ number_format($installment->amount -
                                        $installment->data1, 2)
                                        }}</strong>
                                    @else
                                    {{ number_format($installment->amount, 2) }}
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $installment->due_date }}</td>
                                <td class="text-center fw-semibold">
                                    <p
                                        class="fw-bold
                                        {{ $installment->status === 'Complete' ? 'text-success' : ($installment->status === 'Overdue' ? 'text-danger' : 'text-warning') }}">
                                        {{ $installment->status }}
                                    </p>
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
                                    <button class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#payModal{{ $installment->id }}">
                                        <i class="fas fa-hand-holding-usd me-1 text-white"></i> Pay
                                    </button>
                                    @else
                                    <span class="text-success fw-bold">Completed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    🚫 No Installments found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            @if($pdc->failedInstallments->count() > 0)
            <div class="mt-4">
                <div class="text-start mb-4">
                    <h2 class="fw-bold">Failed Installments History</h2>
                    <p class="text-muted">List of failed installments for this PDC</p>
                </div>

                <div class="table-responsive  rounded">
                    <table class="table align-middle table-hover">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="text-center">Entry Date</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Cheque No</th>
                                <th class="text-center">Bank Name</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pdc->failedInstallments as $failedInstallment)
                            <tr class="text-center">
                                <td class="text-center fw-medium">
                                    {{ $failedInstallment->created_at->format('Y-m-d') }}
                                </td>
                                <td class="text-center fw-bold">
                                    Rs. {{ number_format($failedInstallment->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    {{ $failedInstallment->chequeno ?? 'N/A' }}
                                </td>
                                <td class="text-center">
                                    {{ $failedInstallment->bankName ?? 'N/A' }}
                                </td>
                                <td class="text-center text-secondary">
                                    {{ \Carbon\Carbon::parse($failedInstallment->due_date)->format('Y-m-d') }}
                                </td>
                                <td class="text-center">
                                    <span class="text-danger fw-bold">
                                        {{ $failedInstallment->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    🚫 No Failed Installments found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Transactions -->
            <div class="mt-2">
                <div class="text-start mb-4">
                    <h2 class="mb-3 fw-bold">Transactions</h2>
                    <p class="text-muted">All pdcs-related payments are listed below</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle  rounded-3">
                        <thead>
                            <tr>
                                <th class="text-center"> Party</th>
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
                                <td class="fw-bold text-success">Rs. {{ number_format($transaction->pay_amount,
                                    2)
                                    }}
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
                <a href="{{ route('pdcs.index') }}" class="btn btn-danger px-4">Back</a>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modals -->
@foreach($pdc->installments as $installment)
<div class="modal fade" id="payModal{{ $installment->id }}" tabindex="-1"
    aria-labelledby="payModalLabel{{ $installment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg rounded">
            <div class="modal-header p-3">
                <h5 class="modal-title" id="payModalLabel{{ $installment->id }}">
                    <i class="fas fa-wallet mr-2"></i> Partial Payment
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
                        <i style="color: white;" class="fas fa-check me-1"></i> Confirm Payment
                    </button>
                    <button type="button" class="btn btn-danger fw-bold" data-bs-dismiss="modal">
                        <i style="color: white;" class="fas fa-times me-1"></i> Cancel
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

<!-- Bootstrap JS and Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('customJs')
<script>
    $(function() {
            $("
                #EmployeeTable ").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,

                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).

        buttons().container().appendTo('#EmployeeTable_wrapper .col-md-6:eq(0)');
        });
</script>
@endsection