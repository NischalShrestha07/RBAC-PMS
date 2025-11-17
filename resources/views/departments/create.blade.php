@extends('layouts.superApp',['page'=>'Create Department'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Create New Department</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                    Back</a>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('departments.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department Name *</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="Enter department name" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department Code *</label>
                                    <input type="text" name="code"
                                        class="form-control @error('code') is-invalid @enderror"
                                        value="{{ old('code') }}" placeholder="Enter unique code (e.g., HR, IT)"
                                        required>
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Unique code for the department (will be converted to
                                        uppercase)</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                rows="3" placeholder="Enter department description">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color" class="form-control form-control-color"
                                            value="{{ old('color', '#3498db') }}" title="Choose department color">
                                        <input type="text" name="color_text" class="form-control"
                                            value="{{ old('color', '#3498db') }}" placeholder="#3498db"
                                            oninput="this.previousElementSibling.value = this.value">
                                    </div>
                                    <div class="form-text">Color for department identification</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{
                                            old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label">Active Department</label>
                                    </div>
                                    <div class="form-text">Inactive departments won't be available for new user
                                        assignments</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('departments.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Sync color inputs
    const colorInput = document.querySelector('input[type="color"]');
    const colorText = document.querySelector('input[name="color_text"]');

    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
    });

    colorText.addEventListener('input', function() {
        colorInput.value = this.value;
    });
});
</script>
@endsection