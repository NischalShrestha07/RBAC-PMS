@extends('layouts.app', ['page' => 'Clients'])

@section('content')
<section class="content-header d-flex gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('dashboard.unified') }}">Home</a></li>
        <li class="breadcrumb-item active">Clients</li>
    </div>
    <a href="{{ route('clients.create') }}" class="btn btn-success">
        <i class="fas fa-plus text-white"></i> Add New Client
    </a>

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
<!-- Search Section -->
<section class="content mt-4 mb-2">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Search & Filter Parties
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('clients.index') }}" method="GET" id="searchForm">
                <div class="row g-2 align-items-end">
                    <!-- Client Name -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Party</label>
                        <div class="position-relative searchable-dropdown">
                            <div class="dropdown w-100">
                                <button class="form-control text-start dropdown-toggle-btn" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ request('filter_client') ?? 'Select Party' }}
                                </button>

                                <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                                    style="max-height: 250px; overflow-y: auto;">
                                    <li class="px-2 py-1 sticky-top bg-white border-bottom">
                                        <input type="text" class="form-control form-control-sm dropdown-search"
                                            placeholder="Search party...">
                                    </li>
                                    @foreach ($clients as $client)
                                    <li>
                                        <a href="#" class="dropdown-item dropdown-item-option"
                                            data-value="{{ $client->name }}">
                                            <small>{{ $client->name }}</small> </a>
                                    </li>
                                    @endforeach
                                </ul>

                                <input type="hidden" name="filter_client" class="dropdown-hidden-input"
                                    value="{{ request('filter_client') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Salesman -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Salesman</label>
                        <div class="position-relative searchable-dropdown">
                            <div class="dropdown w-100">
                                <button class="form-control text-start dropdown-toggle-btn" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ request('salesman') ?? 'All Salesmen' }}
                                </button>

                                <ul class="dropdown-menu w-100 shadow-sm border-0 rounded-3"
                                    style="max-height: 250px; overflow-y: auto;">
                                    <li class="px-2 py-1 sticky-top bg-white border-bottom">
                                        <input type="text" class="form-control form-control-sm dropdown-search"
                                            placeholder="Search salesman...">
                                    </li>
                                    @foreach ($agents as $agent)
                                    <li>
                                        <a href="#" class="dropdown-item dropdown-item-option"
                                            data-value="{{ $agent }}">
                                            <small>{{ $agent }}</small>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>

                                <input type="hidden" name="salesman" class="dropdown-hidden-input"
                                    value="{{ request('salesman') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="col-md-2">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="address" name="address"
                                placeholder="Enter address..." value="{{ request('address') }}">
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-2">
                        <label for="department" class="form-label fw-semibold">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department')==$department->id ? 'selected'
                                : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-flex gap-1 align-items-center">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


<style>
    table td {
        vertical-align: middle !important;
    }
</style>

<div class="table-responsive">
    <table id="addEmployeeTable" class="table table-bordered table-striped mt-3 text-center text-nowrap mb-0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Salesman</th>
                <th>Active Commitment</th>
                <th>Active PDCs</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clients as $client)
            <tr>
                <td>{{ $client->name }}</td>
                <td>{{ $client->address ?? '--' }}</td>
                <td>{{ $client->phone ?? '--' }}</td>
                <td>{{ $client->department()->first() ? $client->department()->first()->name : '--' }}</td>
                <td>
                    @php
                    $salesmen = [];
                    // Get salesmen from commitments
                    foreach($client->commitments as $commitment) {
                    if($commitment->agent && $commitment->agent->name) {
                    $salesmen[] = $commitment->agent->name;
                    }
                    }
                    // Get salesmen from PDCs
                    foreach($client->pdcs as $pdc) {
                    if($pdc->agent && $pdc->agent->name) {
                    $salesmen[] = $pdc->agent->name;
                    }
                    }
                    $salesmen = array_unique($salesmen);
                    $primarySalesman = !empty($salesmen) ? $salesmen[0] : null;
                    $additionalCount = count($salesmen) - 1;
                    @endphp

                    @if($primarySalesman)
                    <div class="text-center">
                        <!-- Primary Salesman -->
                        <div class="mb-1">
                            {{ $primarySalesman }}
                        </div>

                        <!-- Additional Salesmen -->
                        @if($additionalCount > 0)
                        <div>
                            <button type="button" data-bs-toggle="modal"
                                class="border-0 bg-transparent p-0 text-primary"
                                data-bs-target="#salesmenModal{{ $client->id }}">
                                +{{ $additionalCount }} more
                            </button>
                        </div>

                        <!-- Simple Modal -->
                        <div class="modal fade" id="salesmenModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">
                                            <i class="fas fa-users text-primary"></i>
                                            All Salesmen
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="salesman-list">
                                            @foreach($salesmen as $salesman)
                                            <div class="salesman-item d-flex align-items-center py-2 border-bottom">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <span class="text-dark">{{ $salesman }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <span class="text-muted">--</span>
                    @endif
                </td>
                <td>{{ number_format($client->active_commitment_amount,2)}}</td>
                <td>{{ number_format($client->active_pdc_amount,2) }}</td>
                <td>
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-action" title="Edit"
                            aria-label="Edit Client">
                            <i class="fas fa-pencil-alt fa-lg text-success"></i>
                        </a>
                        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-action" title="View"
                            aria-label="View Client">
                            <i class="fas fa-eye fa-lg text-primary"></i>
                        </a>

                        <button type="button" class="btn btn-action" data-toggle="modal"
                            data-target="#deleteModal{{ $client->id }}" title="Delete" aria-label="Delete Client">
                            <i class="fas fa-trash-alt fa-lg text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal{{ $client->id }}" tabindex="-1" role="dialog"
                aria-labelledby="deleteModalLabel{{ $client->id }}" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div
                            class="modal-header d-flex justify-content-between align-items-center gap-3 bg-danger text-white">
                            <h5 class="modal-title" id="deleteModalLabel{{ $client->id }}">
                                Confirm Deletion</h5>
                            <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                                aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mb-2 fs-5">Are you sure you want to delete client {{
                                $client->name }}?</p>
                            <p class="text-muted small">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-secondary px-4 py-2"
                                data-dismiss="modal">Cancel</button>
                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger px-4 py-2">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-users fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No clients found</h5>
                        @if(request()->anyFilled(['name', 'address', 'salesman', 'status', 'follow_up', 'department']))
                        <p class="text-muted small">Try adjusting your search criteria</p>
                        <a href="{{ route('clients.index') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-refresh me-1"></i> Clear Filters
                        </a>
                        @else
                        <p class="text-muted small">Get started by adding your first client</p>
                        <a href="{{ route('clients.create') }}" class="btn btn-success mt-2">
                            <i class="fas fa-plus me-1"></i> Add New Client
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4" style="background: white;">
    {{ $clients->links() }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Clear individual filters
    const clearFilter = (fieldName) => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = '';
            document.getElementById('searchForm').submit();
        }
    };

    // Add clear buttons to active filter badges
    document.querySelectorAll('.badge').forEach(badge => {
        if (badge.textContent.includes(':')) {
            const filterText = badge.textContent.split(':')[0].trim().toLowerCase();
            const fieldMap = {
                'name': 'name',
                'address': 'address',
                'salesman': 'salesman',
                'status': 'status',
                'follow up': 'follow_up',
                'department': 'department'
            };

            if (fieldMap[filterText]) {
                badge.style.cursor = 'pointer';
                badge.title = 'Click to remove this filter';
                badge.addEventListener('click', () => clearFilter(fieldMap[filterText]));
            }
        }
    });
});
</script>

@endsection
