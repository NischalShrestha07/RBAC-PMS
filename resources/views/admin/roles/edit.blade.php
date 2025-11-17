@extends('layouts.superApp',['page'=>'Edit Role'])

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit Role</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.show', $role) }}">{{ $role->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>Edit Role Details
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $role->name) }}" placeholder="Enter role name" required {{
                                        $role->name === 'Super Admin' ? 'readonly' : '' }}>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($role->name === 'Super Admin')
                                    <div class="form-text text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Super Admin role name cannot be changed
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{
                                            old('is_active', $role->is_active) ? 'checked' : '' }}
                                        {{ $role->name === 'Super Admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label">
                                            {{ $role->is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                    @if($role->name === 'Super Admin')
                                    <div class="form-text text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Super Admin role cannot be deactivated
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Enter role description">{{ old('description', $role->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Select the permissions you want to assign to this role. Use group checkboxes to select
                                all permissions in a category.
                            </div>

                            <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto;">
                                @foreach($permissions as $group => $groupPermissions)
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3 border-bottom pb-2">
                                        <div class="form-check">
                                            <input class="form-check-input group-checkbox" type="checkbox"
                                                data-group="group-{{ Str::slug($group) }}"
                                                id="group-{{ Str::slug($group) }}-check">
                                            <label class="form-check-label fw-bold"
                                                for="group-{{ Str::slug($group) }}-check">
                                                {{ ucfirst($group) }}
                                                <span class="badge bg-secondary ms-2">{{ count($groupPermissions)
                                                    }}</span>
                                            </label>
                                        </div>
                                    </h6>
                                    <div class="row" id="group-{{ Str::slug($group) }}">
                                        @foreach($groupPermissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permissions[]" value="{{ $permission->id }}"
                                                    id="perm_{{ $permission->id }}" {{ in_array($permission->id,
                                                $rolePermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                                @if($permission->description)
                                                <div class="form-text text-muted small">{{ $permission->description }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('permissions')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Role Insights
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Total Users Assigned:</strong>
                        <span class="badge bg-info float-end">{{ $role->users_count }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Active Permissions:</strong>
                        <span class="badge bg-success float-end">{{ $role->permissions_count }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="float-end">
                            @if($role->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Created On:</strong>
                        <span class="text-muted float-end">{{ $role->created_at->format('M j, Y') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Last Updated:</strong>
                        <span class="text-muted float-end">{{ $role->updated_at->format('M j, Y') }}</span>
                    </div>

                    @if(isset($recentUsers) && count($recentUsers))
                    <div class="mt-4">
                        <h6 class="text-primary mb-2">
                            <i class="bi bi-person-lines-fill me-1"></i>Recently Assigned Users
                        </h6>
                        <ul class="list-unstyled small">
                            @foreach($recentUsers as $user)
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <span>{{ $user->name }}</span>
                                <span class="text-muted">{{ $user->created_at->diffForHumans() }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($topPermissions) && count($topPermissions))
                    <div class="mt-4">
                        <h6 class="text-primary mb-2">
                            <i class="bi bi-shield-lock me-1"></i>Most Common Permissions
                        </h6>
                        <ul class="list-unstyled small">
                            @foreach($topPermissions as $perm)
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <span>{{ $perm->name }}</span>
                                <span class="text-muted">{{ $perm->usage_count }} roles</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Group checkbox functionality
        document.querySelectorAll('.group-checkbox').forEach(checkbox => {
            // Set initial state
            const groupId = checkbox.dataset.group;
            const groupPermissions = document.querySelectorAll(`#${groupId} .permission-checkbox`);
            const checkedCount = Array.from(groupPermissions).filter(p => p.checked).length;

            checkbox.checked = checkedCount === groupPermissions.length;
            checkbox.indeterminate = checkedCount > 0 && checkedCount < groupPermissions.length;

            // Add change event
            checkbox.addEventListener('change', function() {
                groupPermissions.forEach(perm => {
                    perm.checked = this.checked;
                });
            });
        });

        // Individual permission checkbox
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const groupId = this.closest('.row').id;
                const groupCheckbox = document.querySelector(`[data-group="${groupId}"]`);
                const groupPermissions = document.querySelectorAll(`#${groupId} .permission-checkbox`);
                const checkedCount = Array.from(groupPermissions).filter(p => p.checked).length;

                groupCheckbox.checked = checkedCount === groupPermissions.length;
                groupCheckbox.indeterminate = checkedCount > 0 && checkedCount < groupPermissions.length;
            });
        });

        // Select all permissions button
        const selectAllBtn = document.getElementById('selectAllBtn');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
                document.querySelectorAll('.group-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                    checkbox.indeterminate = false;
                });
            });
        }

        // Deselect all permissions button
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                document.querySelectorAll('.group-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.indeterminate = false;
                });
            });
        }
    });
</script>
@endsection