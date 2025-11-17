@extends('layouts.superApp',['page'=>'Users'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">User Management</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add User
        </a>
    </div>

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
    <!-- Search and Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            @foreach($assignableRoles as $role)
                            <option value="{{ $role->name }}" {{ request('role')==$role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>User</th>
                            <th>Contact Info</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="text-center">
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ $user->image ? asset('uploads/images/' . $user->image) : asset('default-avatar.png') }}"
                                        class="rounded-circle border border-2 border-primary" width="50" height="50"
                                        alt="{{ $user->name }}">
                                    <div class="ms-2 text-start">
                                        <strong class="d-block">{{ $user->name }}</strong>
                                        <small class="text-muted">ID: {{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-primary">{{ $user->email }}</div>
                                    @if($user->phone)
                                    <small class="text-muted">{{ $user->phone }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                <span class="badge bg-primary mb-1">{{ $role->name }}</span>
                                @endforeach
                                @else
                                <span class="badge bg-secondary">No Role</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $user->department->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $user->branch->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @if($user->last_login_at)
                                <small class="text-muted">{{ $user->last_login_at->format('M j, Y g:i A') }}</small>
                                @else
                                <small class="text-muted">Never</small>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">
                                    <!-- View -->
                                    <a href="{{ route('users.show', $user) }}" title="View"
                                        class="btn border-0 bg-transparent ">
                                        <i class="bi bi-eye-fill fs-5 text-info"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('users.edit', $user) }}" title="Edit"
                                        class="btn border-0 bg-transparent ">
                                        <i class="bi bi-pencil-square fs-5 text-primary"></i>
                                    </a>

                                    <!-- Activate / Deactivate -->
                                    <button type="button" class="btn border-0 bg-transparent toggle-status-btn"
                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                        data-bs-toggle="modal" data-bs-target="#toggleStatusModal"
                                        data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-current-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                                        <i
                                            class="bi fs-5 {{ $user->is_active ? 'bi-person-dash-fill text-warning' : 'bi-person-check-fill text-success' }}"></i>
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
                                                        this user?</h5>
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
                                                    <form action="{{route('users.toggle-status',$user->id)}}"
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

                                    <!-- Permissions -->
                                    <a href="{{ route('users.permissions', $user) }}" title="Permissions"
                                        class="btn border-0 bg-transparent ">
                                        <i class="bi bi-shield-fill-check fs-5 text-secondary"></i>
                                    </a>

                                    <!-- Delete -->
                                    @if($user->id !== Auth::id() && !$user->hasRole('Super Admin'))
                                    <button type="button" class="btn border-0 bg-transparent delete-user-btn"
                                        title="Delete" data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                        data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}">
                                        <i class="bi bi-trash3-fill fs-5 text-danger"></i>
                                    </button>
                                    <!-- Delete User Confirmation Modal -->
                                    <div class="modal fade" id="deleteUserModal" tabindex="-1"
                                        aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-danger" id="deleteUserModalLabel">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>Confirm Deletion
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div class="mb-1">
                                                        <i class="bi bi-person-x display-1 text-danger"></i>
                                                    </div>
                                                    <h5 class="mb-3">Are you sure you want to delete this user?</h5>
                                                    <div class="alert alert-warning mb-3">
                                                        <strong>Warning:</strong> This action cannot be undone. All user
                                                        data will be permanently removed
                                                        from the system.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 justify-content-center">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                                    </button>
                                                    <form action="{{route('users.destroy',$user->id)}}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash3-fill me-2"></i>Delete User
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users</div>
                {{ $users->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="text-muted mt-3">No users found</h4>
                <p class="text-muted">Get started by creating your first user.</p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add User
                </a>
            </div>
            @endif
        </div>
    </div>
</div>


@endsection