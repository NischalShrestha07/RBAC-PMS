@extends('layouts.electronicsApp', ['page' => 'Salesman Details'])

@section('content')
<section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-start">
            <h5>View details for <b class="text-primary">{{ $agent->name }}</b></h5>
        </div>
        <div class="d-flex align-items-center breadcrumb mb-0">
            <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                    href="{{ route('electronics.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                    href="{{ route('electronics.agents.create') }}">Salesmans</a></li>
            <li class="breadcrumb-item active">Details</li>
        </div>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="navbar p-3 bg-light shadow-sm">
                    <h2 class="text-start fw-bold">Salesman Details</h2>
                    <div>
                        <a href="{{ route('electronics.agents.create') }}" class="btn btn-secondary" title="Back">
                            Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Salesman Information Card -->
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3 p-md-3">
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Name</label>
                                        <p class="mb-0 fw-semibold">{{ $agent->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Phone</label>
                                        <p class="mb-0 fw-semibold">{{ $agent->contact ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Department</label>
                                        <p class="mb-0 fw-semibold">{{ $agent->department ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Area</label>
                                        <p class="mb-0 fw-semibold">{{ $agent->area ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted">Address</label>
                                    <p class="mb-0 fw-semibold">{{ $agent->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Commitments Section -->
                    <h3 class="fw-bold mt-4 mb-3">Active Commitments</h3>
                    @if ($activeCommitments->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No Active Commitments Found.</h5>
                            <p class="text-muted mb-0">This salesman has no pending financial commitments at the moment.
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-white mb-4" id="activeCommitmentsAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th scope="col">Client</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Paid Amount</th>
                                        <th scope="col">Pending Amount</th>
                                        <th scope="col">Salesman</th>
                                        <th scope="col">Commitment Age</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Settled Date</th>
                                        <th scope="col">Follow-up</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activeCommitments as $commitment)
                                    <tr class="align-middle accordion-item border-0"
                                        id="commitmentHeading{{ $commitment->id }}">
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                {{ $commitment->client->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                Rs {{ number_format($commitment->total_amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                @php
                                                $totalPaidAmount = $commitment->transactions->where('pc_id',
                                                $commitment->id)->where('type','commitment')->sum('pay_amount');
                                                @endphp
                                                Rs {{ number_format($totalPaidAmount, 2) }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                Rs {{ number_format($commitment->pending_amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold ">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                {{ ($commitment->agent->name) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                @php
                                                $installment = $commitment->installments()->where('status', '!=',
                                                'Complete')->first();
                                                $isCompleted = !$installment;
                                                $dueDate = $installment?->due_date ?
                                                \Carbon\Carbon::parse($installment->due_date) : null;
                                                $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
                                                false) : null;
                                                @endphp
                                                @if ($commitment->status === 'Failed')
                                                <span class="text-danger">Failed</span>
                                                @elseif ($isCompleted)
                                                <span class="text-success">Completed</span>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) <span class="text-danger">Overdue by {{
                                                    abs($remainingDays) }} days</span>
                                            @elseif($remainingDays == 0)
                                            <span class="text-warning">Due Today</span>
                                            @else
                                            <span class="text-primary">{{ $remainingDays }} days remaining</span>
                                            @endif
                                            @else
                                            <span class="text-warning">No Due Date</span>
                                            @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                <span
                                                    class="badge {{ $commitment->status === 'Complete' ? 'bg-success' : ($commitment->status === 'Overdue' ? 'bg-danger' : ($commitment->status === 'Failed' ? 'bg-secondary' : 'bg-warning')) }}">
                                                    {{ ucfirst($commitment->status) }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                <span class="badge bg-light text-dark">{{
                                                    $commitment->created_at->format('Y-m-d H:i') }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                @php
                                                $lastTransaction =
                                                $commitment->transactions->sortByDesc('created_at')->first();
                                                $settledDate = $lastTransaction ?
                                                $lastTransaction->created_at->format('Y-m-d H:i') : 'N/A';
                                                @endphp
                                                <span class="badge bg-light text-dark">{{ $settledDate }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                <span class="badge text-black">{{ $commitment->followups->count()
                                                    }}</span>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('electronics.client.commitment.show', $commitment->id) }}"
                                                    class="btn" title="View" aria-label="View Commitment">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="11" class="p-0 bg-light">
                                            <div id="commitmentCollapse{{ $commitment->id }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="commitmentHeading{{ $commitment->id }}"
                                                data-bs-parent="#activeCommitmentsAccordion">
                                                <div class="accordion-body p-3">
                                                    @if ($commitment->installments->isNotEmpty())
                                                    <h5 class="text-muted mb-2 text-center mt-2">Installments</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 text-nowrap">
                                                            <thead class="table-light text-center">
                                                                <tr>
                                                                    <th>Amount</th>
                                                                    <th>Due Date</th>
                                                                    <th>Commitment Age(Days)</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($commitment->installments as $installment)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        @if($installment->paid_amount > 0)
                                                                        <del>Rs {{ number_format($installment->amount,
                                                                            2) }}</del><br>
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount -
                                                                            $installment->paid_amount, 2) }}</strong>
                                                                        @else
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount, 2)
                                                                            }}</strong>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">{{ $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td class="text-center">
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date) :
                                                                        null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status === 'Complete')
                                                                        <span class="text-success">Completed</span>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) <span
                                                                            class="text-danger">Overdue by {{
                                                                            abs($remainingDays) }} days</span>
                                                                            @elseif($remainingDays == 0)
                                                                            <span class="text-warning">Due Today</span>
                                                                            @else
                                                                            <span class="text-primary">{{ $remainingDays
                                                                                }} days remaining</span>
                                                                            @endif
                                                                            @else
                                                                            <span class="text-warning">No Due
                                                                                Date</span>
                                                                            @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span style="color:white; padding:7px;"
                                                                            class="badge {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : 'bg-warning') }}">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Active PDCs Section -->
                    <h3 class="fw-bold mt-4 mb-3">Active PDCs</h3>
                    @if ($activePdcs->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No Active PDCs Found.</h5>
                            <p class="text-muted mb-0">This salesman has no pending post-dated cheques at the moment.
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-white mb-4" id="activePdcAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-primary text-white">
                                    <tr class="text-center">
                                        <th scope="col">Client</th>
                                        <th scope="col">Pdc Amount</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Salesman</th>
                                        <th scope="col">Pdc Age</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Settled Date</th>
                                        <th scope="col">Follow-up</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activePdcs as $pdc)
                                    <tr class="align-middle accordion-item border-0 text-center"
                                        id="pdcHeading{{ $pdc->id }}">
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                {{ $pdc->client->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                Rs {{ number_format($pdc->commitment_amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                {{ $pdc->client->address ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                {{ $pdc->agent->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                @php
                                                $installment = $pdc->installments()->where('status', '!=',
                                                'Complete')->first();
                                                $isCompleted = !$installment;
                                                $dueDate = $installment?->due_date ?
                                                \Carbon\Carbon::parse($installment->due_date) : null;
                                                $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
                                                false) : null;
                                                @endphp
                                                @if ($pdc->status === 'Failed')
                                                <span class="text-danger">Failed</span>
                                                @elseif ($isCompleted)
                                                <span class="text-success">Completed</span>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) <span class="text-danger">Overdue by {{
                                                    abs($remainingDays) }} days</span>
                                            @elseif($remainingDays == 0)
                                            <span class="text-warning">Due Today</span>
                                            @else
                                            <span class="text-primary">{{ $remainingDays }} days remaining</span>
                                            @endif
                                            @else
                                            <span class="text-warning">No Due Date</span>
                                            @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                <span
                                                    class="badge {{ $pdc->status === 'Complete' ? 'bg-success' : ($pdc->status === 'Overdue' ? 'bg-danger' : ($pdc->status === 'Failed' ? 'bg-secondary' : 'bg-warning')) }}">
                                                    {{ ucfirst($pdc->status) }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                <span class="badge bg-light text-dark">{{
                                                    \Carbon\Carbon::parse($pdc->created_at)->format('Y-m-d H:i')
                                                    }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                @php
                                                $lastTransaction =
                                                $pdc->transactions->sortByDesc('created_at')->first();
                                                $settledDate = $lastTransaction ?
                                                $lastTransaction->created_at->format('Y-m-d H:i') : 'N/A';
                                                @endphp
                                                <span class="badge bg-light text-dark">{{ $settledDate }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                <span class="badge text-black">{{ $pdc->followups->count() }}</span>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('electronics.client.pdc.show', $pdc->id) }}"
                                                    class="btn" title="View" aria-label="View PDC">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" class="p-0 bg-light">
                                            <div id="pdcCollapse{{ $pdc->id }}" class="accordion-collapse collapse"
                                                aria-labelledby="pdcHeading{{ $pdc->id }}"
                                                data-bs-parent="#activePdcAccordion">
                                                <div class="accordion-body p-3">
                                                    @if ($pdc->installments->isNotEmpty())
                                                    <h5 class="text-muted mb-2 mt-2 text-center">Installments</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm text-nowrap table-bordered mb-0">
                                                            <thead class="table-light text-center">
                                                                <tr class="text-center">
                                                                    <th>Amount</th>
                                                                    <th>Cheque Number</th>
                                                                    <th>Bank Name</th>
                                                                    <th>Due Date</th>
                                                                    <th>Pdc Age</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($pdc->installments as $installment)
                                                                <tr class="text-center">
                                                                    <td class="text-center">
                                                                        @if ($installment->data1 > 0)
                                                                        <del>{{ number_format($installment->amount, 2)
                                                                            }}</del><br>
                                                                        <strong class="text-success">{{
                                                                            number_format($installment->amount -
                                                                            $installment->data1, 2) }}</strong>
                                                                        @else
                                                                        <strong class="text-success"> {{
                                                                            number_format($installment->amount, 2) }}
                                                                        </strong>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $installment->chequeno ?? 'N/A' }}</td>
                                                                    <td>{{ $installment->bankName ?? 'N/A' }}</td>
                                                                    <td>{{ $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td>
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date) :
                                                                        null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status === 'Failed')
                                                                        <span class="text-danger">Failed</span>
                                                                        @elseif ($installment->status === 'Complete')
                                                                        <span class="text-success">Complete</span>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) <span
                                                                            class="text-danger">Overdue by {{
                                                                            abs($remainingDays) }} days</span>
                                                                            @elseif($remainingDays == 0)
                                                                            <span class="text-warning">Due Today</span>
                                                                            @else
                                                                            <span class="text-primary">{{ $remainingDays
                                                                                }} days left</span>
                                                                            @endif
                                                                            @else
                                                                            <span class="text-warning">No Due
                                                                                Date</span>
                                                                            @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span style="color: white; padding: 7px;"
                                                                            class="badge {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : ($installment->status === 'Failed' ? 'bg-secondary' : 'bg-warning')) }}">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <style>
                        table td {
                            vertical-align: middle;
                        }
                    </style>

                    <!-- Commitment History Section -->
                    <h3 class="fw-bold mt-4 mb-3">Commitment Histories</h3>
                    @if ($historyCommitments->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No Commitment History Found.</h5>
                            <p class="text-muted mb-0">This salesman has no record of past or completed commitments.</p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-light mb-4" id="commitmentHistoryAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th scope="col">Client</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Paid Amount</th>
                                        <th scope="col">Pending Amount</th>
                                        <th scope="col">Commitment Age</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historyCommitments as $commitment)
                                    <tr class="align-middle accordion-item border-0 text-center"
                                        id="commitmentHistoryHeading{{ $commitment->id }}">
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                {{ $commitment->client->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                @php
                                                $totalCommitmentAmount = $commitment->installments->sum('amount') +
                                                $commitment->failedInstallments->sum('amount');
                                                @endphp
                                                Rs {{ number_format($totalCommitmentAmount, 2) }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                @php
                                                $totalPaidAmount = $commitment->transactions->where('pc_id',
                                                $commitment->id)->where('type','commitment')->sum('pay_amount');
                                                @endphp
                                                Rs {{ number_format($totalPaidAmount, 2) }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                Rs {{ number_format($commitment->pending_amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                @php
                                                $installment = $commitment->installments()->where('status', '!=',
                                                'Complete')->first();
                                                $isCompleted = !$installment;
                                                $dueDate = $installment?->due_date ?
                                                \Carbon\Carbon::parse($installment->due_date) : null;
                                                $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
                                                false) : null;
                                                @endphp
                                                @if ($commitment->status === 'Failed')
                                                <span class="text-danger">Failed</span>
                                                @elseif ($isCompleted)
                                                <span class="text-success">Completed</span>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) <span class="text-danger">Overdue by {{
                                                    abs($remainingDays) }} days</span>
                                            @elseif($remainingDays == 0)
                                            <span class="text-warning">Due Today</span>
                                            @else
                                            <span class="text-primary">{{ $remainingDays }} days remaining</span>
                                            @endif
                                            @else
                                            <span class="text-warning">No Due Date</span>
                                            @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                <span
                                                    class="badge {{ $commitment->status === 'Complete' ? 'bg-success' : ($commitment->status === 'Failed' ? 'bg-secondary' : 'bg-warning') }}">
                                                    {{ ucfirst($commitment->status) }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                <span class="badge bg-light text-dark">{{
                                                    $commitment->created_at->format('Y-m-d H:i') }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="p-0 bg-light">
                                            <div id="commitmentHistoryCollapse{{ $commitment->id }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="commitmentHistoryHeading{{ $commitment->id }}"
                                                data-bs-parent="#commitmentHistoryAccordion">
                                                <div class="accordion-body p-3">
                                                    @if ($commitment->installments->isNotEmpty())
                                                    <h5 class="text-muted mb-2 mt-2 text-center">Installments</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 text-nowrap">
                                                            <thead class="table-light text-center">
                                                                <tr class="text-center">
                                                                    <th>Amount</th>
                                                                    <th>Due Date</th>
                                                                    <th>Commitment Age(Days)</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($commitment->installments as $installment)
                                                                <tr class="text-center">
                                                                    <td>
                                                                        @if($installment->paid_amount > 0)
                                                                        <del>Rs {{ number_format($installment->amount,
                                                                            2) }}</del><br>
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount -
                                                                            $installment->paid_amount, 2) }}</strong>
                                                                        @else
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount, 2)
                                                                            }}</strong>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td>
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date) :
                                                                        null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status === 'Failed')
                                                                        <span class="text-danger">Failed</span>
                                                                        @elseif ($installment->status === 'Complete')
                                                                        <span class="text-success">Completed</span>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) <span
                                                                            class="text-danger">Overdue by {{
                                                                            abs($remainingDays) }} days</span>
                                                                            @elseif($remainingDays == 0)
                                                                            <span class="text-warning">Due Today</span>
                                                                            @else
                                                                            <span class="text-primary">{{ $remainingDays
                                                                                }} days remaining</span>
                                                                            @endif
                                                                            @else
                                                                            <span class="text-warning">No Due
                                                                                Date</span>
                                                                            @endif
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge text-white {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : ($installment->status === 'Failed' ? 'bg-secondary' : 'bg-warning')) }}">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- PDC History Section -->
                    <h3 class="fw-bold mt-4 mb-3">PDC Histories</h3>
                    @if ($historyPdcs->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No PDC History Found.</h5>
                            <p class="text-muted mb-0">This salesman has no record of past or completed PDCs.</p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-light mb-4" id="pdcHistoryAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-secondary text-white text-center">
                                    <tr>
                                        <th scope="col">Client</th>
                                        <th scope="col">Pdc Amount</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Pdc Age</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historyPdcs as $pdc)
                                    <tr class="align-middle accordion-item border-0 text-center"
                                        id="pdcHistoryHeading{{ $pdc->id }}">
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                {{ $pdc->client->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                @php
                                                $totalPdcAmount = $pdc->installments->sum('amount') +
                                                $pdc->failedInstallments->sum('amount');
                                                @endphp
                                                Rs {{ number_format($totalPdcAmount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                {{ $pdc->client->address ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                @php
                                                $installment = $pdc->installments()->where('status', '!=',
                                                'Complete')->first();
                                                $isCompleted = !$installment;
                                                $dueDate = $installment?->due_date ?
                                                \Carbon\Carbon::parse($installment->due_date) : null;
                                                $remainingDays = $dueDate ? today()->startOfDay()->diffInDays($dueDate,
                                                false) : null;
                                                @endphp
                                                @if ($pdc->status === 'Failed')
                                                <span class="text-danger">Failed</span>
                                                @elseif ($isCompleted)
                                                <span class="text-success">Completed</span>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) <span class="text-danger">Overdue by {{
                                                    abs($remainingDays) }} days</span>
                                            @elseif($remainingDays == 0)
                                            <span class="text-warning">Due Today</span>
                                            @else
                                            <span class="text-primary">{{ $remainingDays }} days remaining</span>
                                            @endif
                                            @else
                                            <span class="text-warning">No Due Date</span>
                                            @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                <span
                                                    class="badge {{ $pdc->status === 'Complete' ? 'bg-success' : ($pdc->status === 'Failed' ? 'bg-secondary' : 'bg-warning') }}">
                                                    {{ ucfirst($pdc->status) }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                <span class="badge bg-light text-dark">{{
                                                    \Carbon\Carbon::parse($pdc->created_at)->format('Y-m-d H:i')
                                                    }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="p-0 bg-light">
                                            <div id="pdcHistoryCollapse{{ $pdc->id }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="pdcHistoryHeading{{ $pdc->id }}"
                                                data-bs-parent="#pdcHistoryAccordion">
                                                <div class="accordion-body p-3">
                                                    @if ($pdc->installments->isNotEmpty())
                                                    <h5 class="text-muted mb-2 mt-2 text-center">Installments</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 text-nowrap">
                                                            <thead class="table-light text-center">
                                                                <tr>
                                                                    <th>Amount</th>
                                                                    <th>Cheque Number</th>
                                                                    <th>Bank Name</th>
                                                                    <th>Due Date</th>
                                                                    <th>Pdc Age</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($pdc->installments as $installment)
                                                                <tr class="text-center">
                                                                    <td>
                                                                        @if ($installment->data1 > 0)
                                                                        <del>{{ number_format($installment->amount, 2)
                                                                            }}</del><br>
                                                                        <strong class="text-success">{{
                                                                            number_format($installment->amount -
                                                                            $installment->data1, 2) }}</strong>
                                                                        @else
                                                                        <strong class="text-success"> {{
                                                                            number_format($installment->amount, 2) }}
                                                                        </strong>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $installment->chequeno ?? 'N/A' }}</td>
                                                                    <td>{{ $installment->bankName ?? 'N/A' }}</td>
                                                                    <td>{{ $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td>
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date) :
                                                                        null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status === 'Failed')
                                                                        <span class="text-danger">Failed</span>
                                                                        @elseif ($installment->status === 'Complete')
                                                                        <span class="text-success">Complete</span>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) <span
                                                                            class="text-danger">Overdue by {{
                                                                            abs($remainingDays) }} days</span>
                                                                            @elseif($remainingDays == 0)
                                                                            <span class="text-warning">Due Today</span>
                                                                            @else
                                                                            <span class="text-primary">{{ $remainingDays
                                                                                }} days left</span>
                                                                            @endif
                                                                            @else
                                                                            <span class="text-warning">No Due
                                                                                Date</span>
                                                                            @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span style="color: white; padding: 7px;"
                                                                            class="badge {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : ($installment->status === 'Failed' ? 'bg-secondary' : 'bg-warning')) }}">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
