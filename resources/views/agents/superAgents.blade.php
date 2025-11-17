@extends('layouts.app',['page' => 'Salesman'])

@section('content')
<section class="content-header py-2">
    <div class="d-flex gap-3 justify-content-between position-relative">
        <div class="d-flex align-items-center breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a class="link-primary fw-bold text-decoration-none" href="{{ route('dashboard.unified') }}">Home</a>
            </li>
            <li class="breadcrumb-item active">Salesmans</li>
        </div>

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

        @can('create agents')
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addEmployeeModal">Add New
            Salesman</button>
        @endcan
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
            <form action="{{ route('agents.index') }}" method="GET" id="searchForm">
                <div class="row g-2 align-items-end">
                    <!-- Salesman Name -->
                    <div class="col-md-3">
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
                    <div class="col-md-2">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="address" name="address"
                                placeholder="Enter address..." value="{{ request('address') }}">
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-2">
                        <label for="department" class="form-label fw-semibold">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department')==$department->id ? 'selected'
                                : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-flex gap-1 align-items-center">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <a href="{{ route('agents.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Salesman Table Section -->
<div class="card mt-4 border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <table id="EmployeeTable" class="table table-bordered table-striped text-center text-nowrap mb-0">
            <thead>
                <tr class="text-center">
                    <th>Salesman</th>
                    <th>Address</th>
                    <th>Contact No</th>
                    <th>Area</th>
                    <th>Department</th>
                    @canany(['edit agents', 'delete agents', 'view agents'])
                    <th>Actions</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach ($agents as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->address ?? 'N/A' }}</td>
                    <td>{{ $item->contact }}</td>
                    <td>{{ $item->area ?? 'N/A' }}</td>
                    {{-- <td>{{ $item->department?->name ?? 'N/A' }}</td> --}}
                    <td>{{ $item->department()->first() ?
                        $item->department()->first()->name : 'N/A' }}</td>

                    @canany(['edit agents', 'delete agents', 'view agents'])
                    <td class="font-weight-medium">
                        <div class="btn-group d-flex align-items-center" role="group" aria-label="Actions">
                            @can('edit agents')
                            <button type="button" class="btn border-0 p-0 bg-transparent text-success" title="Edit"
                                data-toggle="modal" data-target="#updateModel{{ $item->id }}">
                                <i class="fas fa-lg fa-pencil-alt"></i>
                            </button>
                            @endcan

                            @can('view agents')
                            <a href="{{ route('agents.show', $item->id) }}"
                                class="btn border-0 p-0 bg-transparent text-primary" title="View">
                                <i class="fas fa-eye fa-lg text-primary"></i>
                            </a>
                            @endcan

                            @can('delete agents')
                            <button type="button" class="btn border-0 p-0 bg-transparent text-danger" title="Delete"
                                data-toggle="modal" data-target="#deleteModal{{ $item->id }}">
                                <i class="fas fa-lg fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>

                        <!-- Update Modal -->
                        @can('edit agents')
                        <div class="modal fade align-items-center" style="text-align: left;"
                            id="updateModel{{ $item->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="updateModelLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div
                                        class="modal-header d-flex align-items-center justify-content-between gap-3 bg-light">
                                        <h5 class="modal-title" id="updateModelLabel">
                                            <i class="fas fa-edit"></i>
                                            Update Salesman
                                        </h5>
                                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('agents.update', $item->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="form row g-4">
                                                <!-- Salesman Name -->
                                                <div class="form-group col-md-4">
                                                    <label for="name" class="form-label">Salesman Name:</label>
                                                    <input type="text" id="name" name="name" value="{{$item->name}}"
                                                        placeholder="Salesman's Name" class="form-control">
                                                </div>

                                                <!-- Address -->
                                                <div class="form-group col-md-4">
                                                    <label for="address">Address:</label>
                                                    <input type="text" id="address" name="address"
                                                        value="{{ $item->address }}" class="form-control"
                                                        placeholder="Address">
                                                </div>

                                                <!-- Contact No -->
                                                <div class="form-group col-md-4">
                                                    <label for="contact">Contact No:</label>
                                                    <input type="tel" id="contact" name="contact"
                                                        value="{{ $item->contact }}" class="form-control">
                                                </div>

                                                <!-- Area -->
                                                <div class="form-group col-md-4">
                                                    <label for="area">Salesman's Area:</label>
                                                    <input type="text" id="area" name="area" value="{{ $item->area }}"
                                                        class="form-control" placeholder="Area">
                                                </div>

                                                <!-- Department -->
                                                <div class="form-group col-md-4">
                                                    <label for="department"
                                                        class="form-label font-weight-bold mb-0">Department:</label>
                                                    <select name="department_id" id="department_id" class="form-control"
                                                        required>
                                                        <option value="" disabled>Select Department</option>
                                                        @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}" {{ $item->department_id ==
                                                            $department->id ? 'selected' : '' }}>
                                                            {{ $department->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <input type="hidden" name="created_by" value="{{ Auth::id() }}">
                                            <input type="hidden" name="id" value="{{ $item->id }}">

                                            <!-- Submit Button -->
                                            <div class="modal-footer mt-4">
                                                <button type="submit" class="btn btn-success">
                                                    <i style="color: white" class="fas fa-save"></i>
                                                    Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan

                        <!-- Delete Modal -->
                        @can('delete agents')
                        <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal- modal-dialog-centered" role="document">
                                <div class="modal-content shadow-lg">
                                    <div
                                        class="modal-header d-flex justify-content-between align-items-center gap-3 bg-danger text-white">
                                        <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $item->id }}">
                                            Confirm Deletion
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                                            aria-label="Close">
                                        </button>
                                    </div>
                                    <div class="modal-body text-center py-4">
                                        <p class="mb-2 fs-5">Are you sure you want to delete this salesman?</p>
                                        <p class="text-muted small">This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center gap-3">
                                        <button type="button" class="btn btn-secondary px-4 py-2" data-dismiss="modal">
                                            Cancel
                                        </button>
                                        <form action="{{ route('agents.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-4 py-2">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </td>
                    @endcanany
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3 p-3">
            {{ $agents->links() }}
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
@can('create agents')
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header d-flex align-items-center justify-content-between gap-3 bg-light">
                <h4 class="modal-title" id="addEmployeeModalLabel">
                    <i class="fas fa-user-tie mr-2"></i>
                    Add New Salesman
                </h4>
                <button type="button" class="btn-close text-white" data-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="{{ route('agents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form row g-4">
                        <!-- Name -->
                        <div class="form-group col-md-4">
                            <label for="name" class="font-weight-bold">Name:</label>
                            <input type="text" id="name" name="name" placeholder="Salesman Name" class="form-control"
                                required>
                        </div>

                        <!-- Contact -->
                        <div class="form-group col-md-4">
                            <label for="contact" class="font-weight-bold">Contact No:</label>
                            <input type="tel" id="contact" name="contact" placeholder="Contact Number"
                                class="form-control" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="address" class="font-weight-bold">Address:</label>
                            <input type="text" id="address" name="address" placeholder="Address" class="form-control"
                                required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="area" class="font-weight-bold">Salesman Area:</label>
                            <input type="text" id="area" name="area" placeholder="Salesman Area" class="form-control"
                                required>
                        </div>

                        <!-- Department -->
                        <div class="form-group col-md-4">
                            <label for="department_id" class="font-weight-bold">Department:</label>
                            <select id="department_id" name="department_id" class="form-control" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-right">
                        <button type="submit" class="btn btn-success mt-4">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
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
