@extends('layouts.superApp',['page'=>'ChequeLocation'])

@section('content')
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-3 bg-white">
    <div>
        <h2 class="fw-bold">
            Cheque Locations
        </h2>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.unified') }}"
                    class="link-primary text-decoration-none fw-bold">Home</a>
            </li>
            <li class="breadcrumb-item active">Cheque Locations</li>
        </ol>
    </div>

    <!-- Alerts Top-Right -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
        @if (session('success'))
        <div class="alert alert-success text-white bg-success alert-dismissible custom-alert  fade-in" role="alert"
            style="white-space: normal; word-wrap: break-word;">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade-in" role="alert"
            style="white-space: normal; word-wrap: break-word;">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>

    <div class="d-flex align-items-center text-nowrap gap-2">
        <div class="input-group">
            <input type="text" class="form-control rounded-start shadow-sm" placeholder="Search cheques..."
                id="searchInput">
            <span class="input-group-text rounded-end">
                <i class="fas fa-search"></i>
            </span>
        </div>

        <button class="border-0 bg-transparent p-0" href=" {{ asset('admincss/img/excel.png') }}"
            id="exportExcelButton">
            <img src="{{ asset('admincss/img/excel.png') }}" alt="Export Excel" width="30">
        </button>

        <button class="border-0 bg-transparent p-0" href="{{ asset('admincss/img/pdf.png') }}" id="exportPdfButton">
            <img src="{{ asset('admincss/img/pdf.png') }}" alt="Export PDF" width="30">
        </button>
        <a href="{{ route('cheque.locations.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add Cheque
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
                    // Auto-dismiss success alert after 5 seconds
                    const successAlert = document.getElementById('successAlert');
                    if (successAlert) {
                        setTimeout(() => {
                            successAlert.classList.remove('show');
                            successAlert.classList.add('fade');
                            setTimeout(() => successAlert.remove(), 300); // Remove from DOM after fade out
                        }, 4000); // 5 seconds
                    }

                    // Auto-dismiss error alert after 5 seconds
                    const errorAlert = document.getElementById('errorAlert');
                    if (errorAlert) {
                        setTimeout(() => {
                            errorAlert.classList.remove('show');
                            errorAlert.classList.add('fade');
                            setTimeout(() => errorAlert.remove(), 300); // Remove from DOM after fade out
                        }, 4000); // 5 seconds
                    }
                });
</script>

<!-- Summary Cards -->
<div class="d-flex gap-3 mb-3">
    <div class="card w-100 p-3">
        <h6 class="text-muted mb-1">Total Cheques</h6>
        <h4 class="fw-bold mb-0">{{ $chequeLocations->count() }}</h4>
    </div>

    <div class="card w-100 p-3">
        <h6 class="text-muted mb-1">Total Amount</h6>
        <h4 class="fw-bold mb-0">{{ number_format($chequeLocations->sum('amount'),
            2) }}</h4>
    </div>


</div>

