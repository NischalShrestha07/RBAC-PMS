@extends('layouts.app',['page' => 'Pdcs'])

@section('content')
<section class="content-header py-2">
    <div class="d-flex gap-3 justify-content-between position-relative">
        <!-- Breadcrumb -->
        <div class="d-flex align-items-center breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a class="link-primary fw-bold text-decoration-none" href="{{ route('dashboard.unified') }}">Home</a>
            </li>
            <li class="breadcrumb-item active">PDCs</li>
        </div>

        <!-- Export Buttons -->
        @can('view financial reports')
        <div class="d-flex gap-2">
            <button class="border-0 bg-transparent p-0" id="exportExcelButton">
                <img src="{{ asset('admincss/img/excel.png') }}" alt="Export Excel" width="30">
            </button>

            <button class="border-0 bg-transparent p-0" id="exportPdfButton">
                <img src="{{ asset('admincss/img/pdf.png') }}" alt="Export PDF" width="30">
            </button>
        </div>
        @endcan

        <!-- Alerts Top-Right -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
            @if (session('success'))
            <div class="alert alert-success text-white bg-success alert-dismissible custom-alert  fade-in" role="alert"
                style="white-space: normal; word-wrap: break-word;">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade-in" role="alert"
                style="white-space: normal; word-wrap: break-word;">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
            @endif
        </div>
    </div>
</section>

<style>
    .ff-filter>* {
        flex: 1 1 8rem;
    }
