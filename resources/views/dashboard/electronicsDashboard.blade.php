@extends('layouts.electronicsApp',['page' => 'Dashboard'])
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
                        @foreach(App\Models\Commitment::with('agent')->where('department','Electronics')->whereNotIn('status',['Failed'])->latest()->limit(5)->get()
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
                        @foreach($pdcs as $data)
                        <tr>
                            <td class="fw-semibold">
                                <button class="btn p-0 bg-transparent text-secondary fw-bold text-decoration-underline"
                                    data-bs-toggle="modal" data-bs-target="#detailsModal" data-type="commitment"
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
    <div class="d-flex flex-column gap-3 justify-content-between" style="max-width: max-content">

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Electronics Overview</h5>
                <div class="d-flex flex-column justify-content-between gap-3">
                    <!-- Electrical Commitments -->
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
                                    @forelse($agentsCreatedBy2 as $agent)
                                    @if($agent->commitments_count > 0)
                                    <tr>
                                        <td>{{ $agent->name ?? 'N/A' }}</td>
                                        <td class="text-success fw-semibold">COMs</td>
                                        <td>{{ $agent->commitments_count }}</td>
                                        <td>{{ $agent->postponed_count }}</td>
                                        <td>{{ $agent->success_commitments }}</td>
                                    </tr>
                                    @endif

                                    @if($agent->pdcs_count > 0)
                                    <tr>
                                        <td>{{ $agent->name ?? 'N/A' }}</td>
                                        <td class="text-info fw-semibold">PDCs</td>
                                        <td>{{ $agent->pdcs_count }}</td>
                                        <td>{{ $agent->postponed_pdcs }}</td>
                                        <td>{{ $agent->success_pdcs }}</td>
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
                <h6 class="text-center fw-bold mb-4" id="modalSubtitle"></h6>
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
                        @foreach ($agents->take(10) as $agent)
                        @if($agent->total_amount > 0)
                        <!-- Commitment Row -->
                        <tr>
                            <td>
                                <a href="#" class="link-secondary fw-bold agent-history" data-bs-toggle="modal"
                                    data-bs-target="#historyModal" data-agent-id="{{ $agent->id }}"
                                    data-agent-name="{{ $agent->name }}" data-commitments='@json($agent->commitments)'
                                    data-pdcs='@json($agent->pdcs)' data-type="salesman">
                                    {{ $agent->name }}
                                </a>
                                <small class="text-muted">({{ $agent->department ?? 'No Dept' }})</small>
                            </td>
                            <td class="text-success fw-semibold">COMs</td>
                            <td>{{ number_format($agent->total_amount, 2) }}</td>
                            <td>{{ number_format($agent->paid_amount, 2) }}</td>
                            <td>{{ number_format($agent->total_amount - $agent->paid_amount, 2)
                                }}</td>
                        </tr>
                        @endif

                        @if($agent->commitment_amount > 0)
                        <!-- PDC Row -->
                        <tr>
                            <td>
                                <a href="#" class="link-secondary fw-bold agent-history" data-bs-toggle="modal"
                                    data-bs-target="#historyModal" data-agent-id="{{ $agent->id }}"
                                    data-agent-name="{{ $agent->name }}" data-commitments='@json($agent->commitments)'
                                    data-pdcs='@json($agent->pdcs)' data-type="salesman">
                                    {{ $agent->name }}
                                </a>
                                <small class="text-muted">({{ $agent->department ?? 'No Dept' }})</small>
                            </td>
                            <td class="text-info fw-semibold">PDCs</td>
                            <td>{{ number_format($agent->commitment_amount, 2) }}</td>
                            <td>{{ number_format($agent->pdc_collected, 2) }}</td>
                            <td>{{ number_format($agent->commitment_amount -
                                $agent->pdc_collected, 2) }}</td>
                        </tr>
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
                        @foreach ($party->take(10) as $agent)
                        @if($agent->total_amount > 0)
                        <!-- Commitment Row -->
                        <tr>
                            <td>
                                <a href="#" class="link-secondary fw-bold agent-history" data-bs-toggle="modal"
                                    data-bs-target="#historyModal" data-agent-id="{{ $agent->id }}"
                                    data-agent-name="{{ $agent->name }}" data-commitments='@json($agent->commitments)'
                                    data-pdcs='@json($agent->pdcs)' data-type="party">
                                    {{ $agent->name }}
                                </a>
                            </td>
                            <td class="fw-semibold text-success">COMs</td>
                            <td>{{ number_format($agent->total_amount, 2) }}</td>
                            <td>{{ number_format($agent->paid_amount, 2) }}</td>
                            <td>{{ number_format($agent->pending_amount, 2) }}</td>
                        </tr>
                        @endif

                        @if($agent->commitment_amount > 0)
                        <!-- PDC Row -->
                        <tr>
                            <td>
                                <a href="#" class="link-secondary fw-bold agent-history" data-bs-toggle="modal"
                                    data-bs-target="#historyModal" data-agent-id="{{ $agent->id }}"
                                    data-agent-name="{{ $agent->name }}" data-commitments='@json($agent->commitments)'
                                    data-pdcs='@json($agent->pdcs)' data-type="party">
                                    {{ $agent->name }}
                                </a>
                            </td>
                            <td class="text-info fw-semibold">PDCs</td>
                            <td>{{ number_format($agent->commitment_amount, 2) }}</td>
                            <td>{{ number_format($agent->pdc_collected, 2) }}</td>
                            <td>{{ number_format($agent->pdc_pending, 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Single Modal for Both Salesman and Party -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="text-center" id="agentName"></h5>

                <h6 class="text-muted">Commitments</h6>
                <table class="table  text-nowrap mb-0 table-striped">
                    <thead>
                        <tr>
                            <th scope="col">S.N</th>
                            <th scope="col">Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="commitmentData"></tbody>
                </table>

                <h6 class="text-muted mt-3">PDCs</h6>
                <table class="table  text-nowrap mb-0 table-striped">
                    <thead>
                        <tr>
                            <th scope="col">S.N</th>
                            <th scope="col">Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="pdcData"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-nowrap text-center">
                    <thead>
                        <tr>
                            <th>Entry Date</th>
                            <th>Type</th>
                            <th>Party</th>
                            <th>Address</th>
                            <th>TotalAmount</th>
                            <th>Received / Collected</th>
                            <th>Pending</th>
                            <th>Salesman</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Commitments --}}
                        @foreach(App\Models\Commitment::where('department','Electrical')
                        ->whereNotIn('status',['Complete','Failed'])
                        ->latest()->take(5)->get() as $item)
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
                            <td>
                                <span class="badge badge-sm badge-status
                                    @if($item->status == 'Complete') bg-success
                                    @elseif($item->status == 'Overdue') bg-danger
                                    @else bg-warning
                                    @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach

                        {{-- PDCs --}}
                        @foreach(App\Models\Pdc::where('department','Electrical')
                        ->whereNotIn('status',['Complete','Failed'])
                        ->latest()->take(5)->get() as $main)
                        @php
                        $oldestIncompleteInstallment =
                        $main->installments->where('status','!=','Complete')->sortBy('due_date')->first();
                        $receivedAmount = $main->installments->where('status','Complete')->sum('amount');
                        $pendingAmount = $main->commitment_amount - $receivedAmount;
                        @endphp
                        <tr>
                            <td>{{ $main->created_at->format('Y-m-d') }}</td>
                            <td class="text-info fw-semibold">PDC</td>
                            <td>{{ $main->client->name }}</td>
                            <td>{{ $main->client->address ?? 'N/A' }}</td>
                            <td>Rs {{ number_format($main->commitment_amount, 2) }}</td>
                            <td>Rs {{ number_format($receivedAmount, 2) }}</td>
                            <td>Rs {{ number_format($pendingAmount, 2) }}</td>
                            <td>{{ $main->agent->name ?? 'No Salesman Assigned' }}</td>
                            <td>
                                <span class="badge badge-sm badge-status
                                    @if($main->status == 'Complete') bg-success
                                    @elseif($main->status == 'Overdue') bg-danger
                                    @else bg-warning
                                    @endif">
                                    {{ ucfirst($main->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection