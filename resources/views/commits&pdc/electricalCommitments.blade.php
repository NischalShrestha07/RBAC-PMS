@extends('layouts.electricalApp',['page' => 'Commitments'])

@section('content')
<section class="content-header d-flex gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('electrical.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Commitments</li>
    </div>
    <ol class="float-sm-right">
        <div class="d-flex gap-2">
            <button class="border-0 bg-transparent p-0" href="{{ asset('admincss/img/excel.png') }}"
                id="exportExcelButton">
                <img src="{{ asset('admincss/img/excel.png') }}" alt="Export Excel" width="30">

            </button>
            <button class="border-0 bg-transparent p-0" href="{{ asset('admincss/img/pdf.png') }}" id="exportPdfButton">
                <img src="{{ asset('admincss/img/pdf.png') }}" alt="Export PDF" width="30">

            </button>
        </div>
    </ol>
</section>

<style>
    .ff-filter>* {
        flex: 1 1 8rem;
    }
</style>
<form action="{{ route('electrical.commitments.create') }}" method="get" id="filterForm"
    class="d-flex flex-column gap-2">
    <!-- Main Filter Row -->
    <div class="ff-filter d-flex flex-wrap gap-2 text-nowrap text-center">
        <button type="button" id="clearFilters" class="btn btn-sm btn-secondary px-3">All</button>
        <!-- Date Filter -->
        <select class="form-control custom-select" id="filterDate" name="filter_date">
            <option value="">Commitment Date</option>
            <option value="today" {{ request('filter_date')=='today' ? 'selected' : '' }}>
                Today</option>
            <option value="this_week" {{ request('filter_date')=='this_week' ? 'selected' : '' }}>This Week
            </option>
            <option value="this_month" {{ request('filter_date')=='this_month' ? 'selected' : '' }}>This Month
            </option>
            <option value="custom" {{ request('filter_date')=='custom' ? 'selected' : '' }}>
                Custom</option>
        </select>

        <!-- Salesman Filter -->
        <div class="col-md-3">
            <div class="position-relative searchable-dropdown">
                <div class="dropdown w-100">
                    <button class="form-control text-start dropdown-toggle-btn" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ request('filter_agent') ?? 'Salesman' }}
                    </button>

                    <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                        style="max-height: 250px; overflow-y: auto;">
                        <li class="px-2 py-1 sticky-top bg-white border-bottom">
                            <input type="text" class="form-control form-control-sm dropdown-search"
                                placeholder="Search salesman...">
                        </li>
                        @foreach ($agents as $agent)
                        <li>
                            <a href="#" class="dropdown-item dropdown-item-option" data-value="{{ $agent->name }}">
                                <small>{{ $agent->name }}</small>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <input type="hidden" name="filter_agent" class="dropdown-hidden-input"
                        value="{{ request('filter_agent') }}">
                </div>
            </div>
        </div>

        <!-- Client Filter -->
        <div class="position-relative searchable-dropdown">
            <div class="dropdown w-100">
                <button class="form-control text-start dropdown-toggle-btn" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    {{ request('filter_client') ?? 'Party' }}
                </button>

                <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                    style="max-height: 300px; overflow-y: auto;">
                    <li class="px-2 py-1 sticky-top bg-white border-bottom">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control border-start-0 dropdown-search"
                                placeholder="Search party...">
                        </div>
                    </li>

                    @foreach ($clients as $client)
                    <li>
                        <a href="#" class="dropdown-item dropdown-item-option"
                            data-value="{{ $client->name }}"><small>{{
                                $client->name
                                }}</small></a>
                    </li>
                    @endforeach
                </ul>

                <input type="hidden" name="filter_client" class="dropdown-hidden-input"
                    value="{{ request('filter_client') }}">
            </div>
        </div>

        <!-- Status Filter -->
        <select class="form-control custom-select" id="filterStatus" name="filter_status">
            <option value="">Status</option>
            <option value="Pending" {{ request('filter_status')=='Pending' ? 'selected' : '' }}>
                Pending</option>

            <option value="Overdue" {{ request('filter_status')=='Overdue' ? 'selected' : '' }}>
                Overdue</option>
        </select>

        <!-- Buttons -->
        <button type="submit" class="btn btn-sm btn-success px-3">Apply</button>
    </div>

    <!-- Custom Date Range (Below the Row) -->
    <div id="customDateRange" class="custom-date-range d-none d-flex align-items-center gap-2">
        <large class="text-muted text-nowrap text-center">Custom Date:</large>
        <input type="date" id="start_date" name="start_date" class="form-control " value="{{ request('start_date') }}"
            placeholder="Start Date">
        <large class="text-muted"> to </large>
        <input type="date" id="end_date" name="end_date" class="form-control " value="{{ request('end_date') }}"
            placeholder="End Date">
    </div>
