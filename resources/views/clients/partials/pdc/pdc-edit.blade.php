@extends('layouts.app', ['page' => 'Edit Pdc'])


@section('content')
<section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="">
            <span class="mb-0 h3">Edit PDC</span>
            <p>Edit details for <b>{{ $pdc->client->name }}</b></p>
        </div>
        <div class="d-flex align-items-center breadcrumb mb-0">
            <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                    href="{{ route('dashboard.unified') }}">Home</a></li>
            <li class="breadcrumb-item active">Client / Pdcs / Edit</li>
        </div>
    </div>
    <!-- Alerts Top-Right -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
        @if (session('success'))
        <div class="alert alert-success text-white bg-success alert-dismissible custom-alert  fade-in" role="alert"
            style="white-space: normal; word-wrap: break-word;">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade-in" role="alert"
            style="white-space: normal; word-wrap: break-word;">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>
</section>

<div class="content-wrapper">
    <section class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-200">
        <div class="mx-auto w-full">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div
                    class="bg-light from-indigo-600 shadow-sm to-indigo-800 text-center py-3 d-flex justify-between px-4">
                    <h2 class="text-xl font-bold flex items-center justify-center">
                        <i class="fas fa-edit mr-3"></i> Edit PDC #{{ $pdc->id }}
                    </h2>
                    <div>
                        <a href="{{ route('clients.show',$pdc->client->id) }}" class="btn btn-secondary">Back</a>
                        @if($pdc->activeInstallments->where('data1', '>', 0)->count() == 0 && $pdc->status !=
                        'Failed')
                        <!-- Fail Button Trigger -->
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#failPdcModal">
                            <i class="fas fa-times-circle me-1"></i> Fail PDC
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Fail PDC Modal -->
                <div class="modal fade" id="failPdcModal" tabindex="-1" role="dialog"
                    aria-labelledby="failPdcModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content rounded-3 shadow-lg border-0">
                            <div
                                class="modal-header d-flex justify-content-between align-items-center gap-3 bg-danger text-white">
                                <h5 class="modal-title" id="failPdcModalLabel">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Confirm Fail PDC
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                </button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <p class="text-lg">Are you sure you want to mark <strong>PDC #{{ $pdc->id
                                        }}</strong> as
                                    <span class="text-danger fw-bold">Failed</span>?
                                </p>
                                <p class="text-muted">Amount: Rs {{ number_format($pdc->commitment_amount, 2) }}</p>
                                <p class="text-muted">This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <form action="{{ route('pdcs.fail', $pdc->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger px-4">
                                        <i class="fas fa-times-circle me-1"></i> Yes, Fail It
                                    </button>
                                </form>
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form action="{{ route('pdcs.update', $pdc->id) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Party -->
                            <div>
                                <label for="client_id" class="text-xl font-awesome text-gray-800 mb-2">Party</label>
                                <div class="flex gap-3">
                                    <div class="position-relative searchable-dropdown w-100">
                                        <div class="dropdown w-100">
                                            <button class="form-control text-start dropdown-toggle-btn" type="button"
                                                id="clientDropdownButton">
                                                @if(old('client_id', $pdc->client_id))
                                                {{ $clients->where('id', old('client_id',
                                                $pdc->client_id))->first()->name ?? 'Select Party' }}
                                                @else
                                                Select Party
                                                @endif
                                            </button>

                                            <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                                                style="max-height: 300px; overflow-y: auto;">
                                                <li class="px-2 py-1 sticky-top bg-white border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm dropdown-search"
                                                        placeholder="Search party..." id="clientSearch">
                                                </li>
                                                @foreach ($clients as $client)
                                                <li>
                                                    <a href="#" class="dropdown-item dropdown-item-option"
                                                        data-value="{{ $client->id }}"
                                                        data-display="{{ $client->name }}"
                                                        data-agent-id="{{ $client->agent_id }}"
                                                        data-department="{{ $client->department }}"
                                                        data-department-id="{{ $client->department_id }}"
                                                        data-agent-name="{{ $client->agent->name ?? '' }}">
                                                        <small>{{ $client->name }}</small>
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                            <input type="hidden" name="client_id" id="client_id"
                                                class="dropdown-hidden-input"
                                                value="{{ old('client_id', $pdc->client_id) }}">
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 transition-all duration-200 flex items-center"
                                        data-bs-toggle="modal" data-bs-target="#addClientModal">
                                        <i class="fas fa-plus-circle mr-2"></i> Add
                                    </button>
                                </div>
                            </div>

                            <!-- PDC Amount -->
                            <div>
                                <label for="commitment_amountUpdate{{ $pdc->id }}"
                                    class="text-xl font-medium text-gray-800 mb-2">PDC Amount</label>
                                <input type="number" name="commitment_amount" id="commitment_amountUpdate{{ $pdc->id }}"
                                    value="{{ $pdc->commitment_amount }}"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                    aria-label="PDC Amount" readonly
                                    style="cursor: not-allowed; background-color: #f8f9fa;" required>
                            </div>

                            <!-- Salesman -->
                            <div>
                                <label for="agent_id" class="text-xl font-medium text-gray-800 mb-2">Salesman</label>
                                <div class="position-relative searchable-dropdown">
                                    <div class="dropdown w-100">
                                        <button class="form-control text-start dropdown-toggle-btn" type="button"
                                            id="agentDropdownButton">
                                            @if(old('agent_id', $pdc->agent_id))
                                            {{ $agents->where('id', old('agent_id', $pdc->agent_id))->first()->name ??
                                            'Select Salesman' }}
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
                                                    data-department="{{ $agent->department }}"
                                                    data-department-id="{{ $agent->department_id }}">
                                                    <small>{{ $agent->name }}</small>
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        <input type="hidden" name="agent_id" id="agent_id" class="dropdown-hidden-input"
                                            value="{{ old('agent_id', $pdc->agent_id) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Department -->
                            <div>
                                <label for="department_id"
                                    class="text-xl font-medium text-gray-800 mb-2">Department</label>
                                <select id="department_id" name="department_id" class="form-control" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $pdc->department_id)
                                        == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Installments Table -->
                            <div class="col-span-2">
                                <label class="text-xl font-medium text-gray-800 mb-3">Payment
                                    Installments</label>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full text-sm text-gray-700">
                                        <thead class="bg-indigo-50 text-indigo-800">
                                            <tr>
                                                <th class="px-4 py-3 font-semibold text-left">Cheque No</th>
                                                <th class="px-4 py-3 font-semibold text-left">Bank</th>
                                                <th class="px-4 py-3 font-semibold text-left">Installment Amount
                                                </th>
                                                <th class="px-4 py-3 font-semibold text-left">Due Date</th>
                                                <th class="px-5 py-3 font-semibold text-left">Status</th>
                                                <th class="px-4 py-3 font-semibold text-left">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="installmentsTable divide-y divide-gray-200">
                                            @foreach($pdc->activeInstallments as $key => $installment)
                                            <tr data-id="{{ $key }}"
                                                class="hover:bg-indigo-50 transition-all duration-200">
                                                <td class="px-2 py-2">
                                                    <input type="hidden" name="installments[{{ $key }}][id]"
                                                        value="{{ $installment->id }}">
                                                    <input type="number" name="installments[{{ $key }}][chequeno]"
                                                        value="{{ $installment->chequeno }}"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                        aria-label="Cheque Number" required>
                                                </td>
                                                <td class="px-2 py-2">
                                                    <input type="text" name="installments[{{ $key }}][bankName]"
                                                        value="{{ $installment->bankName }}"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                        aria-label="Bank Name" required>
                                                </td>
                                                <td class="px-2 py-2">
                                                    <input type="number" name="installments[{{ $key }}][amount]"
                                                        value="{{ $installment->amount }}"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 inputAmountUpdate{{ $pdc->id }}"
                                                        aria-label="Installment Amount" required>
                                                </td>
                                                <td class="px-2 py-2">
                                                    <input type="date" name="installments[{{ $key }}][due_date]"
                                                        value="{{ $installment->due_date }}"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                        aria-label="Due Date" required>
                                                </td>
                                                <td class="px-2 py-2">
                                                    <select name="installments[{{ $key }}][status]"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                        aria-label="Installment Status" required>
                                                        <option value="Pending" {{ $installment->status == 'Pending'
                                                            ? 'selected' : '' }}>Pending</option>
                                                        <option value="Complete" {{ $installment->status ==
                                                            'Complete' ? 'selected' : '' }}>Complete</option>
                                                        <option value="Overdue" {{ $installment->status == 'Overdue'
                                                            ? 'selected' : '' }}>Overdue</option>
                                                    </select>
                                                </td>
                                                <td class="px-2 py-2">
                                                    @if($installment->data1 == 0)
                                                    <button type="button"
                                                        class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition-all duration-200 removeInstallmentUpdate recordId{{ $pdc->id }}"
                                                        aria-label="Remove Installment">X</button>
                                                    @endif
                                                    @if($installment->data1 == 0 && $installment->status !=
                                                    'Failed')
                                                    <button type="button"
                                                        class="bg-orange-600 text-white px-3 py-1 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 transition-all duration-200"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#failPdcInstallmentModal{{ $installment->id }}"
                                                        aria-label="Fail Installment">Fail</button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button"
                                    class="mt-4 bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 transition-all duration-200 addInstallmentRowBtn recordId{{ $pdc->id }}">Add
                                    New Installment</button>
                            </div>

                            <!-- PDC Details -->
                            <div class="col-span-2">
                                <label for="remarks" class="text-xl font-medium text-gray-800 mb-2">PDC
                                    Details</label>
                                <textarea id="remarks" name="remarks" placeholder="Enter PDC Details"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 min-h-[100px]"
                                    aria-label="PDC Details" required>{{ $pdc->remarks }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-8">
                            <button type="submit"
                                class="bg-green-600 text-white px-3 py-3 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-all duration-200 flex items-center">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Remove Installment Confirmation Modal -->
        <div class="modal fade" id="removeInstallmentModal" tabindex="-1" role="dialog"
            aria-labelledby="removeInstallmentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg rounded-3">
                    <div
                        class="modal-header d-flex justify-content-between align-items-center gap-3 bg-danger text-white">
                        <h5 class="modal-title" id="removeInstallmentModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i> Confirm Removal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="fs-5 fw-semibold">Are you sure you want to remove this installment?</p>
                        <p class="text-muted">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger px-4" id="confirmRemoveInstallment">
                            Yes, Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fail PDC Installment Confirmation Modals -->
        @foreach($pdc->activeInstallments as $installment)
        <div class="modal fade" id="failPdcInstallmentModal{{ $installment->id }}" tabindex="-1" role="dialog"
            aria-labelledby="failPdcInstallmentModalLabel{{ $installment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg rounded-3">
                    <div
                        class="modal-header d-flex justify-content-between align-items-center gap-3 bg-danger text-white">
                        <h5 class="modal-title" id="failPdcInstallmentModalLabel{{ $installment->id }}">
                            <i class="fas fa-exclamation-triangle me-2"></i> Confirm Fail Installment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="fs-5 fw-semibold">Are you sure you want to mark this installment as <span
                                class="text-danger">Failed</span>?</p>
                        <p class="text-muted">Amount: Rs {{ number_format($installment->amount, 2) }}</p>
                        <p class="text-muted">Due Date: {{ $installment->due_date }}</p>
                        <p class="text-danger small">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <form action="{{ route('pdcInstallments.fail', $installment->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-exclamation-triangle me-1"></i> Yes, Fail It
                            </button>
                        </form>
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Pay Modal -->
        @foreach($pdc->activeInstallments as $installment)
        <div class="modal fade" id="payModal{{ $installment->id }}" tabindex="-1" role="dialog"
            aria-labelledby="payModalLabel{{ $installment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg rounded">
                    <div class="modal-header ">
                        <h5 class="modal-title" id="payModalLabel{{ $installment->id }}">
                            <i class="fas fa-wallet me-2"></i> Partial Payment
                        </h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form action="{{ route('pdcInstallments.pay', $installment->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="installment_id" value="{{ $installment->id }}">
                        <input type="hidden" name="pc_id" value="{{ $pdc->id }}">
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label for="pay_amount_{{ $installment->id }}" class="fw-bold">
                                    <i class="fas fa-money-bill-wave me-1"></i> Amount to Pay
                                </label>
                                <input type="number" class="form-control rounded" id="pay_amount_{{ $installment->id }}"
                                    name="pay_amount" placeholder="Enter Amount" min="1"
                                    max="{{ $installment->amount - ($installment->data1 ?? 0) }}" required>
                                @error('pay_amount')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="remarks_{{ $installment->id }}" class="fw-bold">
                                    <i class="fas fa-comment me-1"></i> Remarks
                                </label>
                                <textarea class="form-control rounded" id="remarks_{{ $installment->id }}"
                                    name="remarks" placeholder="Enter Remarks" rows="3"></textarea>
                                @error('remarks')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer p-3">
                            <button type="button" class="btn btn-danger fw-bold" data-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="fas fa-check me-1"></i> Confirm Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            $(document).ready(function() {
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

                                function renumberRows(tbody) {
                                    const rows = tbody.find("tr");
                                    rows.each(function(index) {
                                        $(this).find("input, select").each(function() {
                                            let name = $(this).attr("name");
                                            if (name) {
                                                name = name.replace(/installments\[\d+\]/, `installments[${index}]`);
                                                $(this).attr("name", name);
                                            }
                                        });
                                    });
                                }

                                // Add new installment row
                                $(".addInstallmentRowBtn").off("click").on("click", function(e) {
                                    e.preventDefault();
                                    const recordId = $(this).attr("class").match(/recordId(\d+)/)[1];
                                    const installmentsTable = $(this).closest(".p-6").find(".installmentsTable");
                                    const rowCount = installmentsTable.find("tr").length;

                                    const newRow = `
                                        <tr class="hover:bg-indigo-50 transition-all duration-200">
                                            <td class="px-2 py-2">
                                                <input type="number" name="installments[${rowCount}][chequeno]" placeholder="Cheque No" class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" aria-label="Cheque Number" required>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text" name="installments[${rowCount}][bankName]" placeholder="Bank Name" class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" aria-label="Bank Name" required>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="number" name="installments[${rowCount}][amount]" placeholder="Amount" class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 inputAmountUpdate${recordId}" aria-label="Installment Amount" required>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="date" name="installments[${rowCount}][due_date]" class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" aria-label="Due Date" required>
                                            </td>
                                            <td class="px-2 py-2">
                                                <select name="installments[${rowCount}][status]" class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" aria-label="Installment Status" required>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Complete">Complete</option>
                                                    <option value="Overdue">Overdue</option>
                                                </select>
                                            </td>
                                            <td class="px-2 py-2">
                                                <button type="button" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition-all duration-200 removeInstallmentUpdate recordId${recordId}" aria-label="Remove Installment">X</button>
                                            </td>
                                        </tr>
                                    `;
                                    installmentsTable.append(newRow);
                                    renumberRows(installmentsTable);
                                });

                                // Update total amount
                                function updateTotalEdit(recordId) {
                                    let total = 0;
                                    const inputs = document.querySelectorAll(`.inputAmountUpdate${recordId}`);
                                    inputs.forEach(input => {
                                        total += parseFloat(input.value) || 0;
                                    });
                                    const totalField = document.getElementById(`commitment_amountUpdate${recordId}`);
                                    if (totalField) {
                                        totalField.value = total.toFixed(2);
                                    }
                                }

                                // Handle input changes for amount fields
                                document.addEventListener("input", function(event) {
                                    const inputField = event.target;
                                    const match = inputField.className.match(/inputAmountUpdate(\d+)/);
                                    if (match) {
                                        const recordId = match[1];
                                        updateTotalEdit(recordId);
                                    }
                                });

                                // Handle remove installment button with confirmation modal
                                let rowToRemove = null; // Store the row to be removed
                                let currentRecordId = null; // Store the recordId for total update

                                document.addEventListener("click", function(event) {
                                    if (event.target.classList.contains("removeInstallmentUpdate")) {
                                        event.preventDefault();
                                        rowToRemove = event.target.closest("tr"); // Store the row
                                        const match = event.target.className.match(/recordId(\d+)/);
                                        if (match) {
                                            currentRecordId = match[1]; // Store the recordId
                                            $('#removeInstallmentModal').modal('show'); // Show the confirmation modal
                                        }
                                    }
                                });

                                // Handle confirmation of removal
                                $('#confirmRemoveInstallment').on('click', function() {
                                    if (rowToRemove) {
                                        const installmentsTable = $(rowToRemove).closest('.installmentsTable');
                                        rowToRemove.remove(); // Remove the row
                                        renumberRows(installmentsTable); // Renumber the remaining rows
                                        updateTotalEdit(currentRecordId); // Update the total
                                        $('#removeInstallmentModal').modal('hide'); // Hide the modal
                                        rowToRemove = null; // Clear the stored row
                                        currentRecordId = null; // Clear the stored recordId
                                    }
                                });

                // Initialize everything
                initializeDropdowns();
                updateTotalEdit({{ $pdc->id }});
            });
        </script>
    </section>
</div>

<style>
    .modal.show {
        display: flex !important;
    }
</style>
<!-- Add Client Modal -->
<div class="modal fade align-items-center" id="addClientModal" tabindex="-1" role="dialog"
    aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center gap-3">
                <h5 class="modal-title" id="addClientModalLabel"><i class="fas fa-user-plus"></i> Add New Party</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body px-4">
                <form action="{{ route('clients.store') }}" method="POST" id="addClientForm">
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Party Name"
                                    required>
                                <label for="name">Party Name</label>
                            </div>
                            @error('name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="address" name="address"
                                    placeholder="Party Address" required>
                                <label for="address">Party Address</label>
                            </div>
                            @error('address')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="Party Phone">
                                <label for="phone">Party Phone</label>
                            </div>
                            @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select form-control" id="modal_agent_id" name="agent_id" required>
                                    <option value="" disabled selected>-- Select Salesman --</option>
                                    @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" data-department-id="{{ $agent->department_id }}">
                                        {{ $agent->name }} ({{ $agent->department }})
                                    </option>
                                    @endforeach
                                </select>
                                <label for="modal_agent_id">Salesman</label>
                            </div>
                            @error('agent_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select form-control" id="modal_department_id" name="department_id"
                                    required>
                                    <option value="" disabled selected>-- Select Department --</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <label for="modal_department_id">Department</label>
                            </div>
                            @error('department_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Save Party
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

@endsection

@section('customJs')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('admincss/js/script.js') }}"></script>
@endsection
