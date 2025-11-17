@extends('layouts.electronicsApp', ['page' => 'Clients'])

@section('content')
<section class="content-header d-flex gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('electronics.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Clients</li>
    </div>
</section>
<section class="content mt-4 mb-2">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Search & Filter Parties
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('electronics.clients.index') }}" method="GET" id="searchForm">
                <div class="row g-2 align-items-end">
                    <!-- Client Name -->
                    <div class="col-md-4">
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
                    <div class="col-md-3">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="address" name="address"
                                placeholder="Enter address..." value="{{ request('address') }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-flex gap-1 align-items-center">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('electronics.clients.index') }}" class="btn btn-secondary w-100">
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

                <td>
                    @php
                    $salesmen = [];
                    // Get salesmen from commitments
                    foreach($client->commitments as $commitment) {
                    if($commitment->agent && $commitment->agent->name && $commitment->agent->department ===
                    'Electronics') {
                    $salesmen[] = $commitment->agent->name;
                    }
                    }
                    // Get salesmen from PDCs
                    foreach($client->pdcs as $pdc) {
                    if($pdc->agent && $pdc->agent->name && $pdc->agent->department === 'Electronics') {
                    $salesmen[] = $pdc->agent->name;
                    }
                    }

                    $salesmen = array_unique($salesmen); // remove duplicates
                    $primarySalesman = !empty($salesmen) ? $salesmen[0] : null;
                    $additionalCount = count($salesmen) - 1;
                    @endphp

                    @if($primarySalesman)
                    <div class="text-center">
                        <div class="mb-1">{{ $primarySalesman }}</div>

                        @if($additionalCount > 0)
                        <div>
                            <button type="button" data-bs-toggle="modal"
                                class="border-0 bg-transparent p-0 text-primary"
                                data-bs-target="#salesmenModal{{ $client->id }}">
                                +{{ $additionalCount }} more
                            </button>
                        </div>
                        @endif
                    </div>
                    @else
                    <span class="text-muted">--</span>
                    @endif
                </td>

                <td>{{ number_format($client->active_commitment_amount, 2) }}</td>
                <td>{{ number_format($client->active_pdc_amount, 2) }}</td>
                <td>
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('electronics.clients.show', $client->id) }}" class="btn btn-action p-2"
                            title="View" aria-label="View Client">
                            <i class="fas fa-eye fa-lg text-primary"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @if(!empty($salesmen))
            <div class="modal fade" id="salesmenModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">
                                <i class="fas fa-users text-primary"></i> All Salesmen
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
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
            @endif
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-users fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No clients found</h5>
                        <p class="text-muted small">Try adjusting your search criteria</p>
                        <a href="{{ route('electronics.clients.index') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-refresh me-1"></i> Clear Filters
                        </a>
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

@endsection