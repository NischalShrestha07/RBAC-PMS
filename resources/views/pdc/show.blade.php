@extends('layouts.superApp',['page' => 'Pdcs'])

@section('customCss')
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection


@section('content')
<div class="mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="card-title mb-0">Post Dated PDC's Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <h6 class="text-muted">Party Name:</h6>
                    <p class="fw-bold">{{ $pdc->client->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">PDC Amount:</h6>
                    <p class="fw-bold">{{ number_format($pdc->commitment_amount, 2) }}</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">Received Amount:</h6>
                    <p>
                        @php
                        $totalPaidAmount = $pdc->transactions->where('pc_id', $pdc->id)->sum('pay_amount');
                        @endphp
                        {{ number_format($totalPaidAmount, 2) }}
                    </p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">Pending Amount:</h6>
                    <p>
                        @php
                        $totalPendingAmount = $pdc->commitment_amount - $totalPaidAmount;
                        @endphp
                        {{ number_format($totalPendingAmount, 2) }}
                    </p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">Salesman:</h6>
                    <p class="fw-bold">{{ $pdc->agent->name ?? 'No Salesman Assigned' }}</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">PDC Date:</h6>
                    <p class="fw-bold">
                        @php
                        $oldestIncompleteInstallment = $pdc->installments
                        ->filter(function ($installment) {
                        return $installment->status !== 'Complete';
                        })
                        ->sortBy('due_date')
                        ->first();
                        @endphp
                        @if($oldestIncompleteInstallment)
                        {{ $oldestIncompleteInstallment->due_date }}
                        @else
                        N/A
                        @endif
                    </p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">Department:</h6>
                    <p class="fw-bold">{{ $pdc->department }}</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">Status:</h6>
                    <p class="fw-bold">{{ $pdc->status }}</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-muted">Entry Date:</h6>
                    <p class="fw-bold">{{ $pdc->created_at->format('Y-m-d') }}</p>
                </div>
                <div class="col-md-8">
                    <h6 class="text-muted">Commitment Details</h6>
                    @php
                    $words = explode(' ', $pdc->remarks);
                    $preview = implode(' ', array_slice($words, 0, 2));
                    @endphp
                    <p id="shortText{{ $pdc->id }}" data-bs-toggle="tooltip" title="{{ $pdc->remarks }}">
                        {{ $preview }}...
                        <a href="javascript:void(0);" class="text-primary"
                            onclick="showFullText({{ $pdc->id }})">More</a>
                    </p>
                    <p id="fullText{{ $pdc->id }}" style="display: none;">
                        {{ $pdc->remarks }}
                        <a href="javascript:void(0);" class="text-danger" onclick="hideFullText({{ $pdc->id }})">Show
                            Less</a>
                    </p>
                </div>
            </div>

            <!-- Installments Table -->
            <div class="text-center mt-4">
                <h3 class="mb-2 text-primary font-weight-bold">Installments</h3>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Entry Date</th>
                        <th>Cheque No</th>
                        <th>Bank</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Commitment Age</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pdc->installments as $installment)
                    <tr>
                        <td>{{ $installment->created_at->format('Y-m-d') }}</td>
                        <td>{{ $installment->chequeno }}</td>
                        <td>{{ $installment->bankName }}</td>
                        <td>
                            @if($installment->data1 > 0)
                            <del>{{ number_format($installment->amount, 2) }}</del><br>
                            <strong>{{ number_format($installment->amount - $installment->data1, 2) }}</strong>
                            @else
                            {{ number_format($installment->amount, 2) }}
                            @endif
                        </td>
                        <td>{{ $installment->due_date }}</td>
                        <td>{{ $installment->status }}</td>
                        <td>
                            @php
                            $dueDate = \Carbon\Carbon::parse($installment->due_date);
                            $now = \Carbon\Carbon::today();
                            $daysExceeded = $now->diffInDays($dueDate, false);
                            @endphp
                            @if($installment->status === 'Complete')
                            <h6 class="text-success">Complete</h6>
                            @elseif($daysExceeded < 0) <h6 class="text-danger">Overdue by {{ abs($daysExceeded) }}
                                days</h6>
                                @else
                                <h6 style="color: black">{{ $daysExceeded }} days left</h6>
                                @endif
                        </td>
                        <td>
                            @if(in_array($installment->status, ['Pending', 'Overdue']))
                            <button class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#payModal{{ $installment->id }}">Pay</button>
                            @else
                            <h6 style="color: #28A745; font-weight: bold;">Completed</h6>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Transactions Table -->
            <div class="mt-4">
                <h2 class="text-center mb-4 text-primary font-weight-bold">Transactions</h2>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Party</th>
                                <th>Pay Amount</th>
                                <th>Salesman</th>
                                <th>Remarks</th>
                                <th>Transaction Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\Transactions::where('client_id', $pdc->client_id)
                            ->where('agent_id', $pdc->agent_id)
                            ->where('pc_id', $pdc->id)
                            ->where('type', 'pdc')
                            ->get() as $transaction)
                            <tr>
                                <td>{{ $transaction->client->name ?? 'N/A' }}</td>
                                <td class="font-weight-bold text-success">Rs. {{
                                    number_format($transaction->pay_amount, 2) }}</td>
                                <td>{{ $transaction->agent->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->remarks ?? 'No Remarks' }}</td>
                                <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No transactions found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('clients.show',$pdc->client->id) }}" class="btn btn-danger">Back</a>
        </div>
    </div>
</div>

<!-- Payment Modals -->
@foreach($pdc->installments as $installment)
<div class="modal fade" id="payModal{{ $installment->id }}" tabindex="-1"
    aria-labelledby="payModalLabel{{ $installment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">
            <div class="modal-header p-3">
                <h5 class="modal-title" id="payModalLabel{{ $installment->id }}">
                    <i class="fas fa-wallet me-2"></i> Partial Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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