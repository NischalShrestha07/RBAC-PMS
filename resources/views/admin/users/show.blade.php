@extends('layouts.app',['page'=>'User Details'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">User Details</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active">{{ $user->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-2">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </div>
            <!-- Alerts Top-Right -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
                @if (session('success'))
                <div class="alert alert-success text-white bg-success alert-dismissible custom-alert  fade-in"
                    role="alert" style="white-space: normal; word-wrap: break-word;">
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

            <div class="row">
                <!-- User Information -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ $user->image ? asset('uploads/images/' . $user->image) : asset('default-avatar.png') }}"
                                class="rounded-circle border border-2 border-primary" width="80" height="80"
                                alt="{{ $user->name }}">
                            <h3>{{ $user->name }}</h3>
                            <p class="text-muted">{{ $user->email }}</p>

                            <div class="mb-3">
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            @if($user->phone)
                            <p class="mb-2">
                                <i class="bi bi-telephone me-2"></i>{{ $user->phone }}
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">User Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Department:</strong>
                                <p class="mb-0">{{ $user->department->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Branch:</strong>
                                <p class="mb-0">{{ $user->branch->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Member Since:</strong>
                                <p class="mb-0">{{ $user->created_at->format('F j, Y') }}</p>
                            </div>
                            <div>
                                <strong>Last Login:</strong>
                                <p class="mb-0">
                                    @if($user->last_login_at)
                                    {{ $user->last_login_at->format('F j, Y g:i A') }}
                                    @else
                                    Never
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles and Permissions -->
                <div class="col-md-8">
                    <!-- Roles Card -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Assigned Roles</h5>
                        </div>
                        <div class="card-body">
                            @if($user->roles->count() > 0)
                            <div class="row">
                                @foreach($user->roles as $role)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h6 class="mb-2">{{ $role->name }}</h6>
                                        @if($role->description)
                                        <p class="text-muted small mb-2">{{ $role->description }}</p>
                                        @endif
                                        <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <strong>Key Permissions:</strong>
                                                {{ $role->permissions->take(3)->pluck('name')->implode(', ') }}
                                                @if($role->permissions->count() > 3)
                                                ... and {{ $role->permissions->count() - 3 }} more
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="bi bi-person-x display-4 text-muted"></i>
                                <p class="text-muted mt-3">No roles assigned to this user</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <a href="{{ route('users.permissions', $user) }}"
                                        class="btn btn-outline-primary w-100">
                                        <i class="bi bi-shield-lock me-2"></i>View Permissions
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <!-- Activate / Deactivate -->
                                    <button type="button"
                                        class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} w-100 toggle-status-btn"
                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                        data-bs-toggle="modal" data-bs-target="#toggleStatusModal"
                                        data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-current-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                                        {{-- <i
                                            class="bi fs-5 {{ $user->is_active ? 'bi-person-dash-fill text-warning' : 'bi-person-check-fill text-success' }}"></i>
                                        --}}
                                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }} me-2"></i>
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
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
                                </div>
                                @if($user->id !== Auth::id() && !$user->hasRole('Super Admin'))
                                <div class="col-md-4">
                                    {{-- <form action="{{ route('users.destroy', $user) }}" method="POST"
                                        class="d-inline w-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100"
                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            <i class="bi bi-trash me-2"></i>Delete User
                                        </button>
                                    </form> --}}
                                    <button type="button" class="btn btn-outline-danger w-100" title="Delete"
                                        data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                        data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}">
                                        <i class="bi bi-trash me-2"></i>Delete User </button>
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
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