</style>
<form action="{{ route('pdcs.index') }}" method="get" id="filterForm" class="d-flex flex-column gap-2">

    <div class="ff-filter d-flex flex-wrap gap-2 text-nowrap">

        <button type="button" id="clearFilters" class="btn btn-sm btn-secondary px-3">All</button>

        <!-- Date Filter -->
        <select class="form-control custom-select mr-1" id="filterDate" name="filter_date">
            <option value="">Pdc Date</option>
            <option value="today" {{ request('filter_date')=='today' ? 'selected' : '' }}>Today</option>
            <option value="this_week" {{ request('filter_date')=='this_week' ? 'selected' : '' }}>This Week</option>
            <option value="this_month" {{ request('filter_date')=='this_month' ? 'selected' : '' }}>This Month
            </option>

            <option value="custom" {{ request('filter_date')=='custom' ? 'selected' : '' }}>Custom</option>
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
            <option value="Pending" {{ request('filter_status')=='Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Overdue" {{ request('filter_status')=='Overdue' ? 'selected' : '' }}>Overdue</option>
        </select>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-sm btn-success px-2">Apply</button>

        @can('create pdcs')
        <a href="{{ route('pdcs.create') }}" class="btn btn-success ml-1">Add New Pdc</a>
        @endcan
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
                                        document.getElementById('clearFilters').addEventListener('click', function() {
                                            document.getElementById('filterForm').reset(); // Reset all inputs
                                            window.location.href = "{{ route('pdcs.index') }}"; // Redirect to show all data
                                        });

                                        document.getElementById('filterDate').addEventListener('change', function() {
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

{{-- party modal --}}
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- Increased modal size for better readability -->
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center gap-3 bg-primary text-white">
                <h5 class="modal-title" id="addClientModalLabel"><i class="fas fa-user-plus"></i>
                    Add New Party</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"> </button>
            </div>

            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Party Name"
                                    required>
                                <label for="name">Party Name</label>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="address" name="address"
                                    placeholder="Party Address" required>
                                <label for="address">Party Address</label>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="Party Phone">
                                <label for="phone">Party Phone</label>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Party Email">
                                <label for="email">Party Email</label>
                            </div>
                        </div>

                        <!-- Company -->
                        {{-- <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="company" name="company"
                                    placeholder="Party Company" required>
                                <label for="company">Party Company</label>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        Save Party
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<table style="display: none;" id="addEmployeeTable" class="table table-bordered table-striped">
    <thead>
        <tr class="text-center">
            <th>Entry Date</th>
            <th>Party Name</th>
            <th>Address</th>
            <th>Total Pdc Amount</th>
            <th>Installment Amount</th>
            <th>Pdc Age(Day)</th>
            <th>Salesman</th>
            <th>Department</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($pdcs as $item)
        <tr>
            <td>{{ $item->created_at->format('Y-m-d') }}</td>
            <td>{{ $item->client->name ?? 'N/A' }}</td>
            <td>{{ $item->client->address ?? 'N/A' }}</td>
            <td>{{ $item->commitment_amount ?? 'N/A' }}</td>
            <td>{{ $item->display_amount ?? 'N/A' }}</td>

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

            @if ($item->status === 'Failed')
            <td class="text-danger pt-2 align-center">Failed</td>
            @elseif ($isCompleted)
            <td class="text-success pt-2 align-center">Completed</td>
            @elseif ($dueDate)
            @if ($remainingDays < 0) <td>Overdue by {{ abs($remainingDays) }} days</td>
                @elseif ($remainingDays==0) <td>Due Today</td> @else <td>{{ $remainingDays
                    }} days remaining</td>
                @endif @else <td class="text-warning">No Due
                    Date</td>
                @endif
                <td>{{ $item->agent->name ?? 'No Salesman Assigned' }}</td>
                <td>{{ $item->department }}</td>
                <td>{{ $item->status ?? 'N/A' }}</td>
                <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    function exportToExcel() {
                                        var table = document.getElementById('addEmployeeTable'); // Ensure table ID matches
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
                                        XLSX.writeFile(wb, 'Pdc-Employee-Data-Excel.xlsx');
                                    }

                                    function exportStyledPDF() {
                                        const {
                                            jsPDF
                                        } = window.jspdf;
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
                                        doc.save('Pdc-Employee-Data-Pdf.pdf');
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
            <h5 class="card-title mb-0">Pdcs</h5>
        </div>
        <style>
            table td {
                vertical-align: middle !important;
            }
        </style>
        <div class="table-responsive">
            <table id="EmployeeTable" class="table table-bordered table-striped text-center text-nowrap mb-0">
                <thead>
                    <tr class="text-center">
                        <th>Entry Date</th>
                        <th>Party </th>
                        <th>Address</th>
                        <th>Total Pdc Amount</th>
                        <th>Installment Amount</th>
                        <th>Pdc Age</th>
                        <th>Salesman</th>
                        <th>Status</th>
                        @canany(['edit pdcs', 'delete pdcs', 'view pdcs'])
                        <th>Actions</th>
                        @endcanany
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pdcs as $item)
                    <tr class="text-center">
                        <td>{{ $item->created_at->format('Y-m-d') }}</td>
                        <td>{{ $item->client->name ?? 'N/A' }}</td>
                        <td>{{ $item->client->address ?? 'N/A' }}</td>
                        <td>{{ $item->commitment_amount }}</td>
                        <td>{{ $item->display_amount ?? 'N/A' }}</td>
                        @php
                        // Fetch the first installment that is not "Complete"
                        $installment = $item->installments()->where('status', '!=',
                        'Complete')->first();

                        // Check if all installments are completed
                        $isCompleted = !$installment; // If no pending installment exists, mark

                        // Determine the due date
                        $dueDate = $installment?->due_date ?
                        \Carbon\Carbon::parse($installment->due_date) : null;

                        $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
                        false) : null;
                        @endphp

                        <td>
                            @if ($item->status === 'Failed')
                            <h6 class="text-danger pt-2 align-center">Failed</h6>
                            @elseif ($isCompleted)
                            <h6 class="text-success pt-2 align-center">Completed</h6>
                            @elseif ($dueDate)
                            @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                                @elseif($remainingDays==0) Due Today @else {{ $remainingDays }} days remaining @endif
                                @else <span class="text-warning">
                                No Due
                                Date</span>
                                @endif
                        </td>
                        <td>{{ $item->agent->name ?? 'No Salesman Assigned' }}</td>
                        <td>
                            <span class="badge badge-sm
                                @if ($item->status == 'Complete') bg-success
                                @elseif($item->status == 'Overdue') bg-danger
                                @elseif($item->status == 'Failed') bg-secondary
                                @else bg-warning @endif"
                                style="pointer-events: none; cursor: default; padding: 6px 10px; color: white;">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        @canany(['edit pdcs', 'delete pdcs', 'view pdcs'])
                        <td>
                            <div class="btn-group">
                                @can('edit pdcs')
                                <a href="{{ route('pdcs.edit', $item->id) }}" class="btn text-success" title="Edit">
                                    <i class="fas fa-pencil-alt fa-lg text-success"></i>
                                </a>
                                @endcan

                                @can('view pdcs')
                                <a href="{{ route('pdcs.show', $item->id) }}" class="btn text-primary" title="View">
                                    <i class="fas fa-eye fa-lg text-primary"></i>
                                </a>
                                @endcan

                                @can('delete pdcs')
                                <button type="button" class="btn text-danger" title="Delete" data-toggle="modal"
                                    data-target="#deleteModal{{ $item->id }}">
                                    <i class="fas fa-trash-alt fa-lg text-danger"></i>
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
                                                <p class="mb-2 fs-5">Are you sure you want to
                                                    delete
                                                    this item?</p>
                                                <p class="text-muted small">This action cannot
                                                    be
                                                    undone.</p>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-center">
                                                <button type="button" class="btn btn-secondary px-4 py-2"
                                                    data-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <form action="{{ route('pdcs.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger px-4 py-2">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                            </div>
                        </td>
                        @endcanany
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4" style="background: white;">
            {{ $pdcs->links() }}
        </div>
    </div>
</div>

@endsection

@section('customJs')
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
@endsection
