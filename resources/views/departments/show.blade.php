@extends('layouts.app',['page'=>'View Departments'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Department Details</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                            <li class="breadcrumb-item active">{{ $department->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary me-2">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Department Information -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <div class="department-color mb-3" style="background-color: {{ $department->color }};">
                            </div>
                            <h3>{{ $department->name }}</h3>
                            <span class="badge bg-secondary fs-6">{{ $department->code }}</span>

                            <div class="mt-4">
                                <span class="badge {{ $department->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-0">{{ $department->users_count }}</h4>
                                        <small class="text-muted">Users</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-0">{{ $department->created_at->format('M Y') }}</h4>
                                        <small class="text-muted">Created</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Details -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Department Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Description:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($department->description)
                                    <p class="mb-0">{{ $department->description }}</p>
                                    @else
                                    <p class="text-muted mb-0">No description provided</p>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Color Code:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <div class="d-flex align-items-center">
                                        <div class="color-preview me-2"
                                            style="background-color: {{ $department->color }};"></div>
                                        <code>{{ $department->color }}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Created Date:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $department->created_at->format('F j, Y g:i A') }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Last Updated:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $department->updated_at->format('F j, Y g:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Department Users -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Assigned Users ({{ $department->users_count }})</h5>
                        </div>
                        <div class="card-body">
                            @if($department->users_count > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($department->users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $user->image ? asset('uploads/images/'.$user->image) : asset('images/default-avatar.png') }}"
                                                        class="rounded-circle me-2" width="32" height="32"
                                                        alt="{{ $user->name }}">
                                                    {{ $user->name }}
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->roles->count() > 0)
                                                <span class="badge bg-primary">{{ $user->roles->first()->name }}</span>
                                                @else
                                                <span class="badge bg-secondary">No Role</span>
                                                @endif
                                            </td>
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
                            @else
                            <div class="text-center py-4">
                                <i class="bi bi-people display-4 text-muted"></i>
                                <p class="text-muted mt-3">No users assigned to this department</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