</form>

<script>
    // Clear filter function
                                                            document.getElementById('clearFilters').addEventListener('click', function () {
                                                            document.getElementById('filterForm').reset(); // Reset all inputs
                                                            window.location.href = "{{ route('electrical.commitments.create') }}"; // Redirect to show all data
                                                            });

                                                            document.getElementById('filterDate').addEventListener('change', function () {
                                    const customDateRange = document.getElementById('customDateRange');
                                    if (this.value === 'custom') {
                                        customDateRange.classList.remove('d-none');
                                    } else {
                                        customDateRange.classList.add('d-none');
                                    }
                                });

                                // Pre-select the correct date range option
                                if (document.getElementById('filterDate').value === 'custom') {
                                    document.getElementById('customDateRange').classList.remove('d-none');
                                }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.19/jspdf.plugin.autotable.min.js">
</script>

{{-- excel and pdf --}}
<script>
    $(document).ready(function() {
                                                                $('#commitmentEmployeeTable').DataTable({
                                                                    "paging": true,
                                                                    "ordering": true,
                                                                    "info": true,
                                                                    "responsive": true
                                                                });
                                                            });

                                                            function exportToExcel() {
                                                                var table = document.getElementById('commitmentEmployeeTable');
                                                                var wb = XLSX.utils.book_new();
                                                                var ws = XLSX.utils.table_to_sheet(table);
                                                                XLSX.utils.book_append_sheet(wb, ws, "Commitments");
                                                                XLSX.writeFile(wb, 'Commitments.xlsx');
                                                            }

                                                            function exportStyledPDF() {
                                                                const { jsPDF } = window.jspdf;
                                                                const doc = new jsPDF('l', 'mm', 'a4');
                                                                doc.setFontSize(18);
                                                                doc.text("Commitments Data Report", 14, 15);
                                                                doc.setFontSize(12);
                                                                doc.text("This report contains commitments-related financial data.", 14, 25);
                                                                doc.autoTable({
                                                                    html: "#commitmentEmployeeTable",
                                                                    startY: 35,
                                                                    styles: { fontSize: 10, cellPadding: 2, overflow: 'linebreak' },
                                                                    headStyles: { fillColor: [44, 62, 80], textColor: [255, 255, 255] },
                                                                    alternateRowStyles: { fillColor: [240, 240, 240] }
                                                                });
                                                                doc.save('Commitments.pdf');
                                                            }

                                                            document.getElementById('exportExcelButton').addEventListener('click', exportToExcel);
                                                            document.getElementById('exportPdfButton').addEventListener('click', exportStyledPDF);
</script>
<table style="display: none;" id="commitmentEmployeeTable" class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr class="text-center">
            <th>Entry Date</th>
            <th>Party Name</th>
            <th>Address</th>
            <th>Total Commitment </th>
            <th>Received Amount </th>
            <th>Pending Amount</th>
            <th>Commitment Age(Day)</th>
            <th>Salesman</th>
            <th>Department</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($commitments as $item)
        <tr>
            <td>{{ $item->created_at->format('Y-m-d') }}</td>

            <td>{{ $item->name ?? 'N/A' }}</td>
            <td>{{ $item->client->address ?? 'N/A' }}</td>
            @php
            $totalPendingAmount = $item->installments->where('status',
            'Pending')->sum('amount');
            @endphp
            <td>{{ $item->total_amount ?? 'N/A' }}</td>

            <td>
                @php
                // Sum of 'Complete' installments
                $totalPaidAmount = $item->installments->where('status',
                'Complete')->sum('amount');

                // Sum of partial payments from transactions table
                $partialPayments = \App\Models\Transactions::where('client_id',
                // $item->client_id)->where('installment_id', $item->id)
                $item->client_id)
                ->sum('pay_amount');

                // Total received amount (Complete Installments + Partial Payments)
                $totalReceived = $totalPaidAmount + $partialPayments;

                // Calculate pending amount (Total Amount - Received Amount)
                $totalAmount = $item->total_amount ?? 0;
                $totalPendingAmount = $totalAmount - $totalReceived;
                @endphp
                <!-- Display the total received amount -->
                {{ number_format($totalReceived, 2) }}
            </td>


            <td>
                @php
                $totalPending=$totalAmount-$totalReceived;
                @endphp
                {{ number_format($totalPending, 2) }} </td>



            @php
            // Fetch the first installment that is not "Complete"
            $installment = $item->installments()->where('status', '!=',
            'Complete')->first();

            // Check if all installments are completed
            $isCompleted = !$installment; // If no pending installment exists, mark


            // Determine the due date
            $dueDate = $installment?->due_date ?
            \Carbon\Carbon::parse($installment->due_date) : null;

            // Calculate remaining days safely
            $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
            false) : null;
            @endphp

            <td>
                @if ($isCompleted)
                <h6 class="text-success pt-2 align-center">Completed</h6>
                @elseif ($dueDate)
                @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days @elseif ($remainingDays==0) Due Today
                    @else {{ $remainingDays }} days remaining @endif @else <span class="text-warning">No Due
                    Date</span>
                    @endif
            </td>


            <td>{{ $item->agent->name ?? 'N/A' }}</td>
            <td>{{ $item->department ?? 'N/A' }}</td>
            {{-- <td>{{ $item->installments->pluck('amount')->implode(', ') }}</td>
            <td>{{ $item->installments->pluck('due_date')->implode(', ') }}</td>
            --}}

            <td>{{ $item->status ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Commitments</h5>
        </div>
        <div class="table-responsive">
            <table id="EmployeeTable" class="table table-striped table-bordered text-nowrap text-center">
                <thead>
                    <tr class="text-center">
                        <th>Entry Date</th>
                        <th>Party </th>
                        <th>Address</th>
                        <th>Total Commitment</th>
                        <th>Received Amount </th>
                        <th>Pending Amount </th>
                        <th>Commitment Age(Day)</th>
                        <th>Salesman</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commitments as $item)
                    <tr class="text-center">
                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                        <td>{{ $item->client->name }}</td>
                        <td>{{ $item->client->address ?? 'N/A' }}</td>
                        <td class="text-center">Rs {{ number_format($item->total_amount, 2)
                            }}</td>
                        <td class="text-center">
                            @php
                            $totalPaidAmount = $item->transactions->where('pc_id',
                            $item->id)->sum('pay_amount');
                            @endphp
                            Rs {{ number_format($totalPaidAmount, 2) }}
                        </td>
                        <td class="text-center">
                            @php
                            $totalPendingAmount = $item->total_amount - $totalPaidAmount;
                            @endphp
                            Rs {{ number_format($totalPendingAmount, 2) }}
                        </td>
                        <td>
                            @php
                            $installment = $item->installments()->where('status', '!=',
                            'Complete')->first();
                            $isCompleted = !$installment;
                            $dueDate = $installment?->due_date ?
                            \Carbon\Carbon::parse($installment->due_date) : null;
                            $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
                            false) : null;
                            @endphp
                            @if ($isCompleted)
                            <span class="text-success fw-semibold">Completed</span>
                            @elseif ($dueDate)
                            @if ($remainingDays < 0) <span class="text-danger fw-semibold">Overdue by {{
                                abs($remainingDays) }} days</span>
                                @elseif ($remainingDays == 0)
                                <span class="text-warning fw-semibold">Due Today</span>
                                @else
                                <span class="text-muted fw-semibold">{{ $remainingDays }} days
                                    remaining</span>
                                @endif
                                @else
                                <span class="text-warning fw-semibold">No Due Date</span>
                                @endif
                        </td>
                        <td>{{ $item->agent->name ?? 'No Agent Assigned' }}</td>
                        <td>
                            <span class="badge badge-sm
                                                                    {{ $item->status == 'Complete' ? 'bg-success' : ($item->status == 'Overdue' ? 'bg-danger' : 'bg-warning') }}
                                                                    rounded-pill px-3 py-2">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="text-primary border-0 bg-transparent align-center rounded-pill"
                                title="View Details" data-bs-toggle="modal" data-bs-target="#viewModel{{ $item->id }}">
                                <i class="fas fa-eye fa-lg"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4" style="background: white">
            {{$commitments->appends(request()->query())->links()}}
        </div>
    </div>
