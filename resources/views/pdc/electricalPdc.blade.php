@extends('layouts.electricalApp',['page' => 'Pdcs'])

@section('content')
<section class="content-header d-flex gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('electrical.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">PDCs</li>
    </div>
    <ol class="float-sm-right">
        <div class="d-flex gap-2">
            <button class="border-0 bg-transparent p-0" href=" {{ asset('admincss/img/excel.png') }}"
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
<form action="{{ route('electrical.pdc.create') }}" method="get" id="filterForm" class="d-flex flex-column gap-2">

    <div class="ff-filter d-flex flex-wrap gap-2 text-nowrap">

        <button type="button" id="clearFilters" class="btn btn-sm btn-secondary px-3">All</button>

        <!-- Date Filter -->
        <select class="form-control custom-select mr-1" id="filterDate" name="filter_date">
            <option value="">Pdc Date</option>
            <option value="today" {{ request('filter_date')=='today' ? 'selected' : '' }}>
                Today</option>
            <option value="this_week" {{ request('filter_date')=='this_week' ? 'selected' : '' }}>This Week</option>
            <option value="this_month" {{ request('filter_date')=='this_month' ? 'selected' : '' }}>This Month
            </option>
            {{-- <option value="all" {{ request('filter_date')=='all' ? 'selected' : '' }}>
                All</option> --}}
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

        <!-- Submit Button -->
        <button type="submit" class="btn btn-sm btn-success px-2">Apply</button>
    </div>
    <!-- Custom Date Range -->
    <div id="customDateRange" class="custom-date-range d-none d-flex align-items-center gap-2">
        <input type="date" id="start_date" name="start_date" class="form-control form-control "
            value="{{ request('start_date') }}">
        <input type="date" id="end_date" name="end_date" class="form-control form-control "
            value="{{ request('end_date') }}">
    </div>
</form>

