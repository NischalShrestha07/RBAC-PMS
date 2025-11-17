@extends('layouts.app',['page'=>'Create User'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1 fw-bold text-primary">Create New User</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Users
                </a>
            </div>

            @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                    <div>
                        <h6 class="mb-1">Please fix the following errors:</h6>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-person-plus me-2"></i>Create New User Account
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Full Name <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-person text-muted"></i>
                                        </span>
                                        <input type="text" name="name"
                                            class="form-control border-start-0 @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="Enter full name" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Email Address <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-envelope text-muted"></i>
                                        </span>
                                        <input type="email" name="email"
                                            class="form-control border-start-0 @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" placeholder="Enter email address" required>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock text-muted"></i>
                                        </span>
                                        <input type="password" name="password"
                                            class="form-control border-start-0 @error('password') is-invalid @enderror"
                                            placeholder="Enter password" required>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Minimum 8 characters with letters and numbers
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock-fill text-muted"></i>
                                        </span>
                                        <input type="password" name="password_confirmation"
                                            class="form-control border-start-0" placeholder="Confirm password" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Roles <span
                                            class="text-danger">*</span></label>
                                    <select name="roles[]" class="form-select @error('roles') is-invalid @enderror"
                                        multiple required style="height: 140px;">
                                        @foreach($assignableRoles as $role)
                                        <option value="{{ $role->name }}" {{ in_array($role->name, old('roles', [])) ?
                                            'selected' : '' }}
                                            class="p-2 {{ in_array($role->name, old('roles', [])) ? 'bg-primary
                                            text-white' : '' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $role->name }}</span>
                                                @if($role->description)
                                                <small class="text-muted">{{ Str::limit($role->description, 40)
                                                    }}</small>
                                                @endif
                                            </div>
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('roles')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Hold Ctrl/Cmd to select multiple roles
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-telephone text-muted"></i>
                                        </span>
                                        <input type="text" name="phone"
                                            class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" placeholder="Enter phone number">
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Department <span
                                            class="text-danger">*</span></label>
                                    <select name="department_id"
                                        class="form-select @error('department_id') is-invalid @enderror" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id')==$dept->id ? 'selected'
                                            : '' }}
                                            class="{{ old('department_id')==$dept->id ? 'bg-primary text-white' : ''
                                            }}">
                                            {{ $dept->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Branch <span
                                            class="text-danger">*</span></label>
                                    <select name="branch_id"
                                        class="form-select @error('branch_id') is-invalid @enderror" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id')==$branch->id ? 'selected'
                                            : '' }}
                                            class="{{ old('branch_id')==$branch->id ? 'bg-primary text-white' : '' }}">
                                            {{ $branch->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus me-2"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
