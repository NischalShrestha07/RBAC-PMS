@extends('layouts.superApp',['page'=>'Create Role'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Create New Role</h2>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Role Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        required>
                                    @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Dashboard Access *</label>
                                    <select name="dashboard_access" class="form-select" required>
                                        <option value="full" {{ old('dashboard_access')=='full' ? 'selected' : '' }}>
                                            Full Access</option>
                                        <option value="limited" {{ old('dashboard_access')=='limited' ? 'selected' : ''
                                            }}>Limited Access</option>
                                        <option value="custom" {{ old('dashboard_access')=='custom' ? 'selected' : ''
                                            }}>Custom Access</option>
                                    </select>
                                    @error('dashboard_access')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{
                                            old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"
                                rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissions *</label>
                            <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto;">
                                @foreach($permissions as $group => $groupPermissions)
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3 border-bottom pb-2">
                                        <div class="form-check">
                                            <input class="form-check-input group-checkbox" type="checkbox"
                                                data-group="group-{{ Str::slug($group) }}">
                                            <label class="form-check-label fw-bold">
                                                {{ ucfirst($group) }}
                                            </label>
                                        </div>
                                    </h6>
                                    <div class="row" id="group-{{ Str::slug($group) }}">
                                        @foreach($groupPermissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permissions[]" value="{{ $permission->id }}"
                                                    id="perm_{{ $permission->id }}" {{ is_array(old('permissions')) &&
                                                    in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}"
                                                    data-bs-toggle="tooltip" title="{{ $permission->description }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('permissions')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Group checkbox functionality
    document.querySelectorAll('.group-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const groupId = this.dataset.group;
            const groupPermissions = document.querySelectorAll(`#${groupId} .permission-checkbox`);

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

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection
