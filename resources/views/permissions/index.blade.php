@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Permissions Management</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permissionModal">
                        <i class="fas fa-plus"></i> Add Permission
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Permission Name</th>
                                    <th>Group</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $permission->group }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#permissionModal" data-id="{{ $permission->id }}"
                                            data-name="{{ $permission->name }}" data-group="{{ $permission->group }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="permissionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="permModalTitle">Add Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="perm_name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="perm_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="group" class="form-label">Group</label>
                        <input type="text" class="form-control" id="group" name="group" required>
                        <div class="form-text">e.g., User Management, Role Management, etc.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('permissionModal');
        const form = document.getElementById('permissionForm');
        const modalTitle = document.getElementById('permModalTitle');

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            if (button) {
                const permId = button.getAttribute('data-id');
                const permName = button.getAttribute('data-name');
                const permGroup = button.getAttribute('data-group');

                if (permId) {
                    modalTitle.textContent = 'Edit Permission';
                    form.action = "{{ url('permissions') }}/" + permId;
                    form.innerHTML += '@method("PUT")';
                    document.getElementById('perm_name').value = permName;
                    document.getElementById('group').value = permGroup;
                } else {
                    modalTitle.textContent = 'Add Permission';
                    form.action = "{{ route('permissions.store') }}";
                    document.getElementById('perm_name').value = '';
                    document.getElementById('group').value = '';
                }
            }
        });

        modal.addEventListener('hidden.bs.modal', function() {
            form.innerHTML = '@csrf';
            form.action = "{{ route('permissions.store') }}";
        });
    });
</script>
@endpush