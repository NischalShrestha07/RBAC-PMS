@extends('layouts.electronicsApp',['page'=>'Installments'])

@section('content')
<section class="content-header py-2">
    <div class="">
        <div class="row align-items-center">

            <!-- Left: Breadcrumb -->
            <div class="col-md-6">
                <nav aria-label="breadcrumb mb-0">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('electronics.dashboard') }}"
                                class="text-primary fw-bold text-decoration-none">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Installments</li>
                    </ol>
                </nav>
            </div>

            <!-- Right Side -->
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <button class="border-0 bg-transparent" href=" {{ asset('admincss/img/excel.png') }}"
                        id="exportExcelButton">
                        <img src="{{ asset('admincss/img/excel.png') }}" alt="Export Excel" width="30">
                    </button>

                    <button class="border-0 bg-transparent" href="{{ asset('admincss/img/pdf.png') }}"
                        id="exportPdfButton">
                        <img src="{{ asset('admincss/img/pdf.png') }}" alt="Export PDF" width="30">
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<table style="display: none;" id="recordsTable" class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th class="sortable" data-sort="client_name">Party Name <i class="fas fa-sort ms-1"></i></th>
            <th class="sortable" data-sort="amount">Amount <i class="fas fa-sort ms-1"></i></th>
            <th>Paid Amount</th>
            <th>Due Amount</th>
            <th class="sortable" data-sort="due_date">Due Date</th>
            <th>Status</th>
            <th>Type</th>
            <th>Cheque No</th>
            <th>Age(Day)</th>
            <th>Bank Name</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($records as $record)
        <tr>
            @php
            $dueDate = \Carbon\Carbon::parse($record->due_date);
            $today = \Carbon\Carbon::today();

            if ($dueDate->isFuture()) {
            $ageText = $today->diffInDays($dueDate) . " days remaining";
            } elseif ($dueDate->isToday()) {
            $ageText = "Due today";
            } else {
            $ageText = "Overdue by " . $dueDate->diffInDays($today) . " days";
            }
            @endphp
            <td>{{ $record->client_name }}</td>
            <td>{{ number_format($record->amount, 2) }}</td>
            <td>{{ number_format($record->paid_amount, 2) }}</td>
            <td>{{ number_format($record->amount-$record->paid_amount, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($record->due_date)->format('d-m-Y') }}</td>
            <td>{{ $record->status ?? 'N/A' }}</td>
            <td>{{ $record->type }}</td>
            <td>
                @if ($record->type == 'PDC' && isset($record->chequeno))
                {{ $record->chequeno }}
                @else
                <span class="text-muted">—</span>
                @endif
            </td>
            <td>{{ $ageText }}</td>
            <td>
                @if ($record->type == 'PDC' && isset($record->pdc_id))
                {{ $record->bankname }}
                @else
                <span class="text-muted">—</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No records found</td>
        </tr>
        @endforelse
    </tbody>
</table>

<style>
    .ff-filter>* {
        flex: 1 1 8rem;
    }
</style>
<form action="{{ route('electronics.installments_pdcs.index') }}" method="get" id="filterForm"
    class="d-flex flex-column gap-2">
    <div class="ff-filter d-flex flex-wrap gap-2 text-nowrap">
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

        <select name="filter_type" class="form-control">
            <option value="">All Types</option>
            <option value="Commitment" {{ request('filter_type')=='Commitment' ? 'selected' : '' }}>Commitment
            </option>
            <option value="PDC" {{ request('filter_type')=='PDC' ? 'selected' : '' }}>
                PDC</option>
        </select>

        <!-- Status Filter -->
        <select class="form-control custom-select" id="filterStatus" name="filter_status">
            <option value="">Status</option>
            <option value="Pending" {{ request('filter_status')=='Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Complete" {{ request('filter_status')=='Complete' ? 'selected' : '' }}>Complete
            </option>
            <option value="Overdue" {{ request('filter_status')=='Overdue' ? 'selected' : '' }}>Overdue</option>
        </select>

        <!-- Buttons -->
        <button type="submit" class="btn btn-sm btn-success px-3">Apply</button>
    </div>

    <!-- Custom Date Range (Below the Row) -->
    <div id="customDateRange" class="custom-date-range d-none d-flex align-items-center gap-2">
        <large class="text-muted">Custom Date:</large>
        <input style="border-radius: 15px;" type="date" id="start_date" name="start_date" class="form-control "
            value="{{ request('start_date') }}" placeholder="Start Date">
        <large class="text-muted"> to </large>
        <input style="border-radius: 15px;" type="date" id="end_date" name="end_date" class="form-control "
            value="{{ request('end_date') }}" placeholder="End Date">
    </div>
</form>
<script>
    document.getElementById('clearFilters').addEventListener('click', function () {
                                    document.getElementById('filterForm').reset();
                                    window.location.href = "{{ route('electronics.installments_pdcs.index') }}";
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.19/jspdf.plugin.autotable.min.js">
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
                                            var table = document.getElementById('recordsTable');
                                            var wb = XLSX.utils.book_new();
                                            var ws = XLSX.utils.table_to_sheet(table);
                                            XLSX.utils.book_append_sheet(wb, ws, "Commitments");
                                            XLSX.writeFile(wb, 'Commitments-PDC.xlsx');
                                        }

                                        function exportStyledPDF() {
                                            const {
                                                jsPDF
                                            } = window.jspdf;
                                            const doc = new jsPDF('l', 'mm', 'a4');
                                            doc.setFontSize(18);
                                            doc.text("Commitments PDC Installments Report", 14, 15);
                                            doc.setFontSize(12);
                                            doc.text("This report contains commitments-pdc-related Installments data.", 14, 25);
                                            doc.autoTable({
                                                html: "#recordsTable",
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
                                            doc.save('Commitments-PDC-Details.pdf');
                                        }

                                        document.getElementById('exportExcelButton').addEventListener('click', exportToExcel);
                                        document.getElementById('exportPdfButton').addEventListener('click', exportStyledPDF);
</script>

<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Installments</h5>
        </div>
        <div class="table-responsive">
            <table id="addEmployeeTable" class="table table-bordered table-striped" style="text-align: center;">
                <thead>
                    <tr>
                        <th data-sort="client_name">Party
                        </th>
                        <th data-sort="amount">Installment Amount</th>
                        <th>Paid Amount</th>
                        <th>Due Amount</th>
                        <th data-sort="due_date">Due Date</th>
                        <th>Cheque No</th>
                        <th>Bank Name</th>
                        <th>Age(Day)</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                    @php
                    $dueDate = \Carbon\Carbon::parse($record->due_date);
                    $today = \Carbon\Carbon::today();

                    if ($dueDate->isFuture()) {
                    $ageText = $today->diffInDays($dueDate) . " days remaining";
                    } elseif ($dueDate->isToday()) {
                    $ageText = "Due today";
                    } else {
                    $ageText = "Overdue by " . $dueDate->diffInDays($today) . " days";
                    }
                    @endphp
                    <tr>
                        <td>{{ $record->client_name }}</td>
                        <td>{{ number_format($record->amount, 2) }}</td>
                        <td>{{ number_format($record->paid_amount, 2) }}</td>
                        <td>{{ number_format($record->amount-$record->paid_amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->due_date)->format('d-m-Y') }}</td>

                        <td>
                            @if ($record->type == 'PDC')
                            {{ $record->chequeno ?? 'N/A' }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($record->type == 'PDC')
                            {{ $record->bankname ?? 'N/A' }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $ageText }}</td>
                        <td>
                            <span
                                class="badge p-2 w-100 text-white fw-bold
                                                                                                                                                                    {{ $record->type == 'Commitment' ? 'bg-primary' : 'bg-success' }}">
                                {{ $record->type }}
                            </span>
                        </td>
                        <td>
                            <span
                                class="badge p-2 w-100 text-white fw-bold
                                                                                                                        {{ $record->status == 'Complete' ? 'bg-success' :
                                                                                                                           ($record->status == 'Overdue' ? 'bg-danger' : 'bg-warning') }}">
                                {{ $record->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No records found</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <div class="mt-2" style="background: white;">
            {{ $records->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>


<script>
    // Function to close update modal and open follow-up modal
        function closeUpdateModalAndOpenFollowup(itemId) {
            // Close update modal
            $('#updateModel' + itemId).modal('hide');

            // Give a slight delay before opening follow-up modal to ensure smooth transition
            setTimeout(function() {
                $('#newfollowupModal' + itemId).modal('show');
            }, 200); // 200ms delay
        }
</script>
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
    // Function to close modal1 (updateModel) and open modal2 (newfollowupModal)
        function closeModalAndOpenFollowup(itemId) {
            // Close modal1 (updateModel)
            $('#updateModel' + itemId).modal('hide');

            // Give a slight delay before opening modal2 to ensure smooth transition
            setTimeout(function() {
                $('#newfollowupModal' + itemId).modal('show');
            }, 200); // Delay in milliseconds (200ms)
        }
</script>
<script>
    document.getElementById('total_amount').addEventListener('input', calculatePendingAmount);
        document.getElementById('paid_amount').addEventListener('input', calculatePendingAmount);

        function calculatePendingAmount() {
            const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
            const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
            const pendingAmount = totalAmount - paidAmount;
            document.getElementById('pending_amount').value = pendingAmount >= 0 ? pendingAmount : 0;
        } <
        />


        <
        script >
            document.addEventListener('DOMContentLoaded', function() {
                const totalAmountInput = document.getElementById('total_amount');
                const paidAmountInput = document.getElementById('paid_amount');
                const pendingAmountInput = document.getElementById('pending_amount');

                function updatePendingAmount() {
                    const totalAmount = parseFloat(totalAmountInput.value) || 0; // If NaN, fallback to 0
                    const paidAmount = parseFloat(paidAmountInput.value) || 0; // If NaN, fallback to 0
                    const pendingAmount = totalAmount - paidAmount;

                    // Update the pending amount in the form
                    pendingAmountInput.value = pendingAmount.toFixed(2); // Update the pending amount
                }

                // Add event listeners to update pending amount on input change
                totalAmountInput.addEventListener('input', updatePendingAmount);
                paidAmountInput.addEventListener('input', updatePendingAmount);
     updatePendingAmount();
            });
</script>
@endsection