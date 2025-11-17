@extends('layouts.app',['page'=>'Permissions'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">User Permissions</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>
                            </li>
                            <li class="breadcrumb-item active">Permissions</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

            <div class="row">
                <!-- User Info Card -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ $user->image ? asset('uploads/images/' . $user->image) : asset('default-avatar.png') }}"
                                class="rounded-circle border border-2 border-primary" width="80" height="80"
                                alt="{{ $user->name }}">
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->email }}</p>

                            <div class="d-flex justify-content-center mb-3">
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} me-2">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="text-start">
                                <h6>Assigned Roles:</h6>
                                @foreach($user->roles as $role)
                                <span class="badge bg-primary d-block mb-1">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Permission Summary -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Permission Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <strong>Total Permissions:</strong>
                                <span class="badge bg-info float-end">{{ $permissions->flatten()->count() }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Permission Groups:</strong>
                                <span class="badge bg-secondary float-end">{{ $permissions->count() }}</span>
                            </div>
                            <div>
                                <strong>Access Level:</strong>
                                <span class="badge bg-success float-end">
                                    @if($user->hasRole('Super Admin'))
                                    Full Access
                                    @elseif($permissions->flatten()->count() > 20)
                                    High Access
                                    @elseif($permissions->flatten()->count() > 10)
                                    Medium Access
                                    @else
                                    Limited Access
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Card -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-shield-lock me-2"></i>Effective Permissions
                                <span class="badge bg-light text-primary ms-2">{{ $permissions->flatten()->count()
                                    }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($permissions->count() > 0)
                            @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-4">
                                <h6 class="text-primary mb-3 border-bottom pb-2">
                                    <i class="bi bi-folder me-2"></i>
                                    {{ ucfirst(str_replace('_', ' ', $group)) }}
                                    <span class="badge bg-secondary">{{ count($groupPermissions) }}</span>
                                </h6>
                                <div class="row">
                                    @foreach($groupPermissions as $permission)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span class="small">{{ $permission->name }}</span>
                                        </div>
                                        @if($permission->description)
                                        <small class="text-muted ms-4">{{ $permission->description }}</small>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="text-center py-4">
                                <i class="bi bi-shield-x display-4 text-muted"></i>
                                <h5 class="text-muted mt-3">No Permissions Assigned</h5>
                                <p class="text-muted">This user doesn't have any direct permissions. Permissions are
                                    inherited from roles.</p>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Permissions are inherited from assigned roles. To modify permissions, edit the user's
                                roles in the Role Management section.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
