@extends('layouts.app',['page'=>'Departments'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Department Management</h2>
        <a href="{{ route('departments.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="bi bi-plus-circle me-2"></i>Add Department
        </a>
    </div>

    <!-- Alerts Top-Right -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
        @if (session('success'))
        <div class="alert alert-success text-white bg-success alert-dismissible custom-alert fade show" role="alert"
            style="white-space: normal; word-wrap: break-word;">
            <div class="d-flex align-items-center">
                <div>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade show" role="alert"
            style="white-space: normal; word-wrap: break-word;">
            <div class="d-flex align-items-center">
                <div>
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>

    <!-- Search and Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('departments.index') }}" method="GET" id="searchForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label small text-muted">Search Departments</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" id="search" class="form-control border-start-0"
                                placeholder="Enter department name or code..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label small text-muted">Filter by Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit"
                                class="btn btn-primary flex-fill d-flex align-items-center justify-content-center">
                                <i class="bi bi-funnel me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('departments.index') }}"
                                class="btn btn-secondary d-flex align-items-center justify-content-center">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Departments</h5>
                <span class="badge bg-primary rounded-pill">{{ $departments->total() }} total</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($departments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Department</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                        <tr class="align-middle">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="color-badge me-3"
                                        style="background-color: {{ $department->color }}; border: 2px solid {{ $department->color }};">
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $department->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $department->code }}</span>
                            </td>
                            <td>
                                @if($department->description)
                                <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                    data-bs-toggle="tooltip" title="{{ $department->description }}">
                                    {{ Str::limit($department->description, 50) }}
                                </span>
                                @else
                                <span class="text-muted fst-italic">No description</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people text-primary me-1"></i>
                                    <span class="fw-medium">{{ $department->users_count }}</span>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $department->is_active ? 'bg-success' : 'bg-secondary' }} d-flex align-items-center"
                                    style="width: fit-content;">
                                    <i
                                        class="bi bi-{{ $department->is_active ? 'check-circle' : 'pause-circle' }} me-1 small"></i>
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $department->created_at->format('M j, Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('departments.show', $department) }}"
                                        class="btn text-info d-flex align-items-center" data-bs-toggle="tooltip"
                                        title="View Details">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('departments.edit', $department) }}"
                                        class="btn text-primary d-flex align-items-center" data-bs-toggle="tooltip"
                                        title="Edit Department">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @if($department->users_count === 0)
                                    {{-- <form action="{{ route('departments.toggle-status', $department) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn border-0 bg-transparent d-flex align-items-center"
                                            data-bs-toggle="tooltip"
                                            title="{{ $department->is_active ? 'Deactivate Department' : 'Activate Department' }}"
                                            onclick="return confirm('Are you sure you want to {{ $department->is_active ? 'deactivate' : 'activate' }} this department?')">
                                            <i
                                                class="bi bi-{{ $department->is_active ? 'person-dash-fill text-warning' : 'person-check-fill text-success' }} fs-5"></i>
                                        </button>
                                    </form> --}}
                                    <button type="button" class="btn border-0 bg-transparent toggle-status-btn"
                                        title="{{ $department->is_active ? 'Deactivate' : 'Activate' }}"
                                        data-bs-toggle="modal" data-bs-target="#toggleStatusModal"
                                        data-department-id="{{ $department->id }}"
                                        data-department-name="{{ $department->name }}"
                                        data-department-email="{{ $department->email }}"
                                        data-current-status="{{ $department->is_active ? 'active' : 'inactive' }}">
                                        <i
                                            class="bi fs-5 {{ $department->is_active ? 'bi-person-dash-fill text-warning' : 'bi-person-check-fill text-success' }}"></i>
                                    </button>
                                    <!-- Toggle Status Confirmation Modal -->
                                    <div class="modal fade" id="toggleStatusModal" tabindex="-1"
                                        aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-warning" id="toggleStatusModalLabel">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>Confirm Status
                                                        Change
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div>
                                                        <i class="bi bi-arrow-repeat text-primary display-1"></i>
                                                    </div>
                                                    <h5>Are you sure you want to activate/deactivate
                                                        this department?</h5>
                                                    <div class="alert alert-warning">
                                                        <strong>Warning:</strong> This action can be undone.User
                                                        can be again activated/deactivated as need from the system.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 justify-content-center">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                                    </button>
                                                    <form action="{{ route('departments.toggle-status', $department) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-arrow-repeat text-white"></i> Confirm
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <form action="{{ route('departments.destroy', $department) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn text-danger d-flex align-items-center"
                                            onclick="return confirm('Are you sure you want to delete this department?')"
                                            data-bs-toggle="tooltip" title="Delete Department">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn border-0 text-muted d-flex align-items-center" disabled
                                        data-bs-toggle="tooltip" title="Cannot delete department with users">
                                        <i class="bi bi-trash-fill"></i>
                                    </button> --}}
                                    <button type="button" class="btn border-0 bg-transparent delete-department-btn"
                                        title="Delete" data-bs-toggle="modal" data-bs-target="#deleteDepartmentModal"
                                        data-department-id="{{ $department->id }}"
                                        data-department-name="{{ $department->name }}"
                                        data-department-email="{{ $department->email }}">
                                        <i class="bi bi-trash3-fill fs-5 text-danger"></i>
                                    </button>
                                    <!-- Delete Department Confirmation Modal -->
                                    <div class="modal fade" id="deleteDepartmentModal" tabindex="-1"
                                        aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-danger" id="deleteDepartmentModalLabel">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>Confirm Deletion
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div class="mb-1">
                                                        <i class="bi bi-person-x display-1 text-danger"></i>
                                                    </div>
                                                    <h5 class="mb-3">Are you sure you want to delete this department?
                                                    </h5>
                                                    <div class="alert alert-warning mb-3">
                                                        <strong>Warning:</strong> This action cannot be undone. All
                                                        departments
                                                        data will be permanently removed
                                                        from the system.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 justify-content-center">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                                    </button>
                                                    <form action="{{route('departments.destroy',$department)}}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash3-fill me-2"></i>Delete Department
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted">Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{
                    $departments->total() }} departments</div>
                {{ $departments->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-building display-1 text-muted opacity-50"></i>
                <h4 class="text-muted mt-3">No departments found</h4>
                <p class="text-muted mb-4">Get started by creating your first department.</p>
                <a href="{{ route('departments.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-plus-circle me-2"></i>Add Department
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-close alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Add active state to table rows on hover
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(0,0,0,0.02)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    });
</script>
@endsection
