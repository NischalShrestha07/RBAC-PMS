@extends('layouts.superApp', ['page' => 'Commitments'])

@section('content')
<section class="content-header py-2">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb mb-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.unified') }}" class="text-primary fw-bold text-decoration-none">
                        Home
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Commitments</li>
            </ol>
        </nav>

        <!-- Export Buttons -->
        @can('view financial reports')
        <div class="d-flex gap-1">
            <button type="button" id="exportExcelButton" class="text-success border-0 bg-transparent"
                title="Export to Excel">
                <img src="{{ asset('admincss/img/excel.png') }}" alt="Excel" width="30">
            </button>
            <button type="button" id="exportPdfButton" class="text-danger border-0 bg-transparent"
                title="Export to PDF">
                <img src="{{ asset('admincss/img/pdf.png') }}" alt="PDF" width="30">
            </button>
        </div>
        @endcan

    </div>

    <!-- Alerts Top-Right -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
        @if (session('success'))
        <div class="alert alert-success text-white bg-success alert-dismissible fade show custom-alert mb-2"
            role="alert" style="white-space: normal; word-wrap: break-word;">
            <strong style="color: white">Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger text-white bg-danger alert-dismissible fade show custom-alert mb-2" role="alert"
            style="white-space: normal; word-wrap: break-word;">
            <strong style="color: white">Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>
</section>

<style>
    .ff-filter>* {
        flex: 1 1 8rem;
    }
</style>
<form action="{{ route('commitments.index') }}" method="get" id="filterForm" class="d-flex flex-column gap-2">
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

        <!-- Department Filter -->
        <select class="form-control custom-select mr-1" id="filterDepartment" name="filter_department">
            <option value="">Department</option>
            @foreach($departments as $department)
            <option value="{{ $department->id }}" {{ request('filter_department')==$department->id ? 'selected' : '' }}>
                {{ $department->name }}
            </option>
            @endforeach
        </select>

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

        @can('create commitments')
        <a href="{{ route('commitments.create') }}" class="btn btn-success">Add
            New Commitment</a>
        @endcan
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
    document.getElementById('clearFilters').addEventListener('click', function () {
                                            document.getElementById('filterForm').reset();
                                            window.location.href = "{{ route('commitments.index') }}";
                                        });

                                        const filterDate = document.getElementById('filterDate');
                                        const customDateRange = document.getElementById('customDateRange');

                                        filterDate.addEventListener('change', function () {
                                            if (this.value === 'custom') {
                                                customDateRange.classList.remove('d-none');
                                                setTimeout(() => customDateRange.classList.add('show'), 10); // Small delay for smooth transition
                                            } else {
                                                customDateRange.classList.remove('show');
                                                setTimeout(() => customDateRange.classList.add('d-none'), 300); // Match transition duration
                                            }
                                        });

                                        // Initialize visibility based on current selection
                                        if (filterDate.value === 'custom') {
                                            customDateRange.classList.remove('d-none');
                                            customDateRange.classList.add('show');
                                        }
