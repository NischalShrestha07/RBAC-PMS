@extends('layouts.superApp', ['page' => 'Edit Client'])

@section('content')
<section class="content-header d-flex gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('dashboard.unified') }}">Home</a></li>
        <li class="breadcrumb-item active">Clients/Edit</li>
    </div>
</section>
<div class="card">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-light text-center py-3 shadow-sm d-flex justify-content-between align-items-center px-4">
            <h4 class="text-xl font-bold flex items-center justify-center">
                <i class="fas fa-edit"></i> Edit Client
            </h4>
            <div>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <!-- Form -->
        <div class="p-4">
            <form action="{{ route('clients.update', $client) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name <span class="text-danger fw-bold">*</span></label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $client->name) }}" placeholder="Enter client name" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $client->phone) }}" placeholder="Enter client phone">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="agent_id" class="fw-bold mb-1">Salesman</label>
                        <div class="position-relative searchable-dropdown">
                            <div class="dropdown w-100">
                                <button class="form-control text-start dropdown-toggle-btn" type="button"
                                    id="agentDropdownButton">
                                    @if(old('agent_id', $client->agent_id))
                                    @php
                                    $selectedAgent = $agents->where('id', old('agent_id', $client->agent_id))->first();
                                    @endphp
                                    {{ $selectedAgent->name ?? 'Select Salesman' }}
                                    @else
                                    Select Salesman
                                    @endif
                                </button>

                                <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                                    style="max-height: 250px; overflow-y: auto;">
                                    <li class="px-2 py-1 sticky-top bg-white border-bottom">
                                        <input type="text" class="form-control form-control-sm dropdown-search"
                                            placeholder="Search salesman..." id="agentSearch">
                                    </li>
                                    @foreach ($agents as $agent)
                                    <li>
                                        <a href="#" class="dropdown-item dropdown-item-option"
                                            data-value="{{ $agent->id }}" data-display="{{ $agent->name }}"
                                            data-department="{{ $agent->department_id }}">
                                            <!-- Changed to department_id -->
                                            <small>{{ $agent->name }}</small>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="agent_id" id="agent_id" class="dropdown-hidden-input"
                                    value="{{ old('agent_id', $client->agent_id) }}">
                            </div>
                        </div>
                        @error('agent_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="department_id" class="font-weight-bold">Department:</label>
                        <select id="department_id" name="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $client->department_id) ==
                                $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label">Address <span
                                class="text-danger fw-bold">*</span></label>
                        <input type="text" name="address" id="address"
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="Enter client address" required value="{{ old('address', $client->address)}}">
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i style="color: white" class="fas fa-save mr-1"></i> Update Client
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize dropdown functionality
        function initializeDropdowns() {
            // Agent dropdown
            $('#agentDropdownButton').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dropdownMenu = $(this).next('.dropdown-menu');
                dropdownMenu.toggle();
            });

            // Dropdown item selection
            $('.dropdown-item-option').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const value = $(this).data('value');
                const display = $(this).data('display');
                const departmentId = $(this).data('department'); // Changed variable name

                const dropdown = $(this).closest('.dropdown');
                const hiddenInput = dropdown.find('.dropdown-hidden-input');
                const button = dropdown.find('.dropdown-toggle-btn');

                // Update dropdown display
                hiddenInput.val(value);
                button.text(display);
                dropdown.find('.dropdown-menu').hide();

                // Auto-fill department if department exists - FIXED: targeting department_id
                if (departmentId && departmentId !== '') {
                    $('#department_id').val(departmentId);
                }
            });

            // Dropdown search functionality - Prevent closing when clicking on search
            $('.dropdown-search').on('click', function(e) {
                e.stopPropagation(); // Prevent the click from bubbling up to document
            });

            $('.dropdown-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                const dropdownMenu = $(this).closest('.dropdown-menu');

                dropdownMenu.find('.dropdown-item').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                // Only close if the click is not inside any dropdown
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').hide();
                }
            });

            // Prevent dropdown from closing when interacting with it
            $('.dropdown-menu').on('click', function(e) {
                e.stopPropagation();
            });
        }

        // Initialize everything
        initializeDropdowns();
    });
</script>
@endsection