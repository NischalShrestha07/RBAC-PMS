@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Role Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-circle me-2"></i>Add Role
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Role Name</th>
                            <th>Permissions</th>
                            <th>Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>
                                <strong>{{ $role->name }}</strong>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(3) as $permission)
                                    <span class="badge bg-secondary">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                    <span class="badge bg-light text-dark">+{{ $role->permissions->count() - 3 }}
                                        more</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $role->users_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-role"
                                        data-role="{{ json_encode($role) }}"
                                        data-permissions="{{ $role->permissions->pluck('id') }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    @if($role->name !== 'Super Admin' && $role->name !== 'Admin')
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $roles->links() }}
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <label class="form-label">Permissions</label>
                    <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">{{ ucfirst($group) }}</h6>
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->id }}" id="perm_{{ $permission->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <label class="form-label">Permissions</label>
                    <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">{{ ucfirst($group) }}</h6>
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox"
                                            name="permissions[]" value="{{ $permission->id }}"
                                            id="edit_perm_{{ $permission->id }}">
                                        <label class="form-check-label" for="edit_perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Edit Role Modal
    document.querySelectorAll('.edit-role').forEach(button => {
        button.addEventListener('click', function() {
            const role = JSON.parse(this.dataset.role);
            const permissions = JSON.parse(this.dataset.permissions);

            const form = document.getElementById('editRoleForm');
            form.action = `/roles/${role.id}`;

            form.querySelector('input[name="name"]').value = role.name;

            // Uncheck all permissions first
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Check the role's permissions
            permissions.forEach(permissionId => {
                const checkbox = document.getElementById(`edit_perm_${permissionId}`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });

            new bootstrap.Modal(document.getElementById('editRoleModal')).show();
        });
    });
});
</script>
@endsection
