@extends('layouts.superApp',['page' => 'Dashboard'])
@section('content')

<div class="row g-4">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body position-relative p-4">
                <div class="position-absolute p-3 top-0 end-0">
                    <i class="fas fa-file-invoice-dollar fs-1 text-success fa-lg"></i>
                </div>
                <h5 class="card-title text-muted">Active COMs</h5>
                <p class="card-text fs-3 fw-bold">Rs {{ number_format($activeCommitmentAmount) }}</p>

            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body position-relative p-4">
                <div class="position-absolute p-3 top-0 end-0">
                    <i class="fas fs-1 fa-clock text-danger fa-lg"></i>
                </div>
                <h5 class="card-title text-muted">Overdue COMs</h5>
                <p class="card-text fs-3 fw-bold">Rs {{ number_format($overdueCommitmentTotal) }}</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body position-relative p-4">
                <div class="position-absolute p-3 top-0 end-0">
                    <i class="fas fs-1 fa-money-check-alt text-warning fa-lg"></i>
                </div>
                <h5 class="card-title text-muted">Active PDCs</h5>
                <p class="card-text fs-3 fw-bold">Rs {{ number_format($activePdcAmount) }}</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body position-relative p-4">
                <div class="position-absolute p-3 top-0 end-0">
                    <i class="fas fs-1 fa-calendar-times text-danger fa-lg"></i>
                </div>
                <h5 class="card-title text-muted">Overdue PDCs</h5>
                <p class="card-text fs-3 fw-bold">Rs {{ number_format($overduePdcTotal) }}</p>
            </div>
        </div>
    </div>
</div>
<style>
    .rr-cards>* {
        flex: 1 1 20rem;
    }
</style>
<div class="rr-cards d-flex gap-3 mt-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Recent Updates</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-nowrap text-center">
                    <thead>
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
                        <tr>
                            <td class="fw-semibold">
                                <button class="btn p-0 bg-transparent text-secondary fw-bold text-decoration-underline"
                                    data-bs-toggle="modal" data-bs-target="#detailsModal" data-type="commitment"
                                    data-id="{{ $data->id }}" data-client-name="{{ $data->client->name }}"
                                    data-installments="{{ json_encode($data->installments) }}">
                                    {{ $data->client->name }}
                                </button>
                            </td>
                            <td class="fw-semibold text-success">COMs</td>
                            <td>{{ $data->client->address }}</td>
                            <td>{{ number_format($data->total_amount, 2) }}</td>
                            <td>{{ $data->agent->name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach

                        <!-- PDC Data -->
                        @foreach($pdcsData as $data)
                        <tr>
                            <td class="fw-semibold">
                                <button class="btn p-0 bg-transparent text-secondary fw-bold text-decoration-underline"
                                    data-bs-toggle="modal" data-bs-target="#detailsModal" data-type="pdc"
                                    data-id="{{ $data->id }}" data-client-name="{{ $data->client->name }}"
                                    data-installments="{{ json_encode($data->installments) }}">
                                    {{ $data->client->name }}
                                </button>
                            </td>
                            <td class="fw-semibold text-info">PDCs</td>
                            <td>{{ $data->client->address }}</td>
                            <td>{{ number_format($data->commitment_amount, 2) }}</td>
                            <td>{{ $data->agent->name ?? 'N/A' }}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- <div class="d-flex flex-column gap-3 justify-content-between" style="max-width: max-content">
        <!-- Electronics -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Electronics Overview</h5>
                <div class="d-flex flex-column justify-content-between gap-3">
                    <div class="d-flex flex-column">
                        <div class="table-responsive flex-grow-1">
                            <table class="text-nowrap mb-0 table table-bordered table-striped table-hover mb-0 w-100">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 25%;">Salesman</th>
                                        <th style="width: 15%;">Type</th>
                                        <th style="width: 20%;">Total</th>
                                        <th style="width: 20%;">Postponed</th>
                                        <th style="width: 20%;">Success</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $electronicsAgentsFiltered = $electronicsAgents
                                    ->filter(fn($stats) => $stats['commitments_total'] > 0 || $stats['pdcs_total'] > 0)
                                    ->take(10);
                                    @endphp

                                    @forelse($electronicsAgentsFiltered as $stats)
                                    @if($stats['commitments_total'] > 0)
                                    <tr class="text-center">
                                        <td>{{ $stats['agent']->name ?? 'N/A' }}</td>
                                        <td class="text-success fw-semibold">COMs</td>
                                        <td>{{ $stats['commitments_total'] }}</td>
                                        <td>{{ $stats['commitments_postponed'] }}</td>
                                        <td>{{ $stats['commitments_success'] }}</td>
                                    </tr>
                                    @endif

                                    @if($stats['pdcs_total'] > 0)
                                    <tr class="text-center">
                                        <td>{{ $stats['agent']->name ?? 'N/A' }}</td>
                                        <td class="text-info fw-semibold">PDCs</td>
                                        <td>{{ $stats['pdcs_total'] }}</td>
                                        <td>{{ $stats['pdcs_postponed'] }}</td>
                                        <td>{{ $stats['pdcs_success'] }}</td>
                                    </tr>
                                    @endif
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No agents found for Electronics department.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Electrical -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Electrical Overview</h5>
                <div class="d-flex flex-column justify-content-between gap-3">
                    <div class="d-flex flex-column">
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-bordered table-striped text-nowrap mb-0 table-hover mb-0 w-100">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 25%;">Salesman</th>
                                        <th style="width: 15%;">Type</th>
                                        <th style="width: 20%;">Total</th>
                                        <th style="width: 20%;">Postponed</th>
                                        <th style="width: 20%;">Success</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $electricalAgentsFiltered = $electricalAgents
                                    ->filter(fn($stats) => $stats['commitments_total'] > 0 || $stats['pdcs_total'] > 0)
                                    ->take(10);
                                    @endphp

                                    @forelse($electricalAgentsFiltered as $stats)
                                    @if($stats['commitments_total'] > 0)
                                    <tr class="text-center">
                                        <td>{{ $stats['agent']->name ?? 'N/A' }}</td>
                                        <td class="text-success fw-semibold">COMs</td>
                                        <td>{{ $stats['commitments_total'] }}</td>
                                        <td>{{ $stats['commitments_postponed'] }}</td>
                                        <td>{{ $stats['commitments_success'] }}</td>
                                    </tr>
                                    @endif

                                    @if($stats['pdcs_total'] > 0)
                                    <tr class="text-center">
                                        <td>{{ $stats['agent']->name ?? 'N/A' }}</td>
                                        <td class="text-info fw-semibold">PDCs</td>
                                        <td>{{ $stats['pdcs_total'] }}</td>
                                        <td>{{ $stats['pdcs_postponed'] }}</td>
                                        <td>{{ $stats['pdcs_success'] }}</td>
                                    </tr>
                                    @endif
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No agents found for Electrical department.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="d-flex flex-column gap-3 justify-content-between" style="max-width: max-content">

        @foreach($departmentAgents as $deptStats)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $deptStats['department']->name }} Overview</h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-nowrap mb-0 table-hover w-100">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th style="width: 25%;">Salesman</th>
                                <th style="width: 15%;">Type</th>
                                <th style="width: 20%;">Total</th>
                                <th style="width: 20%;">Postponed</th>
                                <th style="width: 20%;">Success</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($deptStats['agents']->take(10) as $stats)

                            @if($stats['commitments_total'] > 0)
                            <tr class="text-center">
                                <td>{{ $stats['agent']->name ?? 'N/A' }}</td>
                                <td class="text-success fw-semibold">COMs</td>
                                <td>{{ $stats['commitments_total'] }}</td>
                                <td>{{ $stats['commitments_postponed'] }}</td>
                                <td>{{ $stats['commitments_success'] }}</td>
                            </tr>
                            @endif

                            @if($stats['pdcs_total'] > 0)
                            <tr class="text-center">
                                <td>{{ $stats['agent']->name ?? 'N/A' }}</td>
                                <td class="text-info fw-semibold">PDCs</td>
                                <td>{{ $stats['pdcs_total'] }}</td>
                                <td>{{ $stats['pdcs_postponed'] }}</td>
                                <td>{{ $stats['pdcs_success'] }}</td>
                            </tr>
                            @endif

                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No agents found for this department.</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
<style>
    .recentUpdateModal {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
</style>

<!-- Single Modal for Installment Details -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title fw-bold" id="detailsModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-center fw-bold mb-2" id="modalSubtitle"></h6>
                <div class="d-grid recentUpdateModal" id="installmentsContainer"></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-4 mt-3">
    <div class="card w-100">
        <div class="card-body">
            <h5 class="card-title mb-4">Salesman Commitment & PDC Summary</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-nowrap text-center">
                    <thead>
                        <tr>
                            <th>Salesman</th>
                            <th>Type</th>
                            <th>Total Amount</th>
                            <th>Received</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Salesman Table -->
                        @foreach ($agents->take(10) as $agent)
                        @if($agent->total_amount > 0 || $agent->commitment_amount > 0)
                        {{-- Commitment Row --}}
                        @if($agent->total_amount > 0)
                        <tr>
                            <td>
                                <a href="#" class="link-secondary fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#salesmanModal{{ $agent->id }}">
                                    {{ $agent->name }}
                                    <small>({{ $agent->department ?? '--' }})</small>
                                </a>
                            </td>
                            <td class="text-success fw-semibold">COMs</td>
                            <td>{{ number_format($agent->total_amount, 2) }}</td>
                            <td>{{ number_format($agent->paid_amount, 2) }}</td>
                            <td>{{ number_format($agent->pending_amount, 2) }}</td>
                        </tr>
                        @endif

                        @if($agent->commitment_amount > 0)
                        <tr>
                            <td>
                                <a href="#" class="link-secondary fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#salesmanModal{{ $agent->id }}">
                                    {{ $agent->name }}
                                    <small>({{ $agent->department ?? '--' }})</small>
                                </a>
                            </td>
                            <td class="fw-semibold text-primary">PDCs</td>
                            <td>{{ number_format($agent->commitment_amount, 2) }}</td>
                            <td>{{ number_format($agent->pdc_collected, 2) }}</td>
                            <td>{{ number_format($agent->pdc_pending, 2) }}</td>
                        </tr>
                        @endif
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card w-100">
        <div class="card-body">
            <h5 class="card-title mb-4">Party Commitment & PDC Summary</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Party</th>
                            <th>Type</th>
                            <th>Total Amount</th>
                            <th>Received</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($party->take(10) as $client)
                        @if ($client->total_amount > 0 || $client->commitment_amount > 0)
                        {{-- Commitment Row --}}
                        @if ($client->total_amount > 0)
                        <tr>
                            <td class="fw-semibold">
                                <a href="#" class="link-secondary fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#partyModal{{ $client->id }}">
                                    {{ $client->name }}
                                </a>
                            </td>
                            <td class="fw-semibold text-success">COMs</td>
                            <td>{{ number_format($client->total_amount, 2) }}</td>
                            <td>{{ number_format($client->paid_amount, 2) }}</td>
                            <td>{{ number_format($client->pending_amount, 2) }}</td>
                        </tr>
                        @endif

                        {{-- PDC Row --}}
                        @if ($client->commitment_amount > 0)
                        <tr>
                            <td class="fw-semibold">
                                <a href="#" class="link-secondary fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#partyModal{{ $client->id }}">
                                    {{ $client->name }}
                                </a>
                            </td>
                            <td class="fw-semibold text-primary">PDCs</td>
                            <td>{{ number_format($client->commitment_amount, 2) }}</td>
                            <td>{{ number_format($client->pdc_collected, 2) }}</td>
                            <td>{{ number_format($client->pdc_pending, 2) }}</td>
                        </tr>
                        @endif
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Salesman Modals -->
    @foreach ($agents as $agent)
    <div class="modal fade" id="salesmanModal{{ $agent->id }}" tabindex="-1"
        aria-labelledby="salesmanLabel{{ $agent->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="salesmanLabel{{ $agent->id }}">
                        {{ $agent->name }} ({{ $agent->department ?? 'No Dept' }})
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Commitments Section --}}
                    <h6 class="fw-semibold mb-2">Commitments</h6>
                    <table class="table table-bordered align-middle text-center mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agent->commitments as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->client->address ?? $agent->address ?? '--' }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->status ?? 'N/A' }}</td>
                                <td>{{ number_format($data->total_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted">No commitments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- PDC Section --}}
                    <h6 class="fw-semibold mt-4 mb-2">PDCs</h6>
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agent->pdcs as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->client->address ?? $agent->address ?? '--' }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->status ?? 'N/A' }}</td>
                                <td>{{ number_format($data->commitment_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted">No PDCs found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Total Section -->
                    <div class="mt-2 border-top pt-2 text-end">
                        @php
                        $commitmentTotal = $agent->commitments->sum('total_amount');
                        $pdcTotal = $agent->pdcs->sum('commitment_amount');
                        @endphp
                        <h6 class="fw-bold mb-1">Total Commitment: {{ number_format($commitmentTotal, 2) }}</h6>
                        <h6 class="fw-bold mb-0">Total PDC: {{ number_format($pdcTotal, 2) }}</h6>

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
                    {{-- Commitments Section --}}
                    <h6 class="fw-semibold mb-2">Commitments</h6>
                    <table class="table table-bordered align-middle text-center mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($client->commitments as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $client->address ?? '--' }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->status ?? 'N/A' }}</td>
                                <td>{{ number_format($data->total_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted">No commitments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- PDC Section --}}
                    <h6 class="fw-semibold mt-4 mb-2">PDCs</h6>
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($client->pdcs as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $client->address ?? '--' }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->status ?? 'N/A' }}</td>
                                <td>{{ number_format($data->commitment_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted">No PDCs found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Total Section -->
                    <div class="mt-2 border-top pt-2 text-end">
                        @php
                        $commitmentTotal = $client->commitments->sum('total_amount');
                        $pdcTotal = $client->pdcs->sum('commitment_amount');
                        @endphp
                        <h6 class="fw-bold mb-2">Total Commitment: {{ number_format($commitmentTotal, 2) }}</h6>
                        <h6 class="fw-bold mb-0">Total PDC: {{ number_format($pdcTotal, 2) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>


<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills nav-sub-tabs mb-3" id="departmentTabs" role="tablist">
                @foreach($departments as $index => $dept)
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 {{ $index == 0 ? 'active' : '' }}"
                        id="dept-tab-{{ $dept->id }}" data-bs-toggle="pill"
                        data-bs-target="#deptSection-{{ $dept->id }}" type="button" role="tab">
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
                        <table class="table table-striped table-bordered text-nowrap text-center">
                            <thead>
                                <tr>
                                    <th>Entry Date</th>
                                    <th>Type</th>
                                    <th>Party</th>
                                    <th>Address</th>
                                    <th>Amount</th>
                                    <th>Received / Collected</th>
                                    <th>Pending</th>
                                    <th>Salesman</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($commitments->isEmpty() && $pdcs->isEmpty())
                                <tr>
                                    <td colspan="9" class="text-muted">No data found</td>
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
                                    <td class="text-success fw-semibold">COMs</td>
                                    <td>{{ $item->client->name }}</td>
                                    <td>{{ $item->client->address ?? 'N/A' }}</td>
                                    <td>Rs {{ number_format($item->total_amount, 2) }}</td>
                                    <td>Rs {{ number_format($totalPaidAmount, 2) }}</td>
                                    <td>Rs {{ number_format($totalPendingAmount, 2) }}</td>
                                    <td>{{ $item->agent->name ?? 'No Agent Assigned' }}</td>
                                    <td><span class="badge bg-warning">{{ ucfirst($item->status) }}</span></td>
                                </tr>
                                @endforeach

                                {{-- PDCs --}}
                                @foreach($pdcs as $main)
                                <tr>
                                    <td>{{ $main->created_at->format('Y-m-d') }}</td>
                                    <td class="text-info fw-semibold">PDCs</td>
                                    <td>{{ $main->client->name }}</td>
                                    <td>{{ $main->client->address ?? 'N/A' }}</td>
                                    <td>Rs {{ number_format($main->commitment_amount, 2) }}</td>
                                    <td>Rs {{
                                        number_format($main->installments->where('status','Complete')->sum('amount'), 2)
                                        }}
                                    </td>
                                    <td>Rs {{ number_format($main->commitment_amount -
                                        $main->installments->where('status','Complete')->sum('amount'), 2) }}</td>
                                    <td>{{ $main->agent->name ?? 'No Agent Assigned' }}</td>
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
@endsection