@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Role Management</h2>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Role
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Users</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>
                                <strong>{{ $role->name }}</strong>
                                @if($role->name === 'Super Admin')
                                <span class="badge bg-warning ms-1">System</span>
                                @endif
                            </td>
                            <td>{{ $role->description ?? 'No description' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $role->permissions_count }}</span>
                                <button class="btn btn-sm btn-outline-secondary ms-1" data-bs-toggle="tooltip"
                                    title="{{ $role->permissions->pluck('name')->implode(', ') }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $role->users_count }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $role->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($role->name !== 'Super Admin' && $role->users_count === 0)
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($role->name !== 'Super Admin')
                                    <form action="{{ route('roles.duplicate', $role) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-copy"></i>
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
@endsection
