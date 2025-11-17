@extends('layouts.app', ['page' => 'Create Commitment'])

@section('content')
<section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="">
            <span class="mb-0 h3">Create Commitment</span>
            <p>Add a new Commitment record</p>
        </div>
        <div class="d-flex align-items-center breadcrumb mb-0">
            <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                    href="{{ route('dashboard.unified') }}">Home</a></li>
            <li class="breadcrumb-item active">Commitments / Create</li>
        </div>
    </div>
</section>
<div class="card">
    <!-- Header -->
    <div class="bg-light text-center shadow-sm py-3 d-flex justify-content-between px-4">
        <h3 class="text-xl fw-bold flex items-center justify-center">
            <i class="fas fa-edit mr-2"></i> Create Commitment
        </h3>
        <div>
            <a href="{{ route('commitments.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <!-- Form -->
    <div class="p-4 p-md-4">
        <form action="{{ route('commitments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="client_id" class="fw-bold mb-1">Party:</label>
                    <div class="input-group flex-nowrap align-items-center text-nowrap">
                        <div class="position-relative searchable-dropdown w-100">
                            <div class="dropdown w-100">
                                <button class="form-control text-start dropdown-toggle-btn" type="button"
                                    id="clientDropdownButton">
                                    @if(old('client_id'))
                                    {{ $clients->where('id', old('client_id'))->first()->name ?? 'Select Party' }}
                                    @else
                                    Select Party
                                    @endif
                                </button>

                                <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                                    style="max-height: 300px; overflow-y: auto;">
                                    <li class="px-2 py-1 sticky-top bg-white border-bottom">
                                        <input type="text" class="form-control form-control-sm dropdown-search"
                                            placeholder="Search party..." id="clientSearch">
                                    </li>
                                    @foreach ($clients as $client)
                                    <li>
                                        <a href="#" class="dropdown-item dropdown-item-option"
                                            data-value="{{ $client->id }}" data-display="{{ $client->name }}"
                                            data-agent-id="{{ $client->agent_id }}"
                                            data-department="{{ $client->department }}"
                                            data-department-id="{{ $client->department_id }}"
                                            data-agent-name="{{ $client->agent->name ?? '' }}">
                                            <small>{{ $client->name }}</small>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="client_id" id="client_id" class="dropdown-hidden-input"
                                    value="{{ old('client_id') }}">
                            </div>
                        </div>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addClientModal">
                                <i class="fas fa-plus-circle text-white"></i> Add
                            </button>

                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="agent_id" class="fw-bold mb-1">Salesman</label>
                    <div class="position-relative searchable-dropdown">
                        <div class="dropdown w-100">
                            <button class="form-control text-start dropdown-toggle-btn" type="button"
                                id="agentDropdownButton">
                                @if(old('agent_id'))
                                {{ $agents->where('id', old('agent_id'))->first()->name ?? 'Select Salesman' }}
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
                                    <a href="#" class="dropdown-item dropdown-item-option" data-value="{{ $agent->id }}"
                                        data-display="{{ $agent->name }}" data-department="{{ $agent->department }}"
                                        data-department-id="{{ $agent->department_id }}">
                                        <small>{{ $agent->name }}</small>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="agent_id" id="agent_id" class="dropdown-hidden-input"
                                value="{{ old('agent_id') }}">
                        </div>
                    </div>
                    @error('agent_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-4">
                <div class="form-group col-md-6">
                    <label for="department_id" class="font-weight-bold">Department:</label>
                    <select id="department_id" name="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id')==$department->id ? 'selected' : ''
                            }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('department_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Total Amount -->
                <div class="col-md-6">
                    <label for="total_amount" class="fw-bold mb-1">Total
                        Amount:</label>
                    <input type="number" name="total_amount" id="total_amount" class="form-control"
                        placeholder="Enter Total Amount" style="cursor:not-allowed; background-color: white;" readonly
                        value="{{ old('total_amount') }}" required>
                    @error('total_amount')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-12">
                    <label class="fw-bold h5">Installments:</label>
                    <table class="table" id="installmentsTable">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" name="installments[0][amount]" placeholder="Enter Amount"
                                        class="form-control amountInput" required>
                                </td>
                                <td><input type="date" name="installments[0][due_date]" class="form-control"
                                        value="{{ old('installments.0.due_date') }}" required>
                                    @error('installments.0.due_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <select name="installments[0][status]" class="form-control">
                                        <option value="Pending" {{ old('installments.0.status')=='Pending' ? 'selected'
                                            : '' }}>Pending</option>
                                        <option value="Complete" {{ old('installments.0.status')=='Complete'
                                            ? 'selected' : '' }}> Complete</option>
                                        <option value="Overdue" {{ old('installments.0.status')=='Overdue' ? 'selected'
                                            : '' }}>Overdue
                                        </option>
                                    </select>
                                    @error('installments.0.status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td><button type="button" class="btn btn-secondary">X</button>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary mb-3" id="addInstallment">
                        <i class="fas fa-plus"></i> Add New Installment</button>
                </div>
            </div>

            <!-- Remarks -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="remarks" class="fw-bold mb-1">Commitment
                        Details:</label>
                    <textarea name="remarks" id="remarks" class="form-control" placeholder="Commitment Details" required
                        rows="4">{{ old('remarks') }}</textarea>
                    @error('remarks')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success px-4 py-2 fs-5">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

{{-- party modal --}}
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Increased modal size for better readability -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addClientModalLabel"><i class="fas fa-user-plus"></i>
                    Add New Party</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('clients.store') }}" method="POST" id="addClientForm">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6 mb-1">
                            <label for="modal_name" class="form-label fw-bold">Party Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="modal_name"
                                placeholder="Party Name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-md-6 mb-1">
                            <label for="modal_address" class="form-label fw-bold">Party Address *</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                placeholder="Address" id="modal_address" name="address" value="{{ old('address') }}"
                                required>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-1">
                            <label for="modal_phone" class="form-label fw-bold">Party Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                placeholder="Phone" id="modal_phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-1">
                            <label for="modal_agent_id" class="form-label fw-bold">Salesman *</label>
                            <select class="form-control @error('agent_id') is-invalid @enderror" id="modal_agent_id"
                                name="agent_id" required>
                                <option value="">Select Salesman</option>
                                @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" data-department-id="{{ $agent->department_id }}" {{
                                    old('agent_id')==$agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }} ({{ $agent->department }})
                                </option>
                                @endforeach
                            </select>
                            @error('agent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-1">
                            <label for="modal_department_id" class="form-label fw-bold">Department *</label>
                            <select class="form-control @error('department_id') is-invalid @enderror"
                                id="modal_department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id')==$department->id ?
                                    'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('department_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        Save Party
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
    let installmentCount = 1;

    // Function to auto-fill salesman and department when party is selected
    function autoFillPartyDetails(agentId, agentName, departmentId) {
        // Auto-fill salesman
        if (agentId && agentId !== '') {
            $('#agent_id').val(agentId);
            $('#agentDropdownButton').text(agentName || 'Select Salesman');
        }

        // Auto-fill department
        if (departmentId && departmentId !== '') {
            $('#department_id').val(departmentId);
        }
    }

    // Function to auto-fill department when salesman is selected in main form
    function autoFillDepartmentFromSalesman(agentId) {
        if (agentId && agentId !== '') {
            const selectedAgent = $('#agentDropdownButton').closest('.dropdown')
                .find(`.dropdown-item-option[data-value="${agentId}"]`);

            if (selectedAgent.length) {
                const departmentId = selectedAgent.data('department-id');
                if (departmentId && departmentId !== '') {
                    $('#department_id').val(departmentId);
                }
            }
        }
    }

    // Initialize dropdown functionality
    function initializeDropdowns() {
        // Client dropdown
        $('#clientDropdownButton').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdownMenu = $(this).next('.dropdown-menu');
            $('.dropdown-menu').not(dropdownMenu).hide(); // Close other dropdowns
            dropdownMenu.toggle();
        });

        // Agent dropdown
        $('#agentDropdownButton').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdownMenu = $(this).next('.dropdown-menu');
            $('.dropdown-menu').not(dropdownMenu).hide(); // Close other dropdowns
            dropdownMenu.toggle();
        });

        // Dropdown item selection
        $('.dropdown-item-option').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const value = $(this).data('value');
            const display = $(this).data('display');
            const agentId = $(this).data('agent-id');
            const department = $(this).data('department');
            const departmentId = $(this).data('department-id');
            const agentName = $(this).data('agent-name');

            const dropdown = $(this).closest('.dropdown');
            const hiddenInput = dropdown.find('.dropdown-hidden-input');
            const button = dropdown.find('.dropdown-toggle-btn');

            // Update dropdown display
            hiddenInput.val(value);
            button.text(display);
            dropdown.find('.dropdown-menu').hide();

            // If this is the client dropdown, auto-fill salesman and department
            if (hiddenInput.attr('name') === 'client_id') {
                autoFillPartyDetails(agentId, agentName, departmentId);
            }

            // If this is the agent dropdown, auto-fill department
            if (hiddenInput.attr('name') === 'agent_id') {
                autoFillDepartmentFromSalesman(value);
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

    // Auto-set department when salesman is selected in modal
    $('#modal_agent_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const departmentId = selectedOption.data('department-id');
        if (departmentId) {
            $('#modal_department_id').val(departmentId);
        }
    });

    // Reset modal form when closed
    $('#addClientModal').on('hidden.bs.modal', function () {
        $('#addClientForm')[0].reset();
    });

    // Installment functionality
    $('#addInstallment').on('click', function() {
        const tableBody = $('#installmentsTable tbody');
        const newRow = `
            <tr>
                <td>
                    <input type="number" placeholder="Enter Amount"
                        name="installments[${installmentCount}][amount]"
                        class="form-control amountInput" required>
                </td>
                <td>
                    <input type="date" name="installments[${installmentCount}][due_date]"
                        class="form-control" required>
                </td>
                <td>
                    <select name="installments[${installmentCount}][status]" class="form-control">
                        <option value="Pending">Pending</option>
                        <option value="Complete">Complete</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm removeInstallment">
                    X</button>
                </td>
            </tr>`;
        tableBody.append(newRow);
        installmentCount++;

        // Enable remove buttons if there's more than one row
        if ($('#installmentsTable tbody tr').length > 1) {
            $('.removeInstallment').prop('disabled', false);
        }
    });

    // Remove installment row
    $(document).on('click', '.removeInstallment', function() {
        if ($('#installmentsTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            updateTotal();

            // Disable remove button if only one row remains
            if ($('#installmentsTable tbody tr').length === 1) {
                $('.removeInstallment:first').prop('disabled', true);
            }
        }
    });

    // Update total amount
    function updateTotal() {
        let total = 0;
        $('.amountInput').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_amount').val(total.toFixed(2));
    }

    $(document).on('input', '.amountInput', updateTotal);

    // Prevent manual input in total amount field
    $('#total_amount').on('keydown paste focus', function(e) {
        e.preventDefault();
        this.blur();
    });

    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut(300);

    // Initialize everything
    initializeDropdowns();
    updateTotal();

    // Set initial remove button state
    if ($('#installmentsTable tbody tr').length === 1) {
        $('.removeInstallment:first').prop('disabled', true);
    }
});
</script>
@endsection
