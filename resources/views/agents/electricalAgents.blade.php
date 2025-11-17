@extends('layouts.electricalApp',['page' => 'Salesmans'])

@section('content')
<section class="content-header d-flex align-items-center gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('electrical.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Salesmans</li>
    </div>
</section>

<!-- Search Section -->
<section class="content mt-4 mb-2">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Search & Filter Salesmen
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('electrical.agents.create') }}" method="GET" id="searchForm">
                <div class="row g-2 align-items-end">
                    <!-- Salesman Name -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Salesman Name</label>
                        <div class="position-relative searchable-dropdown">
                            <div class="dropdown w-100">
                                <button class="form-control text-start dropdown-toggle-btn" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ request('filter_salesman') ?? 'Select Salesman' }}
                                </button>

                                <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                                    style="max-height: 250px; overflow-y: auto;">
                                    <li class="px-2 py-1 sticky-top bg-white border-bottom">
                                        <input type="text" class="form-control form-control-sm dropdown-search"
                                            placeholder="Search salesman...">
                                    </li>
                                    @foreach ($agents as $agent)
                                    <li>
                                        <a href="#" class="dropdown-item dropdown-item-option"
                                            data-value="{{ $agent->name }}">
                                            <small>{{ $agent->name }}</small>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>

                                <input type="hidden" name="filter_salesman" class="dropdown-hidden-input"
                                    value="{{ request('filter_salesman') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Number -->
                    <div class="col-md-3">
                        <label for="contact" class="form-label fw-semibold">Contact No</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-phone text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="contact" name="contact"
                                placeholder="Enter contact..." value="{{ request('contact') }}">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="col-md-3">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="address" name="address"
                                placeholder="Enter address..." value="{{ request('address') }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-flex gap-1 align-items-center">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <a href="{{ route('electrical.agents.create') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Salesman Table Section -->
<div class="card mt-4 border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Salesmans</h5>
        </div>
        <table id="EmployeeTable" class="table table-bordered table-striped text-nowrap">
            <thead>
                <tr class="text-center">
                    <th>Salesman</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Area</th>
                    {{-- <th>Department</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($agents as $item)
                <tr class="text-center">
                    <td class="fw-semibold">{{ $item->name }}</td>
                    <td>{{ $item->address }}</td>
                    <td>{{ $item->contact }}</td>
                    <td>{{ $item->area ?? 'N/A' }}</td>
                    {{-- <td>{{ $item->department }}</td> --}}
                    <td>
                        <a href="{{ route('electrical.agents.show', $item->id) }}" class="btn btn-link text-primary p-0"
                            title="View">
                            <i class="fas fa-eye fa-lg"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $agents->links() }}
        </div>
    </div>
</div>
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