</div>
@foreach ($commitments as $item)
<div class="modal fade" id="viewModel{{ $item->id }}" tabindex="-1" aria-labelledby="viewModelLabel{{ $item->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-sm">
            <!-- Modal Header -->
            <div class="modal-header text-start">
                <h5 class="modal-title fw-bold" id="viewModelLabel{{ $item->id }}">
                    <i class="fas fa-clipboard-list me-2"></i> Commitment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <div class="card border-0 rounded-3">
                    <div class="card-body">
                        <!-- Commitment Details -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Party Name</h6>
                                <p class="fw-semibold">{{ $item->client->name }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Salesman</h6>
                                <p class="fw-semibold">{{ $item->agent->name ?? 'No
                                    Salesman
                                    Assigned' }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Total Amount</h6>
                                <p class="fw-semibold">Rs {{
                                    number_format($item->total_amount, 2) }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Received Amount</h6>
                                <p class="fw-semibold">
                                    @php
                                    $totalPaidAmount =
                                    $item->transactions->where('pc_id',
                                    $item->id)->sum('pay_amount');
                                    @endphp
                                    Rs {{ number_format($totalPaidAmount, 2) }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Pending Amount</h6>
                                <p class="fw-semibold">
                                    @php
                                    $totalPendingAmount = $item->total_amount -
                                    $totalPaidAmount;
                                    @endphp
                                    Rs {{ number_format($totalPendingAmount, 2) }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Department</h6>
                                <p class="fw-semibold">{{ $item->department ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Status</h6>
                                <span class="badge
                                                                {{ $item->status === 'Complete' ? 'bg-success' : ($item->status === 'Overdue' ? 'bg-danger' : 'bg-warning text-dark') }}
                                                                rounded-pill px-3 py-2">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-1">Entry Date</h6>
                                <p class="fw-semibold">{{
                                    \Carbon\Carbon::parse($item->created_at)->format('M
                                    d, Y')
                                    }}
                                </p>
                            </div>
                            @php
                            $words = explode(' ', $item->remarks);
                            $preview = implode(' ', array_slice($words, 0, 5));
                            @endphp

                            <div class="col-md-9">
                                <h6 class="text-muted mb-1">Commitment Details</h6>

                                <!-- Short Text -->
                                <p id="shortText{{ $item->id }}" class="mb-0" data-bs-toggle="tooltip"
                                    title="{{ $item->remarks }}">
                                    {{ $preview }}...
                                    <a href="javascript:void(0);" class="text-primary fw-semibold"
                                        onclick="showFullText({{ $item->id }})">
                                        More
                                    </a>
                                </p>

                                <!-- Full Text -->
                                <p id="fullText{{ $item->id }}" class="mb-0 d-none">
                                    {{ $item->remarks }}
                                    <a href="javascript:void(0);" class="text-danger fw-semibold"
                                        onclick="hideFullText({{ $item->id }})">
                                        Less
                                    </a>
                                </p>
                            </div>

                            <script>
                                function showFullText(id) {
                                                                document.getElementById('shortText' + id).classList.add('d-none');
                                                                document.getElementById('fullText' + id).classList.remove('d-none');
                                                            }

                                                            function hideFullText(id) {
                                                                document.getElementById('fullText' + id).classList.add('d-none');
                                                                document.getElementById('shortText' + id).classList.remove('d-none');
                                                            }
                            </script>
                        </div>

                        <!-- Installments Table -->
                        <h5 class="text-start fw-bold mb-3 mt-4">Installments
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-nowrap">
                                <thead>
                                    <tr class="text-center">
                                        <th>Entry Date</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Commitment Age</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->installments as $installment)
                                    <tr class="text-center">
                                        <td>{{
                                            \Carbon\Carbon::parse($installment->created_at)->format('M
                                            d, Y') }}</td>
                                        <td>
                                            @if($installment->paid_amount > 0)
                                            <del>Rs {{
                                                number_format($installment->amount, 2)
                                                }}</del><br>
                                            <strong>Rs {{
                                                number_format($installment->amount -
                                                $installment->paid_amount, 2)
                                                }}</strong>
                                            @else
                                            Rs {{ number_format($installment->amount, 2)
                                            }}
                                            @endif
                                        </td>
                                        <td>{{
                                            \Carbon\Carbon::parse($installment->due_date)->format('M
                                            d, Y') }}</td>
                                        <td>
                                            <span class="badge
                                                                                {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : 'bg-warning text-dark') }}
                                                                                rounded-pill px-3 py-2">
                                                {{ ucfirst($installment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                            $dueDate =
                                            \Carbon\Carbon::parse($installment->due_date);
                                            $now = \Carbon\Carbon::today();
                                            $daysExceeded = $now->diffInDays($dueDate,
                                            false);
                                            @endphp
                                            @if($installment->status === 'Complete')
                                            <span class="text-success fw-semibold">Complete</span>
                                            @elseif($daysExceeded < 0) <span class="text-danger fw-semibold">Overdue
                                                by
                                                {{ abs($daysExceeded) }} days</span>
                                                @else
                                                <span class="text-muted fw-semibold">{{
                                                    $daysExceeded }} days
                                                    left</span>
                                                @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Transactions Table -->
                        <h5 class="text-start fw-bold mb-2 mt-3">
                            Transactions</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead>
                                    <tr class="text-center">
                                        <th>Party</th>
                                        <th>Pay Amount</th>
                                        <th>Salesman</th>
                                        <th>Remarks</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(App\Models\Transactions::where('client_id',
                                    $item->client_id)->where('agent_id',
                                    $item->agent_id)->where('pc_id',
                                    $item->id)->get() as $transaction)
                                    <tr>
                                        <td>{{ $transaction->client->name ?? 'N/A' }}
                                        </td>
                                        <td class="text-success fw-semibold">Rs {{
                                            number_format($transaction->pay_amount, 2)
                                            }}</td>
                                        <td>{{ $transaction->agent->name ?? 'N/A' }}
                                        </td>
                                        <td>{{ $transaction->remarks ?? 'No Remarks' }}
                                        </td>
                                        <td>{{
                                            \Carbon\Carbon::parse($transaction->created_at)->format('M
                                            d, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No
                                            transactions found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    $(function() {
        $("#EmployeeTable").DataTable({
            "responsive": true,
            "lengthChange": false,

 "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#EmployeeTable_wrapper .col-md-6:eq(0)');
    });
</script>
<script>
    document.getElementById('total_amount').addEventListener('input', calculatePendingAmount);
    document.getElementById('paid_amount').addEventListener('input', calculatePendingAmount);

    function calculatePendingAmount() {
        const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
        const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        const pendingAmount = totalAmount - paidAmount;
        document.getElementById('pending_amount').value = pendingAmount >= 0 ? pendingAmount : 0;
    }
</script>
@endsection