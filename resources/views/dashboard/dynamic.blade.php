@extends('layouts.superApp',['page' => 'Dashboard'])
@section('content')

<!-- Summary Cards Section -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card summary-card h-100">
            <div class="card-body position-relative p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title text-muted mb-2">Active COMs</h5>
                        <p class="card-text fs-3 fw-bold mb-0">Rs {{ number_format($activeCommitmentAmount) }}</p>
                    </div>
                    <div class="icon-container bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-file-invoice-dollar fs-2 text-success"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card summary-card h-100">
            <div class="card-body position-relative p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title text-muted mb-2">Overdue COMs</h5>
                        <p class="card-text fs-3 fw-bold mb-0">Rs {{ number_format($overdueCommitmentTotal) }}</p>
                    </div>
                    <div class="icon-container bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-clock fs-2 text-danger"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 45%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card summary-card h-100">
            <div class="card-body position-relative p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title text-muted mb-2">Active PDCs</h5>
                        <p class="card-text fs-3 fw-bold mb-0">Rs {{ number_format($activePdcAmount) }}</p>
                    </div>
                    <div class="icon-container bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-money-check-alt fs-2 text-warning"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card summary-card h-100">
            <div class="card-body position-relative p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title text-muted mb-2">Overdue PDCs</h5>
                        <p class="card-text fs-3 fw-bold mb-0">Rs {{ number_format($overduePdcTotal) }}</p>
                    </div>
                    <div class="icon-container bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-calendar-times fs-2 text-danger"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 30%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Recent Updates Card -->
        <div class="card mb-4">
            <div class="card-header bg-transparent border-bottom-0 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Updates</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Party</th>
                                <th>Type</th>
                                <th>Address</th>
                                <th>Amount(Rs)</th>
                                <th>Salesman</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Commitments Data -->
                            @foreach(App\Models\Commitment::with('agent')->whereNotIn('status',['Failed'])->latest()->limit(5)->get()
                            as $data)
                            <tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#detailsModal"
                                data-type="commitment" data-id="{{ $data->id }}"
                                data-client-name="{{ $data->client->name }}"
                                data-installments="{{ json_encode($data->installments) }}">
                                <td class="fw-semibold">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-sm bg-light rounded me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-building text-secondary"></i>
                                        </div>
                                        <div>{{ $data->client->name }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">COMs</span></td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $data->client->address }}</td>
                                <td class="fw-bold">{{ number_format($data->total_amount, 2) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-primary fs-6"></i>
                                        </div>
                                        <div>{{ $data->agent->name ?? 'N/A' }}</div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                            <!-- PDC Data -->
                            @foreach($pdcsData as $data)
                            <tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#detailsModal"
                                data-type="pdc" data-id="{{ $data->id }}" data-client-name="{{ $data->client->name }}"
                                data-installments="{{ json_encode($data->installments) }}">
                                <td class="fw-semibold">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-sm bg-light rounded me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-building text-secondary"></i>
                                        </div>
                                        <div>{{ $data->client->name }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">PDCs</span></td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $data->client->address }}</td>
                                <td class="fw-bold">{{ number_format($data->commitment_amount, 2) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-primary fs-6"></i>
                                        </div>
                                        <div>{{ $data->agent->name ?? 'N/A' }}</div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Department Tabs Section -->
        <div class="card">
            <div class="card-header bg-transparent border-bottom-0 pb-2">
                <h5 class="card-title mb-0">Department Overview</h5>
            </div>
            <div class="card-body pt-0">
                <ul class="nav nav-pills mb-3" id="departmentTabs" role="tablist">
                    @foreach($departments as $index => $dept)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index == 0 ? 'active' : '' }}" id="dept-tab-{{ $dept->id }}"
                            data-bs-toggle="pill" data-bs-target="#deptSection-{{ $dept->id }}" type="button"
                            role="tab">
                            {{ $dept->name }}
                        </button>
                    </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach($departments as $index => $dept)
                    @php
                    $commitments = $departmentData[$dept->id]['commitments'];
                    $pdcs = $departmentData[$dept->id]['pdcs'];
                    @endphp

                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="deptSection-{{ $dept->id }}"
                        role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Entry Date</th>
                                        <th>Type</th>
                                        <th>Party</th>
                                        <th>Amount</th>
                                        <th>Received</th>
                                        <th>Pending</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($commitments->isEmpty() && $pdcs->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-muted text-center py-4">No data found for this
                                            department</td>
                                    </tr>
                                    @else
                                    {{-- Commitments --}}
                                    @foreach($commitments as $item)
                                    @php
                                    $totalPaidAmount = $item->installments->where('status','Complete')->sum('amount');
                                    $totalPendingAmount = $item->total_amount - $totalPaidAmount;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-success">COMs</span></td>
                                        <td>{{ $item->client->name }}</td>
                                        <td class="fw-bold">Rs {{ number_format($item->total_amount, 2) }}</td>
                                        <td class="text-success">Rs {{ number_format($totalPaidAmount, 2) }}</td>
                                        <td class="text-danger">Rs {{ number_format($totalPendingAmount, 2) }}</td>
                                        <td><span class="badge bg-warning">{{ ucfirst($item->status) }}</span></td>
                                    </tr>
                                    @endforeach

                                    {{-- PDCs --}}
                                    @foreach($pdcs as $main)
                                    <tr>
                                        <td>{{ $main->created_at->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-info">PDCs</span></td>
                                        <td>{{ $main->client->name }}</td>
                                        <td class="fw-bold">Rs {{ number_format($main->commitment_amount, 2) }}</td>
                                        <td class="text-success">Rs {{
                                            number_format($main->installments->where('status','Complete')->sum('amount'),
                                            2) }}</td>
                                        <td class="text-danger">Rs {{ number_format($main->commitment_amount -
                                            $main->installments->where('status','Complete')->sum('amount'), 2) }}</td>
                                        <td><span class="badge bg-warning">{{ ucfirst($main->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Department Agents Overview -->
        <div class="card mb-4">
            <div class="card-header bg-transparent border-bottom-0 pb-2">
                <h5 class="card-title mb-0">Team Performance</h5>
            </div>
            <div class="card-body pt-0">
                <div class="accordion" id="departmentAccordion">
                    @foreach($departmentAgents as $deptStats)
                    <div class="accordion-item border-0 mb-2">
                        <h2 class="accordion-header" id="heading{{ $loop->index }}">
                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false"
                                aria-controls="collapse{{ $loop->index }}">
                                {{ $deptStats['department']->name }}
                            </button>
                        </h2>
                        <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse"
                            aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#departmentAccordion">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th>Salesman</th>
                                                <th>Type</th>
                                                <th>Total</th>
                                                <th>Success</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($deptStats['agents']->take(5) as $stats)
                                            @if($stats['commitments_total'] > 0)
                                            <tr class="text-center">
                                                <td class="text-start">{{ $stats['agent']->name ?? 'N/A' }}</td>
                                                <td><span class="badge bg-success">COMs</span></td>
                                                <td>{{ $stats['commitments_total'] }}</td>
                                                <td class="text-success">{{ $stats['commitments_success'] }}</td>
                                            </tr>
                                            @endif

                                            @if($stats['pdcs_total'] > 0)
                                            <tr class="text-center">
                                                <td class="text-start">{{ $stats['agent']->name ?? 'N/A' }}</td>
                                                <td><span class="badge bg-info">PDCs</span></td>
                                                <td>{{ $stats['pdcs_total'] }}</td>
                                                <td class="text-success">{{ $stats['pdcs_success'] }}</td>
                                            </tr>
                                            @endif

                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-2">No agents found for this
                                                    department.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header bg-transparent border-bottom-0 pb-2">
                <h5 class="card-title mb-0">Quick Stats</h5>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <div class="text-primary mb-1">
                                <i class="fas fa-users fs-4"></i>
                            </div>
                            <h5 class="mb-0">{{ $agents->count() }}</h5>
                            <small class="text-muted">Salesmen</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <div class="text-success mb-1">
                                <i class="fas fa-handshake fs-4"></i>
                            </div>
                            <h5 class="mb-0">{{ $party->count() }}</h5>
                            <small class="text-muted">Parties</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <div class="text-info mb-1">
                                <i class="fas fa-building fs-4"></i>
                            </div>
                            <h5 class="mb-0">{{ $departments->count() }}</h5>
                            <small class="text-muted">Departments</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <div class="text-warning mb-1">
                                <i class="fas fa-chart-line fs-4"></i>
                            </div>
                            <h5 class="mb-0">{{ $departmentAgents->count() }}</h5>
                            <small class="text-muted">Teams</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Salesman & Party Summary Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom-0 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Salesman Summary</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Salesman</th>
                                <th>Type</th>
                                <th>Total</th>
                                <th>Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($agents->take(6) as $agent)
                            @if($agent->total_amount > 0 || $agent->commitment_amount > 0)
                            @if($agent->total_amount > 0)
                            <tr data-bs-toggle="modal" data-bs-target="#salesmanModal{{ $agent->id }}"
                                class="clickable-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-primary fs-6"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $agent->name }}</div>
                                            <small class="text-muted">{{ $agent->department ? $agent->department->name :
                                                '--' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">COMs</span></td>
                                <td class="fw-bold">{{ number_format($agent->total_amount, 2) }}</td>
                                <td class="text-danger">{{ number_format($agent->pending_amount, 2) }}</td>
                            </tr>
                            @endif

                            @if($agent->commitment_amount > 0)
                            <tr data-bs-toggle="modal" data-bs-target="#salesmanModal{{ $agent->id }}"
                                class="clickable-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-primary fs-6"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $agent->name }}</div>
                                            <small class="text-muted">{{ $agent->department ? $agent->department->name :
                                                '--' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">PDCs</span></td>
                                <td class="fw-bold">{{ number_format($agent->commitment_amount, 2) }}</td>
                                <td class="text-danger">{{ number_format($agent->pdc_pending, 2) }}</td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom-0 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Party Summary</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Party</th>
                                <th>Type</th>
                                <th>Total</th>
                                <th>Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($party->take(6) as $client)
                            @if ($client->total_amount > 0 || $client->commitment_amount > 0)
                            @if ($client->total_amount > 0)
                            <tr data-bs-toggle="modal" data-bs-target="#partyModal{{ $client->id }}"
                                class="clickable-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-xs bg-secondary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-building text-secondary fs-6"></i>
                                        </div>
                                        <div class="fw-semibold">{{ $client->name }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">COMs</span></td>
                                <td class="fw-bold">{{ number_format($client->total_amount, 2) }}</td>
                                <td class="text-danger">{{ number_format($client->pending_amount, 2) }}</td>
                            </tr>
                            @endif

                            @if ($client->commitment_amount > 0)
                            <tr data-bs-toggle="modal" data-bs-target="#partyModal{{ $client->id }}"
                                class="clickable-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-xs bg-secondary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-building text-secondary fs-6"></i>
                                        </div>
                                        <div class="fw-semibold">{{ $client->name }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">PDCs</span></td>
                                <td class="fw-bold">{{ number_format($client->commitment_amount, 2) }}</td>
                                <td class="text-danger">{{ number_format($client->pdc_pending, 2) }}</td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single Modal for Installment Details -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title fw-bold" id="detailsModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-center fw-bold mb-3" id="modalSubtitle"></h6>
                <div class="d-grid recentUpdateModal" id="installmentsContainer"></div>
            </div>
        </div>
    </div>
</div>

<!-- Salesman Modals -->
@foreach ($agents as $agent)
<div class="modal fade" id="salesmanModal{{ $agent->id }}" tabindex="-1" aria-labelledby="salesmanLabel{{ $agent->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="salesmanLabel{{ $agent->id }}">
                    {{ $agent->name }} ({{ $agent->department ?? 'No Dept' }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="commitments-tab-{{ $agent->id }}" data-bs-toggle="pill"
                            data-bs-target="#commitments-{{ $agent->id }}" type="button" role="tab">Commitments</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pdcs-tab-{{ $agent->id }}" data-bs-toggle="pill"
                            data-bs-target="#pdcs-{{ $agent->id }}" type="button" role="tab">PDCs</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="commitments-{{ $agent->id }}" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.N</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($agent->commitments as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-start">{{ $data->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-warning">{{ $data->status ?? 'N/A' }}</span></td>
                                        <td class="fw-bold">{{ number_format($data->total_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-muted text-center py-3">No commitments found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pdcs-{{ $agent->id }}" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.N</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($agent->pdcs as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-start">{{ $data->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-warning">{{ $data->status ?? 'N/A' }}</span></td>
                                        <td class="fw-bold">{{ number_format($data->commitment_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-muted text-center py-3">No PDCs found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="mt-4 border-top pt-3">
                    @php
                    $commitmentTotal = $agent->commitments->sum('total_amount');
                    $pdcTotal = $agent->pdcs->sum('commitment_amount');
                    @endphp
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-bold mb-1">Total Commitment: {{ number_format($commitmentTotal, 2) }}</h6>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold mb-0">Total PDC: {{ number_format($pdcTotal, 2) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Party Modals -->
@foreach ($party as $client)
<div class="modal fade" id="partyModal{{ $client->id }}" tabindex="-1" aria-labelledby="partyLabel{{ $client->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="partyLabel{{ $client->id }}">{{ $client->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="party-commitments-tab-{{ $client->id }}"
                            data-bs-toggle="pill" data-bs-target="#party-commitments-{{ $client->id }}" type="button"
                            role="tab">Commitments</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="party-pdcs-tab-{{ $client->id }}" data-bs-toggle="pill"
                            data-bs-target="#party-pdcs-{{ $client->id }}" type="button" role="tab">PDCs</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="party-commitments-{{ $client->id }}" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.N</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($client->commitments as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-start">{{ $data->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-warning">{{ $data->status ?? 'N/A' }}</span></td>
                                        <td class="fw-bold">{{ number_format($data->total_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-muted text-center py-3">No commitments found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="party-pdcs-{{ $client->id }}" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.N</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($client->pdcs as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-start">{{ $data->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-warning">{{ $data->status ?? 'N/A' }}</span></td>
                                        <td class="fw-bold">{{ number_format($data->commitment_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-muted text-center py-3">No PDCs found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="mt-4 border-top pt-3">
                    @php
                    $commitmentTotal = $client->commitments->sum('total_amount');
                    $pdcTotal = $client->pdcs->sum('commitment_amount');
                    @endphp
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-bold mb-1">Total Commitment: {{ number_format($commitmentTotal, 2) }}</h6>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold mb-0">Total PDC: {{ number_format($pdcTotal, 2) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .summary-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .icon-container {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .clickable-row {
        cursor: pointer;
        transition: background-color 0.15s;
    }

    .clickable-row:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .avatar-sm {
        width: 36px;
        height: 36px;
    }

    .avatar-xs {
        width: 24px;
        height: 24px;
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem 0.5rem;
    }

    .nav-pills .nav-link.active {
        background-color: #0d6efd;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.05);
        color: #0d6efd;
    }

    .recentUpdateModal {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .card-body {
            padding: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make table rows clickable
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function() {
                if (this.getAttribute('data-bs-toggle') === 'modal') {
                    const target = this.getAttribute('data-bs-target');
                    const modal = new bootstrap.Modal(document.querySelector(target));
                    modal.show();
                }
            });
        });

        // Handle details modal data
        const detailsModal = document.getElementById('detailsModal');
        if (detailsModal) {
            detailsModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const type = button.getAttribute('data-type');
                const clientName = button.getAttribute('data-client-name');
                const installments = JSON.parse(button.getAttribute('data-installments'));

                const modalTitle = detailsModal.querySelector('.modal-title');
                const modalSubtitle = detailsModal.querySelector('#modalSubtitle');
                const installmentsContainer = detailsModal.querySelector('#installmentsContainer');

                modalTitle.textContent = `${clientName} - ${type.toUpperCase()} Details`;
                modalSubtitle.textContent = 'Installment Schedule';

                // Clear previous content
                installmentsContainer.innerHTML = '';

                // Add installments
                installments.forEach(installment => {
                    const statusClass = installment.status === 'Complete' ? 'bg-success' :
                                      installment.status === 'Pending' ? 'bg-warning' : 'bg-secondary';

                    const card = document.createElement('div');
                    card.className = 'card';
                    card.innerHTML = `
                        <div class="card-body">
                            <h6 class="card-title">Installment ${installment.installment_number}</h6>
                            <p class="card-text mb-1"><strong>Amount:</strong> Rs ${parseFloat(installment.amount).toFixed(2)}</p>
                            <p class="card-text mb-1"><strong>Due Date:</strong> ${installment.due_date}</p>
                            <p class="card-text mb-0">
                                <span class="badge ${statusClass}">${installment.status}</span>
                            </p>
                        </div>
                    `;

                    installmentsContainer.appendChild(card);
                });
            });
        }
    });
</script>

@endsection
