@extends('layouts.electronicsApp',['page' => 'Pdcs'])


@section('content')
<section class="content-header d-flex gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('electronics.dashboard') }}">Home</a></li>
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
<form action="{{ route('electronics.pdc.create') }}" method="get" id="filterForm" class="d-flex flex-column gap-2">

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
                                                                window.location.href = "{{ route('electronics.pdc.create') }}"; // Redirect to show all data
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


                                                                            ws['!cols'] = Array.from({ length: table.rows[0].cells.length }, () => ({ wch: 15 }));

                                                                            XLSX.utils.book_append_sheet(wb, ws, "Employee Data");
                                                                            XLSX.writeFile(wb, 'Pdc-Employee-Data-Excel.xlsx');
                                                                        }

                                                                        function exportStyledPDF() {
                                                                            const { jsPDF } = window.jspdf;
                                                                            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape mode for better column spacing


                                                                            doc.setFontSize(18);
                                                                            doc.text("PDC Data Report", 14, 15);


                                                                            doc.setFontSize(12);
                                                                            doc.text("This report contains pdcs-related financial data.", 14, 25);


                                                                            const table = document.getElementById("addEmployeeTable");
                                                                            const headers = [];
                                                                            const data = [];


                                                                            table.querySelectorAll("thead tr th").forEach(th => {
                                                                                headers.push(th.innerText);
                                                                            });


                                                                            table.querySelectorAll("tbody tr").forEach(tr => {
                                                                                const rowData = [];
                                                                                tr.querySelectorAll("td").forEach((td, index) => {
                                                                                    if (!td.querySelector('.modal') && index !== tr.cells.length - 1) { // Exclude modal and actions
                                                                                        rowData.push(td.innerText);
                                                                                    }
                                                                                });
                                                                                data.push(rowData);
                                                                            });


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


                                                                            const finalY = doc.lastAutoTable.finalY + 10;
                                                                            doc.setFontSize(12);
                                                                            doc.text("Generated on: " + new Date().toLocaleDateString(), 14, finalY);


                                                                            doc.save('Pdc-Employee-Data-Pdf.pdf');
                                                                        }

                                                                        // Event Listeners
                                                                        document.getElementById('exportExcelButton').addEventListener('click', exportToExcel);
                                                                        document.getElementById('exportPdfButton').addEventListener('click', exportStyledPDF);
</script>