<!-- Table Section -->
<div class="table-wrapper card">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0">Cheque List</h5>
        <div class="d-flex gap-2">
            <select style="display: none" class="form-select form-control form-select shadow-sm" id="statusFilter">
                <option value="">Cheque Locations</option>
                <option value="In Ktm Bank">In Ktm Bank</option>
                <option value="With Salesman">With Salesman</option>
                <option value="Received but Not Cleared">Received but Not Cleared</option>
                <option value="Received & Cleared">Received & Cleared</option>
            </select>
            <input style="display: none" type="date" class="form-control form-control shadow-sm" id="dateFilter"
                title="Filter by Date">
            <button style="display: none" class="btn btn-outline-teal btn-sm shadow-sm" id="resetFilters"
                title="Reset Filters">
                <i class="fas fa-undo"></i>
            </button>
        </div>
    </div>
    <div class="table">
        <table class="table table-striped mb-0 text-center text-nowrap" id="chequeTable">
            <thead>
                <tr>
                    <th class="fw-bold text-center py-3">Entry Date</th>
                    <th class="fw-bold text-center py-3">Cheque No</th>
                    <th class="fw-bold text-center py-3">Party Name</th>
                    <th class="fw-bold text-center py-3">Amount</th>
                    <th class="fw-bold text-center py-3">Salesman</th>
                    <th class="fw-bold text-center py-3">Location</th>
                    <th class="fw-bold text-center py-3">Status</th>
                    <th class="fw-bold text-center py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="chequeTableBody">
                @forelse ($chequeLocations as $cheque)
                <tr>
                    <td class="py-2 px-2">{{ $cheque->created_at->format('Y-m-d') }}</td>
                    <td class="py-2 px-2">{{ $cheque->cheque_no }}</td>
                    <td class="py-2 px-2" data-bs-toggle="tooltip" title="{{ $cheque->client->name }}">
                        {{ Str::limit($cheque->client->name, 20) }}</td>
                    <td class="py-2 px-2 text-end">{{ number_format($cheque->amount, 2) }}</td>
                    <td class="py-2 px-2" data-bs-toggle="tooltip" title="{{ $cheque->agent->name }}">
                        {{ Str::limit($cheque->agent->name, 20) }}</td>
                    <td class="py-2 px-2">{{ $cheque->location }}</td>
                    <td>
                        @if($cheque->details == 'Pending')
                        <span class="badge p-2 text-white  bg-warning text-dark">Pending</span>
                        @elseif($cheque->details == 'Processed')
                        <span class="badge p-2 text-white  bg-primary">Processed</span>
                        @elseif($cheque->details == 'Collected')
                        <span class="badge p-2 text-white  bg-success">Collected</span>
                        @else
                        <span class="badge p-2 text-white  bg-secondary">Unknown</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <a href="{{ route('cheque.locations.edit', $cheque->id) }}" class="btn me-1" title="Edit">
                                <i class="fas fa-pencil-alt text-success"></i>
                            </a>
                            <button type="button" class="btn btn-sm" title="Delete" data-toggle="modal"
                                data-target="#deleteModal{{ $cheque->id }}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                        </div>
                    </td>

                    <div class="modal fade" id="deleteModal{{ $cheque->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteModalLabel{{ $cheque->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                            <div class="modal-content shadow-lg rounded">
                                <div
                                    class="modal-header d-flex justify-content-between align-items-center gap-3 bg-danger text-white">
                                    <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $cheque->id }}">
                                        Confirm Deletion
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                                        aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <p class="mb-2 fs-5">Are you sure you want to delete
                                        this item?</p>
                                    <p class="text-muted small">This action cannot be
                                        undone.</p>
                                </div>
                                <div class="modal-footer d-flex justify-content-center">
                                    <button type="button" class="btn btn-secondary px-4 py-2" data-dismiss="modal">
                                        Cancel
                                    </button>
                                    <form action="{{ route('cheque.locations.destroy', $cheque->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-4 py-2">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open me-2" style="font-size: 1.5rem;"></i>
                        No cheque locations found. Add one to get started!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-1">
            {{$chequeLocations->links()}}
        </div>

    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.19/jspdf.plugin.autotable.min.js">
</script>

<script>
    $(document).ready(function() {
        $('#chequeTable').DataTable({
            paging: true,
            ordering: true,
            info: true,
            responsive: true,
            pageLength: 10,
            searching: false
        });
    });

    document.getElementById('searchInput').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#chequeTableBody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });


    document.getElementById('dateFilter').addEventListener('change', function () {
        const date = this.value;
        const rows = document.querySelectorAll('#chequeTableBody tr');
        rows.forEach(row => {
            const rowDate = row.cells[6].textContent;
            row.style.display = date === '' || rowDate === date ? '' : 'none';
        });
    });

    document.getElementById('resetFilters').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('dateFilter').value = '';
        const rows = document.querySelectorAll('#chequeTableBody tr');
        rows.forEach(row => row.style.display = '');
    });

    document.getElementById('exportExcelButton').addEventListener('click', function () {
        exportToExcel();
    });

    document.getElementById('exportPdfButton').addEventListener('click', function () {
        exportStyledPDF();
    });

    function exportToExcel() {
        var table = document.getElementById('chequeTable');
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.table_to_sheet(table);
        XLSX.utils.book_append_sheet(wb, ws, "ChequeLocations");
        XLSX.writeFile(wb, 'Cheque_Locations.xlsx');
    }

    function exportStyledPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        doc.setFontSize(18);
        doc.text("Cheque Locations Report", 14, 15);
        doc.setFontSize(12);
        doc.text("Generated on: " + new Date().toLocaleDateString(), 14, 25);
        doc.autoTable({
            html: "#chequeTable",
            startY: 35,
            styles: { fontSize: 10, cellPadding: 2, overflow: 'linebreak' },
            headStyles: { fillColor: [0, 105, 92], textColor: [255, 255, 255] },
            alternateRowStyles: { fillColor: [245, 245, 245] }
        });
        doc.save('Cheque_Locations.pdf');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
