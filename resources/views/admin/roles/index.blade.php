@extends('layouts.superApp',['page'=>'Roles'])

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h2 mb-2">Role Management</h1>
            <p class="text-muted mb-0">Manage system roles and their permissions</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create Role
        </a>
    </div>

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

    <!-- Stats Cards - Cleaner Version -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="bi bi-people fs-4 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $roles->total() }}</h4>
                            <p class="text-muted mb-0">Total Roles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="bi bi-check-circle fs-4 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $roles->where('is_active', true)->count() }}</h4>
                            <p class="text-muted mb-0">Active Roles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="bi bi-shield-check fs-4 text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $permissionGroups->count() }}</h4>
                            <p class="text-muted mb-0">Permission Groups</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="bi bi-key fs-4 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ \Spatie\Permission\Models\Permission::count() }}</h4>
                            <p class="text-muted mb-0">Total Permissions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="bi bi-list-ul me-2"></i>System Roles
            </h5>
        </div>
        <div class="card-body p-0">
            @if($roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Role</th>
                            <th class="border-0">Description</th>
                            <th class="border-0 text-center">Permissions</th>
                            <th class="border-0 text-center">Users</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr class="border-bottom">
                            <td class="text-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3">
                                        <i class="bi bi-person-badge text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $role->name }}</div>
                                        @if($role->name === 'Super Admin')
                                        <small class="text-muted">Full system access</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $role->description ?? 'No description' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $role->permissions_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $role->users_count }}</span>
                            </td>
                            <td class="text-center">
                                @if($role->name === 'Super Admin')
                                <span class="badge bg-success bg-opacity-10 text-success border-0">
                                    Active
                                </span>
                                @else
                                <span
                                    class="badge {{ $role->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} border-0">
                                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    <!-- View -->
                                    <a href="{{ route('roles.show', $role) }}" class="btn border-0 border"
                                        data-bs-toggle="tooltip" title="View details">
                                        <i class="bi bi-eye-fill text-info fs-5"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('roles.edit', $role) }}" class="btn border-0 border"
                                        data-bs-toggle="tooltip" title="Edit role">
                                        <i class="bi bi-pencil-square text-primary fs-5"></i>
                                    </a>

                                    @if($role->name !== 'Super Admin')
                                    <!-- Duplicate -->
                                    <form action="{{ route('roles.duplicate', $role) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn border-0 border" data-bs-toggle="tooltip"
                                            title="Duplicate role">
                                            <i class="bi bi-files text-warning fs-5"></i>
                                        </button>
                                    </form>

                                    @if($role->users_count === 0)
                                    {{--
                                    <!-- Delete -->
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn border-0 border" data-bs-toggle="tooltip"
                                            title="Delete role"
                                            onclick="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                            <i class="bi bi-trash3-fill text-danger fs-5"></i>
                                        </button>
                                    </form>
                                    @else
                                    <!-- Disabled Delete (has users) -->
                                    <button class="btn border-0 border text-muted" disabled data-bs-toggle="tooltip"
                                        title="Role has assigned users">
                                        <i class="bi bi-trash3 fs-5"></i>
                                    </button> --}}
                                    <button type="button" class="btn border-0 bg-transparent delete-role-btn"
                                        title="Delete" data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                        data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}"
                                        data-role-email="{{ $role->email }}">
                                        <i class="bi bi-trash3-fill fs-5 text-danger"></i>
                                    </button>
                                    <!-- Delete role Confirmation Modal -->
                                    <div class="modal fade" id="deleteRoleModal" tabindex="-1"
                                        aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-danger" id="deleteRoleModalLabel">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>Confirm Deletion
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div class="mb-1">
                                                        <i class="bi bi-person-x display-1 text-danger"></i>
                                                    </div>
                                                    <h5 class="mb-3">Are you sure you want to delete this role?</h5>
                                                    <div class="alert alert-warning mb-3">
                                                        <strong>Warning:</strong> This action cannot be undone. All
                                                        roles
                                                        data will be permanently removed
                                                        from the system.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 justify-content-center">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                                    </button>
                                                    <form action="{{route('roles.destroy',$role)}}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash3-fill me-2"></i>Delete Role
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-transparent border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} roles
                    </div>
                    <div>
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <div class="py-5">
                    <i class="bi bi-people display-1 text-muted opacity-50"></i>
                    <h4 class="text-muted mt-4">No roles found</h4>
                    <p class="text-muted mb-4">Get started by creating your first role</p>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Create Role
                    </a>
                </div>
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
        var tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            .map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
    });
</script>
@endsection