<table style="display: none;" id="addEmployeeTable" class="table table-bordered table-striped">
    <thead>
        <tr class="text-center">
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
        <!-- Table Displaying PDC Data -->
        <table id="EmployeeTable" class="table table-bordered table-striped">
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
                    $installment = $item->installments()->where('status', '!=',
                    'Complete')->first();

                    $isCompleted = !$installment; // If no pending installment exists, mark

                    $dueDate = $installment?->due_date ?
                    \Carbon\Carbon::parse($installment->due_date) : null;

                    $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
                    false) : null;
                    @endphp

                    <td>
                        @if ($isCompleted)
                        <h6 class="text-success pt-2 align-center">Completed</h6>
                        @elseif ($dueDate)
                        @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days @elseif($remainingDays==0)
                            Due Today @else {{ $remainingDays }} days remaining @endif @else <span class="text-warning">
                            No Due
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
                        <div class="btn-group" role="group" aria-label="Actions">
                            <button type="button" class="text-primary bg-transparent border-0" title="View"
                                data-toggle="modal" data-target="#viewModel{{ $item->id }}">
                                <i class="fas fa-eye fa-lg"></i> </button>

                            <div class="modal fade" id="viewModel{{ $item->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="viewModelLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <div
                                            class="modal-header d-flex justify-content-between align-items-center gap-3">
                                            <h5 class="modal-title" id="viewModelLabel{{ $item->id }}">PDC Details</h5>
                                            <button type="button" class="btn-close" data-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="card border-0">
                                                <div class="card-header text-start">
                                                    <h5 class="card-title mb-0">Post Dated Cheque's Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-4">
                                                        <div class="col-md-12">
                                                            <div class="row g-3">
                                                                <!-- Name -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">Party Name</h6>
                                                                    <p class="fw-medium">{{ $item->client->name }}</p>
                                                                </div>
                                                                <!-- Commitment Amount -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">PDC Amount</h6>
                                                                    <p class="fw-medium">{{
                                                                        number_format($item->commitment_amount, 2) }}
                                                                    </p>
                                                                </div>
                                                                <!-- Received Amount -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">Received Amount</h6>
                                                                    <p class="fw-medium">
                                                                        @php
                                                                        $totalPaidAmount =
                                                                        $item->transactions->where('pc_id',
                                                                        $item->id)->sum('pay_amount');
                                                                        @endphp
                                                                        {{ number_format($totalPaidAmount, 2) }}
                                                                    </p>
                                                                </div>
                                                                <!-- Pending Amount -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">Pending Amount</h6>
                                                                    <p class="fw-medium">
                                                                        @php
                                                                        $totalPendingAmount = $item->commitment_amount -
                                                                        $totalPaidAmount;
                                                                        @endphp
                                                                        {{ number_format($totalPendingAmount, 2) }}
                                                                    </p>
                                                                </div>
                                                                <!-- Agent -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">Salesman</h6>
                                                                    <p class="fw-medium">{{ $item->agent->name ?? 'No
                                                                        Salesman Assigned' }}</p>
                                                                </div>
                                                                <!-- PDC Date -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">PDC Date</h6>
                                                                    <p class="fw-medium">
                                                                        @php
                                                                        $oldestIncompleteInstallment =
                                                                        $item->installments
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
                                                                <!-- Department -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">Department</h6>
                                                                    <p class="fw-medium">{{ $item->department }}</p>
                                                                </div>
                                                                <!-- Status -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">Status</h6>
                                                                    <p>
                                                                        <span
                                                                            class="badge {{ $item->status === 'Complete' ? 'bg-success' : ($item->status === 'Overdue' ? 'bg-danger' : 'bg-warning') }} text-white py-2 px-3">
                                                                            {{ $item->status }}
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                                <!-- Entry Date -->
                                                                <div class="col-md-2">
                                                                    <h6 class="text-muted">Entry Date</h6>
                                                                    <p class="fw-medium">{{
                                                                        $item->created_at->format('Y-m-d') }}</p>
                                                                </div>
                                                                <!-- Commitment Details -->
                                                                @php
                                                                $words = explode(' ', $item->remarks);
                                                                $preview = implode(' ', array_slice($words, 0, 3));
                                                                @endphp
                                                                <div class="col-md-5">
                                                                    <h6 class="text-muted mb-2">Commitment Details</h6>
                                                                    <div class="card border-0">
                                                                        <div class="card-body py-2">
                                                                            <p id="shortText{{ $item->id }}"
                                                                                class="text-truncate"
                                                                                style="max-height: 4.5rem; overflow: hidden;"
                                                                                data-bs-toggle="tooltip"
                                                                                title="{{ $item->remarks }}">
                                                                                {{ $preview }}...
                                                                                <a href="javascript:void(0);"
                                                                                    class="fw-semibold text-primary"
                                                                                    onclick="showFullText({{ $item->id }})">
                                                                                    More
                                                                                </a>
                                                                            </p>
                                                                            <div id="fullText{{ $item->id }}"
                                                                                class="d-none">
                                                                                <p class="mb-2">{{ $item->remarks }}</p>
                                                                                <a href="javascript:void(0);"
                                                                                    class="fw-semibold text-danger"
                                                                                    onclick="hideFullText({{ $item->id }})">
                                                                                    Less
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
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
                                                        </div>
                                                    </div>

                                                    <!-- Installments Section -->
                                                    <div class="text-center my-2">
                                                        <h4 class="fw-bold card-header text-start">Installments</h4>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped table-hover">
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
                                                                    <td>{{ $installment->created_at->format('Y-m-d') }}
                                                                    </td>
                                                                    <td>{{ $installment->chequeno }}</td>
                                                                    <td>{{ $installment->bankName }}</td>
                                                                    <td>
                                                                        @if($installment->data1 > 0)
                                                                        <del>{{ number_format($installment->amount, 2)
                                                                            }}</del><br>
                                                                        <strong>{{ number_format($installment->amount -
                                                                            $installment->data1, 2)
                                                                            }}</strong>
                                                                        @else
                                                                        {{ number_format($installment->amount, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $installment->due_date }}</td>
                                                                    <td>{{ $installment->status }}</td>
                                                                    <td>
                                                                        @php
                                                                        $dueDate =
                                                                        \Carbon\Carbon::parse($installment->due_date);
                                                                        $now = \Carbon\Carbon::today();
                                                                        $daysExceeded = $now->diffInDays($dueDate,
                                                                        false);
                                                                        @endphp
                                                                        @if($installment->status === 'Complete')
                                                                        <h6 class="text-success mb-0">Complete</h6>
                                                                        @elseif($daysExceeded < 0) <h6
                                                                            class="text-danger mb-0">Overdue by {{
                                                                            abs($daysExceeded) }} days</h6>
                                                                            @else
                                                                            <h6 class="text-dark mb-0">{{ $daysExceeded
                                                                                }}
                                                                                days left</h6>
                                                                            @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Transactions Section -->
                                                    <div class="text-center my-2">
                                                        <h4 class="fw-bold card-header text-start">Transactions</h4>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped table-hover">
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
                                                                    <td>{{ $transaction->client->name ?? 'N/A' }}</td>
                                                                    <td class="fw-medium text-success">Rs. {{
                                                                        number_format($transaction->pay_amount, 2) }}
                                                                    </td>
                                                                    <td>{{ $transaction->agent->name ?? 'N/A' }}</td>
                                                                    <td>{{ $transaction->remarks ?? 'No Remarks' }}</td>
                                                                    <td>{{ $transaction->created_at->format('Y-m-d') }}
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="8" class="text-center">No transactions
                                                                        found</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">Close</button>
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