<script>
    // Clear filter function
                                                                document.getElementById('clearFilters').addEventListener('click', function () {
                                                                document.getElementById('filterForm').reset(); // Reset all inputs
                                                                window.location.href = "{{ route('electrical.pdc.create') }}"; // Redirect to show all data
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.19/jspdf.plugin.autotable.min.js">
</script>

<script>
    function exportToExcel() {
                                                                            var table = document.getElementById('addEmployeeTable'); // Ensure table ID matches
                                                                            var wb = XLSX.utils.book_new();
                                                                            var ws = XLSX.utils.table_to_sheet(table, { raw: true });

                                                                            // Adjust column width for better formatting
                                                                            ws['!cols'] = Array.from({ length: table.rows[0].cells.length }, () => ({ wch: 15 }));

                                                                            XLSX.utils.book_append_sheet(wb, ws, "Employee Data");
                                                                            XLSX.writeFile(wb, 'Pdc-Employee-Data-Excel.xlsx');
                                                                        }

                                                                        function exportStyledPDF() {
                                                                            const { jsPDF } = window.jspdf;
                                                                            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape mode for better column spacing

                                                                            // Title
                                                                            doc.setFontSize(18);
                                                                            doc.text("PDC Data Report", 14, 15);

                                                                            // Subtitle
                                                                            doc.setFontSize(12);
                                                                            doc.text("This report contains pdcs-related financial data.", 14, 25);

                                                                            // Extract table data, excluding modals
                                                                            const table = document.getElementById("addEmployeeTable");
                                                                            const headers = [];
                                                                            const data = [];

                                                                            // Get header row (without modals)
                                                                            table.querySelectorAll("thead tr th").forEach(th => {
                                                                                headers.push(th.innerText);
                                                                            });

                                                                            // Get table data (excluding modal-related content)
                                                                            table.querySelectorAll("tbody tr").forEach(tr => {
                                                                                const rowData = [];
                                                                                tr.querySelectorAll("td").forEach((td, index) => {
                                                                                    if (!td.querySelector('.modal') && index !== tr.cells.length - 1) { // Exclude modal and actions
                                                                                        rowData.push(td.innerText);
                                                                                    }
                                                                                });
                                                                                data.push(rowData);
                                                                            });

                                                                            // AutoTable configuration
                                                                            doc.autoTable({
                                                                                head: [headers],
                                                                                body: data,
                                                                                startY: 35,
                                                                                styles: {
                                                                                    fontSize: 10,
                                                                                    cellPadding: 2,
                                                                                    overflow: 'linebreak'
                                                                                },
                                                                                headStyles: {
                                                                                    fillColor: [44, 62, 80],
                                                                                    textColor: [255, 255, 255]
                                                                                },
                                                                                alternateRowStyles: {
                                                                                    fillColor: [240, 240, 240]
                                                                                },
                                                                                columnStyles: {
                                                                                    0: { cellWidth: 30 },
                                                                                    1: { cellWidth: 40 },
                                                                                    2: { cellWidth: 50 },
                                                                                }
                                                                            });

                                                                            // Footer
                                                                            const finalY = doc.lastAutoTable.finalY + 10;
                                                                            doc.setFontSize(12);
                                                                            doc.text("Generated on: " + new Date().toLocaleDateString(), 14, finalY);

                                                                            // Save PDF
                                                                            doc.save('Pdc-Employee-Data-Pdf.pdf');
                                                                        }

                                                                        // Event Listeners
                                                                        document.getElementById('exportExcelButton').addEventListener('click', exportToExcel);
                                                                        document.getElementById('exportPdfButton').addEventListener('click', exportStyledPDF);
</script>

<table style="display: none;" id="addEmployeeTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Entry Date</th>
            <th>Party</th>
            <th>Address</th>
            <th>Pdc Amount</th>
            <th>Salesman</th>
            <th>Pdc Age</th>
            <th>Department</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($pdcs as $item)
        <tr>
            <td>{{$item->created_at->format('Y-m-d')}}</td>
            <td>{{ $item->client->name ?? 'N/A' }}</td>
            <td>{{ $item->client->address?? 'N/A' }}</td>
            <td>{{ $item->commitment_amount ?? 'N/A' }}</td>
            <td>{{ $item->agent->name ?? 'No Salesman Assigned' }}</td>
            @php
            $installment = $item->installments()->where('status', '!=',
            'Complete')->first();

            $isCompleted = !$installment; // If no pending installment exists, mark

            $dueDate = $installment?->due_date ?
            \Carbon\Carbon::parse($installment->due_date) : null;

            $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
            false) : null;
            @endphp


            @if ($isCompleted)
            <td class="text-success pt-2 align-center">Completed</td>
            @elseif ($dueDate)
            @if ($remainingDays < 0) <td> Overdue by {{ abs($remainingDays) }} days</td>
                @elseif ($remainingDays==0) <td> Due Today</td> @else <td> {{
                    $remainingDays
                    }} days remaining</td> @endif @else <td class="text-warning">No Due
                    Date</td>
                @endif




                <td>{{ $item->department ?? 'N/A' }}</td>
                <td>{{ $item->status ?? 'N/A'}}</td>
                <td></td>

        </tr>
        @endforeach
    </tbody>
</table>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Pdcs</h5>
        </div>
        <div class="table-responsive">
            <table id="EmployeeTable" class="table table-bordered table-striped text-center text-nowrap mb-0">
                <thead>
                    <tr class="text-center">
                        <th>Entry Date</th>
                        <th>Party </th>
                        <th>Address</th>
                        <th>Pdc Amount</th>
                        <th>Salesman</th>
                        <th>Pdc Age</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pdcs as $item)
                    <tr class="text-center">
                        <td>{{$item->created_at->format('Y-m-d')}}</td>
                        <td>{{$item->client->name ?? 'N/A'}}</td>
                        <td>{{$item->client->address ?? 'N/A'}}</td>
                        <td>{{ $item->commitment_amount }}</td>

                        <td>{{ $item->agent->name ?? 'No Salesman Assigned' }}</td>

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
                            @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                                @elseif($remainingDays==0) Due Today @else {{ $remainingDays }} days remaining @endif
                                @else <span class="text-warning">No Due
                                Date</span>
                                @endif
                        </td>
                        <td>
                            <span class="badge badge-sm
                                                                                            @if($item->status == 'Complete') bg-success
                                                                                            @elseif($item->status == 'Overdue') bg-danger
                                                                                            @else bg-warning
                                                                                            @endif"
                                style="pointer-events: none; cursor: default; padding: 6px 10px; font-size: 12px;color:white; line-height: 1;">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="font-weight-medium">
                            <div class="btn-group " role="group" aria-label="Actions">
                                <button type="button" class="bg-transparent border-0 text-primary" title="View"
                                    data-toggle="modal" data-target="#viewModel{{ $item->id }}">
                                    <i class="fas fa-eye fa-lg"></i> </button>


                                <div class="modal fade" id="viewModel{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="viewModelLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content rounded-3 shadow-sm">
                                            <!-- Modal Header -->
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold" id="viewModelLabel{{ $item->id }}">
                                                    <i class="fas fa-money-check-alt me-2"></i> PDC Details
                                                </h5>
                                                <button type="button" class="btn-close" data-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <!-- Modal Body -->
                                            <div class="modal-body">
                                                <div class="card border-0 rounded-3">
                                                    <div class="card-header text-start fw-bold py-3">
                                                        <h5 class="mb-0 fw-semibold">Post Dated Cheque Information</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- PDC Details -->
                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <h6 class="text-muted mb-1">Party Name</h6>
                                                                <p class="fw-semibold">{{ $item->client->name }}</p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-muted mb-1">PDC Amount</h6>
                                                                <p class="fw-semibold">Rs {{
                                                                    number_format($item->commitment_amount, 2) }}</p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-muted mb-1">Received Amount</h6>
                                                                <p class="fw-semibold text-success">
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
                                                                <p class="fw-semibold text-danger">
                                                                    @php
                                                                    $totalPendingAmount = $item->commitment_amount -
                                                                    $totalPaidAmount;
                                                                    @endphp
                                                                    Rs {{ number_format($totalPendingAmount, 2) }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-muted mb-1">Salesman</h6>
                                                                <p class="fw-semibold">{{ $item->agent->name ?? 'No
                                                                    Salesman
                                                                    Assigned' }}</p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-muted mb-1">PDC Date</h6>
                                                                <p class="fw-semibold">
                                                                    @php
                                                                    $oldestIncompleteInstallment = $item->installments
                                                                    ->filter(function ($installment) {
                                                                    return $installment->status !== 'Complete';
                                                                    })
                                                                    ->sortBy('due_date')
                                                                    ->first();
                                                                    @endphp
                                                                    {{ $oldestIncompleteInstallment ?
                                                                    \Carbon\Carbon::parse($oldestIncompleteInstallment->due_date)->format('M
                                                                    d, Y') :
                                                                    'N/A' }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-muted mb-1">Department</h6>
                                                                <p class="fw-semibold">{{ $item->department }}</p>
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
                                                                <p class="fw-semibold">{{ $item->created_at->format('M
                                                                    d, Y') }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-muted">Commitment Details</h6>
                                                                @php
                                                                $words = explode(' ', $item->remarks);
                                                                $preview = implode(' ', array_slice($words, 0, 5));
                                                                $showMore = count($words) > 5;
                                                                @endphp
                                                                <p id="shortText{{ $item->id }}" class="text-dark"
                                                                    data-bs-toggle="tooltip"
                                                                    title="{{ $item->remarks }}">
                                                                    {{ $preview }}{{ $showMore ? '...' : '' }}
                                                                    @if ($showMore)
                                                                    <a href="javascript:void(0);"
                                                                        class="text-primary fw-semibold"
                                                                        onclick="showFullText({{ $item->id }})">More</a>
                                                                    @endif
                                                                </p>
                                                                <p id="fullText{{ $item->id }}" class="text-dark"
                                                                    style="display: none;">
                                                                    {{ $item->remarks }}
                                                                    @if ($showMore)
                                                                    <a href="javascript:void(0);"
                                                                        class="text-danger fw-semibold"
                                                                        onclick="hideFullText({{ $item->id }})">Show
                                                                        Less</a>
                                                                    @endif
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
                                                        <h4 class="text-start fw-bold mb-4 mt-3">Installments
                                                        </h4>
                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-bordered table-striped table-hover text-nowrap">
                                                                <thead>
                                                                    <tr class="text-center">
                                                                        <th>Entry Date</th>
                                                                        <th>Cheque No</th>
                                                                        <th>Bank</th>
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
                                                                        <td>{{ $installment->chequeno }}</td>
                                                                        <td>{{ $installment->bankName }}</td>
                                                                        <td>
                                                                            @if($installment->data1 > 0)
                                                                            <del>Rs {{
                                                                                number_format($installment->amount, 2)
                                                                                }}</del><br>
                                                                            <strong>Rs {{
                                                                                number_format($installment->amount -
                                                                                $installment->data1, 2)
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
                                                                            <span
                                                                                class="text-success fw-semibold">Complete</span>
                                                                            @elseif($daysExceeded < 0) <span
                                                                                class="text-danger fw-semibold">Overdue
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
                                                        <h4 class="text-start fw-bold mb-4 mt-3">
                                                            Transactions</h4>
                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-bordered table-striped table-hover table-sm">
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
                                                                    $item->client_id)->where('pc_id', $item->id)->get()
                                                                    as $transaction)
                                                                    <tr class="text-center">
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
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Follow-up History Modal -->
                                <div class="modal fade" id="followupHistoryModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="followupHistoryLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content rounded-3 shadow-lg">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold"
                                                    id="followupHistoryLabel{{ $item->id }}">
                                                    <i class="fas fa-history me-2"></i> Follow-up History
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                @php $followups = $item->followups ?? collect(); @endphp
                                                @if ($followups->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Follow-up Date</th>
                                                                <th>Original Due Date</th>
                                                                <th>Comments</th>
                                                                <th>Status</th>
                                                                <th>Follow-up Count</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($followups as $followup)
                                                            <tr class="text-center">
                                                                <td>{{ $followup->followup_date ?
                                                                    \Carbon\Carbon::parse($followup->followup_date)->format('M
                                                                    d, Y') : 'N/A' }}</td>
                                                                <td>{{ $followup->original_due_date ?
                                                                    \Carbon\Carbon::parse($followup->original_due_date)->format('M
                                                                    d, Y') : 'N/A' }}
                                                                </td>
                                                                <td>{{ $followup->comments ?? 'N/A' }}</td>
                                                                <td>{{ ucfirst($followup->status ?? 'unknown') }}</td>
                                                                <td>{{ $followup->followup_count ?? 0 }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @else
                                                <p class="text-center text-muted">No follow-up history available.</p>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Create Follow-up Modal -->
                                <div class="modal fade" id="newfollowupModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="followupLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content rounded-3 shadow-lg">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold" id="followupLabel{{ $item->id }}">
                                                    <i class="fas fa-plus-circle me-2"></i> Create Follow-up
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <h5 class="fw-bold mb-3">New Follow-up</h5>
                                                <form action="{{ route('electrical.pdcs.followup', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="followup_date"
                                                            class="form-label fw-semibold">Follow-up
                                                            Date</label>
                                                        <input type="date" name="followup_date" id="followup_date"
                                                            class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="comments" class="form-label fw-semibold">Follow-up
                                                            Comments</label>
                                                        <textarea name="comments" id="comments"
                                                            placeholder="Enter follow-up reason" class="form-control"
                                                            rows="4" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Create
                                                            Follow-up</button>
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Update PDC Modal -->
                                <div class="modal fade" id="updateModel{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="updateModelLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content rounded-3 shadow-lg">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold" id="updateModelLabel{{ $item->id }}">
                                                    <i class="fas fa-edit me-2"></i> Update PDC
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <form action="{{ route('electrical.pdc.update', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label for="name" class="form-label fw-semibold">Party
                                                                Name</label>
                                                            <input type="text" name="name" value="{{ $item->name }}"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="commitment_amount"
                                                                class="form-label fw-semibold">PDC
                                                                Amount</label>
                                                            <input type="number" name="commitment_amount"
                                                                value="{{ $item->commitment_amount }}"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="cheque_number"
                                                                class="form-label fw-semibold">Cheque
                                                                Number</label>
                                                            <input type="text" name="cheque_number"
                                                                value="{{ $item->cheque_number }}" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="bank_name" class="form-label fw-semibold">Bank
                                                                Name</label>
                                                            <input type="text" name="bank_name"
                                                                value="{{ $item->bank_name }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="cheque_date" class="form-label fw-semibold">PDC
                                                                Date</label>
                                                            <input type="date" name="cheque_date"
                                                                value="{{ $item->cheque_date }}" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="agent_id"
                                                                class="form-label fw-semibold">Salesman</label>
                                                            <select name="agent_id" id="agent_id" class="form-control"
                                                                required>
                                                                <option value="">Select Salesman</option>
                                                                @foreach ($agents as $agent)
                                                                <option value="{{ $agent->id }}" {{ $item->agent_id ==
                                                                    $agent->id ? 'selected' : '' }}>
                                                                    {{ $agent->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="status"
                                                                class="form-label fw-semibold">Status</label>
                                                            <select name="status" id="status" class="form-control"
                                                                required>
                                                                <option value="">Select Status</option>
                                                                @php $statuses = ['Pending', 'Complete', 'Overdue'];
                                                                @endphp
                                                                @foreach ($statuses as $status)
                                                                <option value="{{ $status }}" {{ $item->status ==
                                                                    $status ?
                                                                    'selected' : '' }}>
                                                                    {{ $status }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="remarks" class="form-label fw-semibold">PDC
                                                                Details</label>
                                                            <textarea name="remarks" id="remarks"
                                                                placeholder="Enter PDC details" class="form-control"
                                                                rows="4" required>{{ $item->remarks }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success"><i
                                                                class="fas fa-save me-2"></i> Save
                                                            Changes</button>
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete PDC Modal -->
                                <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-3 shadow-lg">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $item->id }}">
                                                    <i class="fas fa-trash-alt me-2"></i> Confirm Deletion
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="mb-0">Are you sure you want to delete this PDC? This action
                                                    cannot be
                                                    undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('electrical.pdc.destroy', $item->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Partial Payment Modal -->
                                <div class="modal fade" id="payModal{{ $installment->id }}" tabindex="-1"
                                    aria-labelledby="payModalLabel{{ $installment->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-3 shadow-lg">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold"
                                                    id="payModalLabel{{ $installment->id }}">
                                                    <i class="fas fa-money-bill-wave me-2"></i> Partial Payment
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <form action="{{ route('pdcInstallments.pay', $installment->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="installment_id"
                                                        value="{{ $installment->id }}">
                                                    <input type="hidden" name="pc_id" value="{{ $item->id }}">
                                                    <div class="mb-3">
                                                        <label for="pay_amount" class="form-label fw-semibold">Amount to
                                                            Pay</label>
                                                        <input type="number" class="form-control" name="pay_amount"
                                                            placeholder="Enter Amount" min="1"
                                                            max="{{ $installment->amount - $installment->paid_amount }}"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="remarks"
                                                            class="form-label fw-semibold">Remarks</label>
                                                        <textarea class="form-control" name="remarks" rows="3"
                                                            placeholder="Enter remarks"></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Confirm
                                                            Payment</button>
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4" style="background: white">
            {{$pdcs->appends(request()->query())->links()}}
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    $(function () {
        $("
#EmployeeTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,

 "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#EmployeeTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection