@extends('layouts.app',['page'=>'Role Details'])

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Role Details</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">{{ $role->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-2"></i>Edit Role
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Role Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Role Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-person-badge text-white fs-2"></i>
                        </div>
                        <h4 class="mb-1">{{ $role->name }}</h4>
                        @if($role->name === 'Super Admin')
                        <span class="badge bg-warning">
                            <i class="bi bi-shield-check me-1"></i>System Role
                        </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <p class="text-muted">{{ $role->description ?? 'No description provided' }}</p>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $role->permissions_count }}</h4>
                                <small class="text-muted">Permissions</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info mb-1">{{ $role->users_count }}</h4>
                            <small class="text-muted">Assigned Users</small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Status</label>
                        <div>
                            @if($role->name === 'Super Admin')
                            <span class="badge bg-success">
                                <i class="bi bi-shield-check me-1"></i>Always Active
                            </span>
                            @else
                            <span class="badge {{ $role->is_active ? 'bg-success' : 'bg-danger' }}">
                                <i class="bi bi-{{ $role->is_active ? 'check' : 'x' }}-circle me-1"></i>
                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Created</label>
                        <p class="text-muted mb-0">{{ $role->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-2"></i>Edit Role
                        </a>

                        @if($role->name !== 'Super Admin')
                        <form action="{{ route('roles.duplicate', $role) }}" method="POST" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-copy me-2"></i>Duplicate Role
                            </button>
                        </form>

                        @if($role->users_count === 0)
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                <i class="bi bi-trash me-2"></i>Delete Role
                            </button>
                        </form>
                        @else
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="bi bi-trash me-2"></i>Cannot Delete (Has Users)
                        </button>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>Assigned Permissions
                        <span class="badge bg-primary ms-2">{{ $role->permissions_count }}</span>
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
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <div>
                                        <div class="fw-semibold">{{ $permission->name }}</div>
                                        @if($permission->description)
                                        <small class="text-muted">{{ $permission->description }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-shield-x display-4 text-muted"></i>
                        <h5 class="text-muted mt-3">No Permissions Assigned</h5>
                        <p class="text-muted">This role doesn't have any permissions assigned.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Users -->
            @if($role->users_count > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>Assigned Users
                        <span class="badge bg-info ms-2">{{ $role->users_count }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->image ? asset('uploads/images/' . $user->image) : asset('default-avatar.png') }}"
                                                class="rounded-circle me-2" width="32" height="32"
                                                alt="{{ $user->name }}">
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