</script>
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
                                                    const {
                                                        jsPDF
                                                    } = window.jspdf;
                                                    const doc = new jsPDF('l', 'mm', 'a4');
                                                    doc.setFontSize(18);
                                                    doc.text("Commitments Data Report", 14, 15);
                                                    doc.setFontSize(12);
                                                    doc.text("This report contains commitments-related financial data.", 14, 25);
                                                    doc.autoTable({
                                                        html: "#commitmentEmployeeTable",
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
                                                        }
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
            <th>Total Commitment</th>
            <th>Installment Amount</th>
            {{-- <th>Received Amount</th>
            <th>Pending Amount</th> --}}
            <th>Commitment Age(Day)</th>
            <th>Salesman</th>
            <th>Department</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($commitments->whereIn('status', ['Pending', 'Overdue']) as $item)
        <tr>
            <td>{{ $item->created_at->format('Y-m-d') }}</td>
            <td>{{ $item->client->name ?? 'N/A' }}</td>
            <td>{{ $item->client->address ?? 'N/A' }}</td>
            <td>{{ $item->total_amount ?? 'N/A' }}</td>
            <td>{{ $item->display_amount ?? 'N/A' }}</td>
            {{-- @php
            $totalPaidAmount = $item->installments->where('status',
            'Complete')->sum('amount');
            $partialPayments = \App\Models\Transactions::where('client_id',
            $item->client_id)->where('installment_id', $item->id)
            ->sum('pay_amount');
            $totalReceived = $totalPaidAmount + $partialPayments;
            $totalAmount = $item->total_amount ?? 0;
            $totalPendingAmount = $totalAmount - $totalReceived;
            @endphp

            <td>{{ number_format($totalReceived, 2) }}</td>
            <td>{{ number_format($totalPendingAmount, 2) }}</td> --}}

            @php
            $installment = $item->installments()->where('status', '!=',
            'Complete')->first();
            $isCompleted = !$installment;
            $dueDate = $installment?->due_date ?
            \Carbon\Carbon::parse($installment->due_date) : null;
            $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
            false) : null;
            @endphp

            <td>@if ($dueDate)@if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                    @elseif($remainingDays==0) Due Today @else {{ $remainingDays }} days remaining @endif @else <span
                    class="text-warning">No Due
                    Date</span>
                    @endif </td>
            <td>{{ $item->agent->name ?? 'N/A' }}</td>
            <td>{{ $item->department ?? 'N/A' }}</td>
            <td>{{ $item->status ?? 'N/A' }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    function exportToExcel() {
                                        var table = document.getElementById('commitmentEmployeeTable'); // Ensure table ID matches
                                        var wb = XLSX.utils.book_new();
                                        var ws = XLSX.utils.table_to_sheet(table, {
                                            raw: true
                                        });

                                        // Adjust column width for better formatting
                                        ws['!cols'] = Array.from({
                                            length: table.rows[0].cells.length
                                        }, () => ({
                                            wch: 15
                                        }));

                                        XLSX.utils.book_append_sheet(wb, ws, "Employee Data");
                                        XLSX.writeFile(wb, 'Commitments-Employee-Data-Excel.xlsx');
                                    }

                                    function exportStyledPDF() {
                                        const {
                                            jsPDF
                                        } = window.jspdf;
                                        const doc = new jsPDF('l', 'mm', 'a4'); // Landscape mode for better column spacing

                                        // Title
                                        doc.setFontSize(18);
                                        doc.text("Commitments Data Report", 14, 15);

                                        // Subtitle
                                        doc.setFontSize(12);
                                        doc.text("This report contains commitment-related financial data.", 14, 25);

                                        // Extract table data, excluding modals
                                        const table = document.getElementById("commitmentEmployeeTable");
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
                                                fontSize: 8,
                                                cellPadding: 1,
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
                                                0: {
                                                    cellWidth: 30
                                                },
                                                1: {
                                                    cellWidth: 40
                                                },
                                                2: {
                                                    cellWidth: 50
                                                },
                                            }
                                        });

                                        // Footer
                                        const finalY = doc.lastAutoTable.finalY + 10;
                                        doc.setFontSize(12);
                                        doc.text("Generated on: " + new Date().toLocaleDateString(), 14, finalY);

                                        // Save PDF
                                        doc.save('Commitments-Employee-Data-Pdf.pdf');
                                    }

                                    // Event Listeners
                                    document.getElementById('exportExcelButton').addEventListener('click', exportToExcel);
                                    document.getElementById('exportPdfButton').addEventListener('click', exportStyledPDF);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.19/jspdf.plugin.autotable.min.js">
</script>
<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Commitments</h5>
        </div>
        <style>
            table td {
                vertical-align: middle !important;
            }
        </style>
        <div class="table-responsive">
            <table id="commitmentEmployeeTable" class="table table-striped table-bordered text-nowrap text-center">
                <thead>
                    <tr class="text-center">
                        <th>Entry Date</th>
                        <th>Party</th>
                        <th>Address</th>
                        <th>Total Commitment</th>
                        <th>Installment Amount</th>
                        {{-- <th>Received Amount</th>
                        <th>Pending Amount</th> --}}
                        <th>Commitment Age(Day)</th>
                        <th>Salesman</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commitments->whereIn('status',
                    ['Pending', 'Overdue']) as $item)
                    <tr class="text-center">
                        <td>{{ $item->created_at->format('Y-m-d') }}
                        </td>
                        <td>{{ $item->client->name ?? 'N/A' }}</td>
                        <td>{{ $item->client->address ?? 'N/A' }}
                        </td>
                        <td>{{ $item->total_amount }}</td>
                        <td>{{ $item->display_amount ?? 'N/A' }}</td>
                        {{-- <td>
                            @php
                            $partialPayments =
                            \App\Models\Transactions::where('client_id',
                            $item->client_id)->where('agent_id',$item->agent_id)->where('pc_id',$item->id)
                            ->sum('pay_amount');
                            $totalReceived = $partialPayments;
                            $totalAmount = $item->total_amount ?? 0;
                            $totalPending = max($totalAmount -
                            $totalReceived, 0);
                            @endphp
                            {{ number_format($totalReceived, 2) }}
                        </td>
                        <td>{{ number_format($totalPending, 2) }}
                        </td> --}}
                        <td>
                            @php
                            $installment =
                            $item->installments()->where('status',
                            '!=',
                            'Complete')->first();
                            $isCompleted = !$installment;
                            $dueDate = $installment?->due_date ?
                            \Carbon\Carbon::parse($installment->due_date)
                            : null;
                            $remainingDays = $dueDate ?
                            today()->startOfDay()->diffInDays($dueDate,
                            false) : null;
                            @endphp
                            @if ($dueDate)
                            @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                                @elseif($remainingDays==0) Due Today @else {{ $remainingDays }} days remaining @endif
                                @else <span class="text-warning">No Due
                                Date</span>
                                @endif
                        </td>
                        <td>{{ $item->agent->name ?? 'No Agent
                            Assigned' }}</td>
                        <td>
                            <span
                                class="badge badge-sm @if ($item->status == 'Overdue') bg-danger @else bg-warning @endif"
                                style="pointer-events: none; cursor: default; padding: 6px 10px; color: white;">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('commitments.edit', $item->id) }}" class="btn text-success"
                                    title="Edit">
                                    <i class="fas fa-pencil-alt fa-lg text-success"></i>
                                </a>
                                <a href="{{ route('commitments.show', $item->id) }}" class="btn text-primary"
                                    title="View">
                                    <i class="fas fa-eye fa-lg text-primary"></i>
                                </a>
                                <button type="button" class="btn" title="Delete" data-toggle="modal"
                                    data-target="#deleteModal{{ $item->id }}">
                                    <i class="fas fa-lg fa-trash-alt text-danger"></i>
                                </button>
                                <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                                        <div class="modal-content shadow-lg rounded">
                                            <div
                                                class="modal-header d-flex justify-content-between align-items-center gap-3 bg-danger text-white">
                                                <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $item->id }}">
                                                    Confirm Deletion
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p class="mb-2 fs-5">
                                                    Are you sure you
                                                    want to delete
                                                    this item?</p>
                                                <p class="text-muted small">
                                                    This action
                                                    cannot be
                                                    undone.</p>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-center">
                                                <button type="button" class="btn btn-secondary px-4 py-2"
                                                    data-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <form action="{{ route('commitments.destroy', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger px-4 py-2">Delete</button>
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
        <div class="mt-4" style="background: white;">
            {{ $commitments->links() }}
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('filterForm').reset();
        window.location.href = "{{ route('commitments.index') }}";
    });

    const filterDate = document.getElementById('filterDate');
    const customDateRange = document.getElementById('customDateRange');

    filterDate.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.classList.remove('d-none');
            setTimeout(() => customDateRange.classList.add('show'), 10);
        } else {
            customDateRange.classList.remove('show');
            setTimeout(() => customDateRange.classList.add('d-none'), 300);
        }
    });

    if (filterDate.value === 'custom') {
        customDateRange.classList.remove('d-none');
        customDateRange.classList.add('show');
    }
</script>
<script>
    $(function() {
        $("#commitmentEmployeeTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#commitmentEmployeeTable_wrapper .col-md-6:eq(0)');
    });
</script>
<script>
    function closeUpdateModalAndOpenFollowup(itemId) {
        $('#updateModel' + itemId).modal('hide');
        setTimeout(function() {
            $('#newfollowupModal' + itemId).modal('show');
        }, 200);
    }

    function closeModalAndOpenFollowup(itemId) {
        $('#updateModel' + itemId).modal('hide');
        setTimeout(function() {
            $('#newfollowupModal' + itemId).modal('show');
        }, 200);
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalAmountInput = document.getElementById('total_amount');
        const paidAmountInput = document.getElementById('paid_amount');
        const pendingAmountInput = document.getElementById('pending_amount');

        if (totalAmountInput && paidAmountInput && pendingAmountInput) {
            function updatePendingAmount() {
                const totalAmount = parseFloat(totalAmountInput.value) || 0;
                const paidAmount = parseFloat(paidAmountInput.value) || 0;
                const pendingAmount = totalAmount - paidAmount;
                pendingAmountInput.value = pendingAmount >= 0 ? pendingAmount.toFixed(2) : 0;
            }

            totalAmountInput.addEventListener('input', updatePendingAmount);
            paidAmountInput.addEventListener('input', updatePendingAmount);
            updatePendingAmount();
        }
    });
</script>
@endsection