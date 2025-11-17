@extends('layouts.app', ['page' => 'View Clients'])

@section('content')
<section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-start">
            {{-- <h3 class="mb-0 fw-bold">Client Details</h3> --}}
            <h5>View details for <b class="text-primary">{{ $client->name }}</b></h5>
        </div>
        <div class="d-flex align-items-center breadcrumb mb-0">
            <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                    href="{{ route('dashboard.unified') }}">Home</a></li>
            <li class="breadcrumb-item active">Client / Details</li>
        </div>
    </div>
</section>

<style>
    table td {
        vertical-align: middle !important;
    }
</style>
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="navbar p-3 bg-light shadow-sm">
                    <h2 class="text-start fw-bold">Client Details</h2>
                    <div>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary mr-2" title="Edit">
                            Edit
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary" title="Back">
                            Back
                        </a>
                    </div>
                </div>
                <!-- Alerts Top-Right -->
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
                    @if (session('success'))
                    <div class="alert alert-success text-white bg-success alert-dismissible custom-alert  fade-in"
                        role="alert" style="white-space: normal; word-wrap: break-word;">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade-in"
                        role="alert" style="white-space: normal; word-wrap: break-word;">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3 p-md-3">
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Company</label>
                                        <p class="mb-0 fw-semibold">{{ $client->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Phone</label>
                                        <p class="mb-0 fw-semibold">{{ $client->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Department</label>
                                        <p class="mb-0 fw-semibold">{{ $client->department()->first() ?
                                            $client->department()->first()->name : 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Address</label>
                                        <p class="mb-0 fw-semibold">{{ $client->address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Total
                                            Commitment</label>
                                        <p class="mb-0 fw-semibold">Rs {{
                                            number_format($client->commitments->sum('total_amount'),
                                            2) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Total
                                            PDCs</label>
                                        <p class="mb-0 fw-semibold">Rs {{
                                            number_format($client->pdcHistory->sum('commitment_amount'), 2)
                                            }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Active Commitment --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Active Commitment</label>
                                        <p class="mb-0 fw-semibold">
                                            @php
                                            $activeCommitmentAmount = 0;

                                            foreach ($client->commitments as $commitment) {
                                            foreach ($commitment->installments as $installment) {
                                            if (!in_array($installment->status, ['Complete', 'Failed'])) {
                                            $activeCommitmentAmount += ($installment->amount -
                                            $installment->paid_amount);
                                            }
                                            }
                                            }
                                            @endphp
                                            Rs {{ number_format($activeCommitmentAmount, 2) }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Active PDC --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-1">
                                        <label class="form-label fw-bold text-muted">Active PDC</label>
                                        <p class="mb-0 fw-semibold">
                                            @php
                                            $activePdcAmount = 0;

                                            foreach ($client->pdcHistory as $pdc) {
                                            foreach ($pdc->installments as $installment) {
                                            if (!in_array($installment->status, ['Complete', 'Failed'])) {
                                            $activePdcAmount += ($installment->amount - $installment->paid_amount);
                                            }
                                            }
                                            }
                                            @endphp
                                            Rs {{ number_format($activePdcAmount, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        /* .link-style {
                            text-decoration: none;
                            background: transparent !important;
                            border: none !important;
                            padding: 0;
                            width: 100%;
                            height: 100%;
                            text-align: left;
                        } */
                        /*
                        .link-style:focus {
                            box-shadow: none;
                            outline: none;
                        } */

                        .table-hover tbody tr:hover {
                            background-color: rgba(0, 0, 0, 0.05);
                        }
                    </style>

                    <!-- Active Commitments Section -->
                    <h3 class="fw-bold mt-4 mb-3">Active Commitments</h3>
                    @if ($client->commitments->whereNotIn('status', ['Complete', 'Failed'])->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No Active Commitments Found.</h5>
                            <p class="text-muted mb-0">It looks like this client has no pending financial
                                commitments at the moment.</p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-white mb-4" id="activeCommitmentsAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th scope="col">ID</th>
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
                                    @foreach ($client->commitments->whereNotIn('status', ['Complete',
                                    'Failed']) as $commitment)
                                    <tr class="align-middle accordion-item border-0"
                                        id="commitmentHeading{{ $commitment->id }}">
                                        <td class="fw-semibold text-center">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                #{{ $commitment->id }}
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
                                        <td class="fw-semibold">
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
                                                $installment = $commitment->installments()->where('status',
                                                '!=', 'Complete')->first();
                                                $isCompleted = !$installment;
                                                $dueDate = $installment?->due_date ?
                                                \Carbon\Carbon::parse($installment->due_date) : null;
                                                $remainingDays = $dueDate ?
                                                today()->startOfDay()->diffInDays($dueDate, false) : null;
                                                @endphp
                                                @if ($commitment->status === 'Failed')
                                                <h6 class="text-danger pt-2 align-center">Failed</h6>
                                                @elseif ($isCompleted)
                                                <h6 class="text-success pt-2 align-center">Completed</h6>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                                                    @elseif($remainingDays==0) Due Today @else {{ $remainingDays }} days
                                                    remaining @endif @else <span class="text-warning">No Due
                                                    Date
                                            </span>
                                            @endif
                                            </button>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                <span
                                                    class="fw-bold @if ($commitment->status == 'Complete') text-success @elseif($commitment->status == 'Overdue') text-danger @elseif($commitment->status == 'Failed') text-secondary @else text-warning @endif">
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
                                                <span class="badge bg-light text-dark">{{ $settledDate
                                                    }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentCollapse{{ $commitment->id }}">
                                                <span class="badge text-black">{{
                                                    $commitment->followups->count() }}</span>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('client.commitment.show', $commitment->id) }}"
                                                    class="btn" title="View" aria-label="View Commitment">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                                <a href="{{ route('client.commitment.edit', $commitment->id) }}"
                                                    class="btn" title="Edit" aria-label="Edit Commitment">
                                                    <i class="fas fa-pencil-alt text-success"></i>
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
                                                    <h5 class="text-muted mb-2 text-center mt-2 mt-2 text-center">
                                                        Installments</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 text-nowrap">
                                                            <thead class="table-light text-center">
                                                                <tr>
                                                                    <th>Amount</th>
                                                                    <th>Due Date</th>
                                                                    <th>Commitment Age(Days)</th>
                                                                    <th>Status</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($commitment->installments
                                                                as $installment)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        @if($installment->paid_amount > 0)
                                                                        <del>Rs {{
                                                                            number_format($installment->amount,
                                                                            2) }}</del><br>
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount
                                                                            - $installment->paid_amount, 2)
                                                                            }}</strong>
                                                                        @else
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount,
                                                                            2) }}</strong>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">{{
                                                                        $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td class="text-center">
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)
                                                                        : null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status ===
                                                                        'Complete')
                                                                        <h6 class="text-success">Completed
                                                                        </h6>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) Overdue by {{
                                                                            abs($remainingDays) }} days
                                                                            @elseif($remainingDays==0) Due Today @else
                                                                            {{ $remainingDays }} days remaining @endif
                                                                            @else <span class="text-warning">No
                                                                            Due Date</span>
                                                                            @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span style="color:white; padding:7px;"
                                                                            class="badge {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : 'bg-warning') }}">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if(in_array($installment->status,
                                                                        ['Pending', 'Overdue']))
                                                                        <button type="button" class="btn"
                                                                            title="Partial Payment"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#commitmentPayModal{{ $installment->id }}"
                                                                            aria-label="Pay Installment">
                                                                            <i
                                                                                class="fas fa-money-bill-wave text-success"></i>
                                                                        </button>
                                                                        <button type="button" class="btn"
                                                                            title="Follow-up" data-bs-toggle="modal"
                                                                            data-bs-target="#newInstallmentFollowupModal{{ $installment->id }}"
                                                                            aria-label="Create Follow-up">
                                                                            <i class="fas fa-comment text-info"></i>
                                                                        </button>
                                                                        @else
                                                                        <span class="text-success fw-bold">{{
                                                                            ucfirst($installment->status)
                                                                            }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @if($commitment->followups->where('installment_id',
                                                                $installment->id)->isNotEmpty())
                                                                <tr>
                                                                    <td colspan="5">
                                                                        <h5 class="text-muted mb-2 mt-2 text-center">
                                                                            Follow-ups for Installment #{{
                                                                            $installment->id }}</h5>
                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-sm table-bordered mb-0 text-nowrap">
                                                                                <thead class="table-light text-center">
                                                                                    <tr>
                                                                                        <th>Follow-up Date
                                                                                        </th>
                                                                                        <th>Original Due
                                                                                            Date</th>
                                                                                        <th>Comments</th>
                                                                                        <th>Status</th>
                                                                                        <th>Next Due Date
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($commitment->followups->where('installment_id',
                                                                                    $installment->id)
                                                                                    as $followup)
                                                                                    <tr>
                                                                                        <td class="text-center">
                                                                                            {{$followup->followup_date
                                                                                            ?
                                                                                            $followup->followup_date->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->original_due_date
                                                                                            ?
                                                                                            $followup->original_due_date->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td>{{
                                                                                            $followup->comments
                                                                                            ?? 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            <span
                                                                                                style="color: white; padding: 7px;"
                                                                                                class="badge {{ $followup->status === 'completed' ? 'bg-success' : ($followup->status === 'overdue' ? 'bg-danger' : 'bg-warning') }}">
                                                                                                {{
                                                                                                ucfirst($followup->status)
                                                                                                }}
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->next_due_date
                                                                                            ?
                                                                                            $followup->next_due_date->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                                <style>
                                                                    .modal.show {
                                                                        display: flex !important;
                                                                    }
                                                                </style>
                                                                <!-- New Follow-up Modal for Commitment Installment -->
                                                                <div class="modal fade align-items-center"
                                                                    id="newInstallmentFollowupModal{{ $installment->id }}"
                                                                    tabindex="-1" role="dialog"
                                                                    aria-labelledby="installmentFollowupLabel{{ $installment->id }}"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    style="white-space: normal"
                                                                                    id="installmentFollowupLabel{{ $installment->id }}">
                                                                                    Create Follow-up for
                                                                                    Installment #{{
                                                                                    $installment->id }}
                                                                                    (Commitment #{{
                                                                                    $commitment->id }})
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close">
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form
                                                                                    action="{{ route('commitments.followup', $commitment->id) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <input type="hidden"
                                                                                        name="installment_id"
                                                                                        value="{{ $installment->id }}">
                                                                                    <div class="form-group mb-3">
                                                                                        <label
                                                                                            for="followup_date_{{ $installment->id }}">Follow-up
                                                                                            Date:</label>
                                                                                        <input type="date"
                                                                                            name="followup_date"
                                                                                            id="followup_date_{{ $installment->id }}"
                                                                                            class="form-control mt-1"
                                                                                            required>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="comments_{{ $installment->id }}">Follow-up
                                                                                            Comments:</label>
                                                                                        <textarea name="comments"
                                                                                            id="comments_{{ $installment->id }}"
                                                                                            placeholder="Follow-up Reason"
                                                                                            class="form-control mt-1"
                                                                                            required></textarea>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="submit"
                                                                                            class="btn btn-success">Create
                                                                                            Follow-up</button>
                                                                                        <button type="button"
                                                                                            class="btn btn-danger"
                                                                                            data-bs-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <!-- Payment Modals -->
                                                                <div class="modal fade"
                                                                    id="commitmentPayModal{{ $installment->id }}"
                                                                    tabindex="-1"
                                                                    aria-labelledby="payModalLabel{{ $installment->id }}"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered ">
                                                                        <div class="modal-content shadow-lg rounded">
                                                                            <div class="modal-header p-3">
                                                                                <h5 class="modal-title"
                                                                                    id="payModalLabel{{ $installment->id }}">
                                                                                    <i class="fas fa-wallet me-2"></i>
                                                                                    Partial Installment of
                                                                                    Commitment
                                                                                    #{{$commitment->id}}
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close">
                                                                                </button>
                                                                            </div>
                                                                            <form
                                                                                action="{{ route('installments.pay', $installment->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <input type="hidden"
                                                                                    name="installment_id"
                                                                                    value="{{ $installment->id }}">
                                                                                <input type="hidden" name="pc_id"
                                                                                    value="{{ $commitment->id }}">
                                                                                <div class="modal-body p-3">
                                                                                    <div class="form-group mb-3">
                                                                                        <label
                                                                                            for="pay_amount_{{ $installment->id }}"
                                                                                            class="fw-bold">
                                                                                            <i
                                                                                                class="fas fa-money-bill-wave me-1"></i>
                                                                                            Amount to Pay
                                                                                        </label>
                                                                                        <input type="number"
                                                                                            class="form-control rounded mt-1"
                                                                                            id="pay_amount_{{ $installment->id }}"
                                                                                            name="pay_amount"
                                                                                            placeholder="Enter Amount"
                                                                                            min="1"
                                                                                            max="{{ $installment->amount - $installment->paid_amount }}"
                                                                                            required>
                                                                                    </div>
                                                                                    <div class="form-group mb-3">
                                                                                        <label
                                                                                            for="remarks_{{ $installment->id }}"
                                                                                            class="fw-bold">
                                                                                            <i
                                                                                                class="fas fa-comment me-1"></i>
                                                                                            Remarks
                                                                                        </label>
                                                                                        <textarea
                                                                                            class="form-control rounded mt-1"
                                                                                            id="remarks_{{ $installment->id }}"
                                                                                            name="remarks"
                                                                                            placeholder="Enter Remarks"
                                                                                            rows="3"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer p-3">
                                                                                    <button type="submit"
                                                                                        class="btn btn-success fw-bold">
                                                                                        <i style="color: white;"
                                                                                            class="fas fa-check me-1"></i>
                                                                                        Confirm Payment
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn btn-danger fw-bold"
                                                                                        data-bs-dismiss="modal">
                                                                                        <i style="color: white;"
                                                                                            class="fas fa-times me-1"></i>
                                                                                        Cancel
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                    @if ($client->pdcHistory->whereNotIn('status', ['Complete', 'Failed'])->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No Active PDCs Found.</h5>
                            <p class="text-muted mb-0">There are no pending post-dated cheques for this
                                client.</p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-white mb-4" id="activePdcAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-primary text-white">
                                    <tr class="text-center">
                                        <th scope="col">ID</th>
                                        <th scope="col">Pdc Amount</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Salesman</th>
                                        <th scope="col">Pdc Age</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Settled Date</th>
                                        <th scope="col">Follow-up</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->pdcHistory->whereNotIn('status', ['Complete',
                                    'Failed']) as $pdc)
                                    <tr class="align-middle accordion-item border-0 text-center"
                                        id="pdcHeading{{ $pdc->id }}">
                                        <td class="fw-semibold text-center">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                #{{ $pdc->id }}
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
                                                $remainingDays = $dueDate ?
                                                today()->startOfDay()->diffInDays($dueDate, false) : null;
                                                @endphp
                                                @if ($pdc->status === 'Failed')
                                                <h6 class="text-danger pt-2 align-center">Failed</h6>
                                                @elseif ($isCompleted)
                                                <h6 class="text-success pt-2 align-center">Completed</h6>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                                                    @elseif ($remainingDays==0) Due Today @else {{ $remainingDays }}
                                                    days remaining @endif @else <span class="text-warning">No Due
                                                    Date
                                            </span>
                                            @endif
                                            </button>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                <span
                                                    class="fw-bold @if ($pdc->status == 'Complete') text-success @elseif($pdc->status == 'Overdue') text-danger @elseif($pdc->status == 'Failed') text-secondary @else text-warning @endif">
                                                    {{ ucfirst($pdc->status) }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                <span class="badge bg-light text-dark badge-lg">{{
                                                    \Carbon\Carbon::parse($pdc->created_at)->format('Y-m-d
                                                    H:i') }}</span>
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
                                                <span class="badge bg-light text-dark">{{ $settledDate
                                                    }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcCollapse{{ $pdc->id }}">
                                                <span class="badge text-black">{{ $pdc->followups->count()
                                                    }}</span>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('client.pdc.show', $pdc->id) }}" class="btn"
                                                    title="View" aria-label="View PDC">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                                <a href="{{ route('client.pdc.edit', $pdc->id) }}" class="btn"
                                                    title="Edit" aria-label="Edit PDC">
                                                    <i class="fas fa-pencil-alt text-success"></i>
                                                </a>
                                                {{-- <button type="button" class="btn" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $pdc->id }}"
                                                    aria-label="Delete PDC">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button> --}}
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
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($pdc->installments
                                                                as $installment)
                                                                <tr class="text-center">
                                                                    <td class="text-center">
                                                                        @if ($installment->data1 > 0)
                                                                        <del>{{
                                                                            number_format($installment->amount,
                                                                            2) }}</del><br>
                                                                        <strong class="text-success">{{
                                                                            number_format($installment->amount
                                                                            - $installment->data1, 2)
                                                                            }}</strong>
                                                                        @else
                                                                        <strong class="text-success"> {{
                                                                            number_format($installment->amount,
                                                                            2) }} </strong>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $installment->chequeno ?? 'N/A'
                                                                        }}</td>
                                                                    <td>{{ $installment->bankName ?? 'N/A'
                                                                        }}</td>
                                                                    <td>{{ $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td>
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)
                                                                        : null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status ===
                                                                        'Failed')
                                                                        <h6 class="text-danger">Failed</h6>
                                                                        @elseif ($installment->status ===
                                                                        'Complete')
                                                                        <h6 class="text-success">Complete
                                                                        </h6>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) Overdue by {{
                                                                            abs($remainingDays) }} days
                                                                            @elseif($remainingDays==0) Due Today @else
                                                                            {{ $remainingDays }} days left @endif @else
                                                                            <span class="text-warning">
                                                                            No Due
                                                                            Date</span>
                                                                            @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span style="color: white; padding: 7px;"
                                                                            class="badge {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : ($installment->status === 'Failed' ? 'bg-secondary' : 'bg-warning')) }}">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        @if (in_array($installment->status,
                                                                        ['Pending', 'Overdue']))
                                                                        <button type="button" class="btn"
                                                                            title="Partial Payment"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#payModal{{ $installment->id }}"
                                                                            aria-label="Pay Installment">
                                                                            <i
                                                                                class="fas fa-money-bill-wave text-success"></i>
                                                                        </button>
                                                                        <button type="button" class="btn"
                                                                            title="Follow-up" data-bs-toggle="modal"
                                                                            data-bs-target="#newPdcInstallmentFollowupModal{{ $installment->id }}"
                                                                            aria-label="Create Follow-up">
                                                                            <i class="fas fa-comment text-info"></i>
                                                                        </button>
                                                                        @else
                                                                        <p class="text-success fw-bold">{{
                                                                            ucfirst($installment->status)
                                                                            }}</p>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @if($pdc->followups->where('pdc_installment_id',
                                                                $installment->id)->isNotEmpty())
                                                                <tr>
                                                                    <td colspan="7">
                                                                        <h5 class="text-muted mb-2 mt-2 text-center">
                                                                            Follow-ups for Installment #{{
                                                                            $installment->id }}</h5>
                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-sm table-bordered mb-0 text-nowrap">
                                                                                <thead class="table-light text-center">
                                                                                    <tr>
                                                                                        <th>Follow-up Date
                                                                                        </th>
                                                                                        <th>Original Due
                                                                                            Date</th>
                                                                                        <th>Comments</th>
                                                                                        <th>Status</th>
                                                                                        <th>Next Due Date
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($pdc->followups->where('pdc_installment_id',
                                                                                    $installment->id)
                                                                                    as $followup)
                                                                                    <tr>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->followup_date
                                                                                            ?
                                                                                            \Carbon\Carbon::parse($followup->followup_date)->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->original_due_date
                                                                                            ?
                                                                                            \Carbon\Carbon::parse($followup->original_due_date)->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td>{{
                                                                                            $followup->comments
                                                                                            ?? 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            <span
                                                                                                style="color: white; padding: 7px;"
                                                                                                class="badge {{ $followup->status === 'Complete' ? 'bg-success' : ($followup->status === 'Overdue' ? 'bg-danger' : 'bg-warning') }}">
                                                                                                {{
                                                                                                $followup->status
                                                                                                }}
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->next_due_date
                                                                                            ?
                                                                                            \Carbon\Carbon::parse($followup->next_due_date)->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                                <style>
                                                                    .modal.show {
                                                                        display: flex !important;
                                                                    }
                                                                </style>
                                                                <!-- New Follow-up Modal for PDC Installment -->
                                                                <div class="modal fade align-items-center"
                                                                    id="newPdcInstallmentFollowupModal{{ $installment->id }}"
                                                                    tabindex="-1" role="dialog"
                                                                    aria-labelledby="pdcInstallmentFollowupLabel{{ $installment->id }}"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    style="white-space: normal"
                                                                                    id="pdcInstallmentFollowupLabel{{ $installment->id }}">
                                                                                    Create Follow-up for
                                                                                    Installment #{{
                                                                                    $installment->id }} (PDC
                                                                                    #{{ $pdc->id }})
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"> </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form
                                                                                    action="{{ route('pdcs.followup', $pdc->id) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <input type="hidden"
                                                                                        name="installment_id"
                                                                                        value="{{ $installment->id }}">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="followup_date_{{ $installment->id }}">Follow-up
                                                                                            Date:</label>
                                                                                        <input type="date"
                                                                                            name="followup_date"
                                                                                            id="followup_date_{{ $installment->id }}"
                                                                                            class="form-control mt-1"
                                                                                            required>
                                                                                    </div>
                                                                                    <div class="form-group mt-2">
                                                                                        <label
                                                                                            for="comments_{{ $installment->id }}">Follow-up
                                                                                            Comments:</label>
                                                                                        <textarea name="comments"
                                                                                            id="comments_{{ $installment->id }}"
                                                                                            placeholder="Follow-up Reason"
                                                                                            class="form-control mt-1"
                                                                                            required></textarea>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="submit"
                                                                                            class="btn btn-success">Create
                                                                                            Follow-up</button>
                                                                                        <button type="button"
                                                                                            class="btn btn-danger"
                                                                                            data-bs-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <style>
                                                                    .modal.show {
                                                                        display: flex !important
                                                                    }
                                                                </style>
                                                                <!-- Pay Modal for PDC Installment -->
                                                                <div class="modal fade"
                                                                    id="payModal{{ $installment->id }}" tabindex="-1"
                                                                    aria-labelledby="payModalLabel{{ $installment->id }}"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content shadow-lg rounded">
                                                                            <div
                                                                                class="modal-header d-flex justify-content-between align-items-center p-3">
                                                                                <h5 class="modal-title"
                                                                                    style="white-space: normal"
                                                                                    id="payModalLabel{{ $installment->id }}">
                                                                                    <i class="fas fa-wallet mr-2"></i>
                                                                                    Partial Installment
                                                                                    Payment of Pdc
                                                                                    #{{$pdc->id}}
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close">

                                                                                </button>
                                                                            </div>
                                                                            <form
                                                                                action="{{ route('pdcInstallments.pay', $installment->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <input type="hidden"
                                                                                    name="installment_id"
                                                                                    value="{{ $installment->id }}">
                                                                                <input type="hidden" name="pc_id"
                                                                                    value="{{ $pdc->id }}">
                                                                                <div class="modal-body p-3">
                                                                                    <div class="form-group mb-3">
                                                                                        <label
                                                                                            for="pay_amount_{{ $installment->id }}"
                                                                                            class="fw-bold">
                                                                                            <i
                                                                                                class="fas fa-money-bill-wave me-1"></i>
                                                                                            Amount to Pay
                                                                                        </label>
                                                                                        <input type="number"
                                                                                            class="form-control rounded mt-1"
                                                                                            id="pay_amount_{{ $installment->id }}"
                                                                                            name="pay_amount"
                                                                                            placeholder="Enter Amount"
                                                                                            min="1"
                                                                                            max="{{ $installment->amount - $installment->data1 }}"
                                                                                            required>
                                                                                    </div>
                                                                                    <div class="form-group mb-3">
                                                                                        <label
                                                                                            for="remarks_{{ $installment->id }}"
                                                                                            class="fw-bold">
                                                                                            <i
                                                                                                class="fas fa-comment me-1"></i>
                                                                                            Remarks
                                                                                        </label>
                                                                                        <textarea
                                                                                            class="form-control rounded mt-1"
                                                                                            id="remarks_{{ $installment->id }}"
                                                                                            name="remarks"
                                                                                            placeholder="Enter Remarks"
                                                                                            rows="3"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer p-3">
                                                                                    <button type="submit"
                                                                                        class="btn btn-success fw-bold">
                                                                                        <i style="color: white;"
                                                                                            class="fas fa-check me-1"></i>
                                                                                        Confirm Payment
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn btn-danger fw-bold"
                                                                                        data-bs-dismiss="modal">
                                                                                        <i style="color: white;"
                                                                                            class="fas fa-times me-1"></i>
                                                                                        Cancel
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Delete Modal for this PDC -->
                                    <div class="modal fade" id="deleteModal{{ $pdc->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="deleteModalLabel{{ $pdc->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                                            <div class="modal-content shadow-lg rounded">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $pdc->id }}">
                                                        Confirm Deletion
                                                    </h5>
                                                    <button type="button" class="close text-white"
                                                        data-bs-dismiss="modal" aria-label="Close"
                                                        style="font-size: 20px; background-color: transparent; border: none;">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center py-4">
                                                    <p class="mb-2 fs-5">Are you sure you want to delete PDC
                                                        #{{ $pdc->id }}?</p>
                                                    <p class="text-muted small">This action cannot be
                                                        undone.</p>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-center gap-3">
                                                    <button type="button" class="btn btn-secondary px-4 py-2"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('pdcs.destroy', $pdc->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger px-4 py-2">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Commitment History Section -->
                    <h3 class="fw-bold mt-4 mb-3">Commitment Histories</h3>
                    @if ($client->commitments->whereIn('status', ['Complete', 'Failed'])->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No Commitment History Found.</h5>
                            <p class="text-muted mb-0">There is no record of past or completed commitments
                                for this client.</p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-light mb-4" id="commitmentHistoryAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-secondary text-white">
                                    <tr class="text-center">
                                        <th scope="col">ID</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Paid Amount</th>
                                        <th scope="col">Pending Amount</th>
                                        <th scope="col">Commitment Age</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->commitments->whereIn('status', ['Complete',
                                    'Failed']) as $commitment)
                                    <tr class="align-middle accordion-item border-0 text-center"
                                        id="commitmentHistoryHeading{{ $commitment->id }}">
                                        <td class="fw-semibold text-center">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                #{{ $commitment->id }}
                                            </span>
                                        </td>
                                        <td class="text-primary fw-bold">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                @php
                                                $totalCommitmentAmount =
                                                $commitment->installments->sum('amount') +
                                                $commitment->failedInstallments->sum('amount');
                                                @endphp
                                                Rs {{ number_format($totalCommitmentAmount, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-success fw-bold">
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
                                        <td class="text-danger fw-bold">
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
                                                $installment = $commitment->installments()->where('status',
                                                '!=', 'Complete')->first();
                                                $isCompleted = !$installment;
                                                $dueDate = $installment?->due_date ?
                                                \Carbon\Carbon::parse($installment->due_date) : null;
                                                $remainingDays = $dueDate ?
                                                today()->startOfDay()->diffInDays($dueDate, false) : null;
                                                @endphp
                                                @if ($commitment->status === 'Failed')
                                                <h6 class="text-danger pt-2 align-center">Failed</h6>
                                                @elseif ($isCompleted)
                                                <h6 class="text-success pt-2 align-center">Completed</h6>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                                                    @elseif($remainingDays==0) Due Today @else {{ $remainingDays }} days
                                                    remaining @endif @else <span class="text-warning">No Due
                                                    Date
                                            </span>
                                            @endif
                                            </button>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#commitmentHistoryCollapse{{ $commitment->id }}"
                                                aria-expanded="false"
                                                aria-controls="commitmentHistoryCollapse{{ $commitment->id }}">
                                                <span
                                                    class="badge badge-sm @if ($commitment->status == 'Complete') bg-success @elseif($commitment->status == 'Overdue') bg-danger @elseif($commitment->status == 'Failed') bg-secondary @else bg-warning @endif"
                                                    style="pointer-events: none; cursor: default; padding: 6px 10px; color: white;">
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
                                                    $commitment->created_at->format('Y-m-d H:i')
                                                    }}</span>
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
                                                    @if ($commitment->status === 'Failed' &&
                                                    $commitment->failedInstallments->isNotEmpty())
                                                    <h5 class="text-muted mb-2 text-center mt-2">Failed Installments
                                                    </h5>
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
                                                                @foreach ($commitment->failedInstallments
                                                                as $installment)
                                                                <tr class="text-center">
                                                                    <td class="text-center">
                                                                        <strong class="text-danger">Rs {{
                                                                            number_format($installment->amount,
                                                                            2) }}</strong>
                                                                    </td>
                                                                    <td class="text-center">{{
                                                                        $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        :
                                                                        'N/A' }}</td>
                                                                    <td class="text-center">
                                                                        <h6 class="text-danger">Failed</h6>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span style="color:white; padding:7px;"
                                                                            class="badge bg-secondary">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @elseif ($commitment->installments->isNotEmpty())
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
                                                                @foreach ($commitment->installments
                                                                as $installment)
                                                                <tr class="text-center">
                                                                    <td>
                                                                        @if($installment->paid_amount > 0)
                                                                        <del>Rs {{
                                                                            number_format($installment->amount,
                                                                            2) }}</del><br>
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount
                                                                            - $installment->paid_amount, 2)
                                                                            }}</strong>
                                                                        @else
                                                                        <strong class="text-success">Rs {{
                                                                            number_format($installment->amount,
                                                                            2) }}</strong>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{
                                                                        $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td>
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)
                                                                        : null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status ===
                                                                        'Failed')
                                                                        <h6 class="text-danger">Failed</h6>
                                                                        @elseif ($installment->status ===
                                                                        'Complete')
                                                                        <h6 class="text-success">Completed
                                                                        </h6>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) Overdue by {{
                                                                            abs($remainingDays) }} days
                                                                            @elseif($remainingDays==0) Due Today @else
                                                                            {{ $remainingDays }} days remaining @endif
                                                                            @else <span class="text-warning">No
                                                                            Due Date</span>
                                                                            @endif
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge text-white {{ $installment->status === 'Complete' ? 'bg-success' : ($installment->status === 'Overdue' ? 'bg-danger' : ($installment->status === 'Failed' ? 'bg-secondary' : 'bg-warning')) }}">
                                                                            {{ $installment->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @if($commitment->followups->where('installment_id',
                                                                $installment->id)->isNotEmpty())
                                                                <tr>
                                                                    <td colspan="4">
                                                                        <h5 class="text-muted mb-2 mt-2 text-center">
                                                                            Follow-ups for Installment #{{
                                                                            $installment->id }}</h5>
                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-sm table-bordered mb-0 text-nowrap">
                                                                                <thead class="table-light text-center">
                                                                                    <tr>
                                                                                        <th>Follow-up Date
                                                                                        </th>
                                                                                        <th>Original Due
                                                                                            Date</th>
                                                                                        <th>Comments</th>
                                                                                        <th>Status</th>
                                                                                        <th>Next Due Date
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($commitment->followups->where('installment_id',
                                                                                    $installment->id)
                                                                                    as $followup)
                                                                                    <tr class="text-center">
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->followup_date
                                                                                            ?
                                                                                            $followup->followup_date->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->original_due_date
                                                                                            ?
                                                                                            $followup->original_due_date->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td>{{
                                                                                            $followup->comments
                                                                                            ?? 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            <span
                                                                                                style="color: white; padding: 7px;"
                                                                                                class="badge {{ $followup->status === 'completed' ? 'bg-success' : ($followup->status === 'overdue' ? 'bg-danger' : 'bg-warning') }}">
                                                                                                {{
                                                                                                ucfirst($followup->status)
                                                                                                }}
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->next_due_date
                                                                                            ?
                                                                                            $followup->next_due_date->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endif
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
                    @if ($client->pdcHistory->whereIn('status', ['Complete', 'Failed'])->isEmpty())
                    <div class="card border-0 rounded-4 bg-light-subtle shadow-sm mb-4">
                        <div class="card-body py-4 text-center">
                            <h5 class="text-muted fw-bold mb-2">No PDC History Found.</h5>
                            <p class="text-muted mb-0">There are no post-dated cheques recorded for this
                                client.</p>
                        </div>
                    </div>
                    @else
                    <div class="accordion shadow-sm rounded bg-light mb-4" id="pdcHistoryAccordion">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="bg-secondary text-white text-center">
                                    <tr class="text-center">
                                        <th scope="col">ID</th>
                                        <th scope="col">Pdc Amount</th>
                                        <th scope="col">Party Name</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Pdc Age</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->pdcHistory->whereIn('status', ['Complete', 'Failed'])
                                    as $pdc)
                                    <tr class="align-middle accordion-item border-0 text-center"
                                        id="pdcHistoryHeading{{ $pdc->id }}">
                                        <td class="fw-semibold text-center">
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                #{{ $pdc->id }}
                                            </span>
                                        </td>
                                        <td class="text-success fw-bold">
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
                                                {{ $pdc->client->name ?? 'N/A' }}
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
                                                $remainingDays = $dueDate ?
                                                today()->startOfDay()->diffInDays($dueDate, false) : null;
                                                @endphp
                                                @if ($pdc->status === 'Failed')
                                                <h6 class="text-danger pt-2 align-center">Failed</h6>
                                                @elseif ($isCompleted)
                                                <h6 class="text-success pt-2 align-center">Completed</h6>
                                                @elseif ($dueDate)
                                                @if ($remainingDays < 0) Overdue by {{ abs($remainingDays) }} days
                                                    @elseif($remainingDays==0) Due Today @else {{ $remainingDays }} days
                                                    remaining @endif @else <span class="text-warning">No Due
                                                    Date
                                            </span>
                                            @endif
                                            </button>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                <span
                                                    class="fw-bold @if ($pdc->status == 'Complete') text-success @elseif($pdc->status == 'Overdue') text-danger @elseif($pdc->status == 'Failed') text-danger @else text-warning @endif">
                                                    {{ ucfirst($pdc->status) }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="link-style" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#pdcHistoryCollapse{{ $pdc->id }}" aria-expanded="false"
                                                aria-controls="pdcHistoryCollapse{{ $pdc->id }}">
                                                <span class="badge bg-light text-dark">{{
                                                    \Carbon\Carbon::parse($pdc->created_at)->format('Y-m-d
                                                    H:i') }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="p-0 bg-light">
                                            <div id="pdcHistoryCollapse{{ $pdc->id }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="pdcHistoryHeading{{ $pdc->id }}"
                                                data-bs-parent="#pdcHistoryAccordion">
                                                <div class="accordion-body p-3">
                                                    @if ($pdc->status === 'Failed' &&
                                                    $pdc->failedInstallments->isNotEmpty())
                                                    <h5 class="text-muted mb-2 mt-2 text-center">Failed Installments
                                                    </h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 text-nowrap">
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
                                                                @foreach ($pdc->failedInstallments
                                                                as $installment)
                                                                <tr class="text-center">
                                                                    <td>
                                                                        <strong class="text-danger">Rs {{
                                                                            number_format($installment->amount,
                                                                            2) }}</strong>
                                                                    </td>
                                                                    <td>{{ $installment->chequeno ?? 'N/A'
                                                                        }}</td>
                                                                    <td>{{ $installment->bankName ?? 'N/A'
                                                                        }}</td>
                                                                    <td>{{ $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        :
                                                                        'N/A' }}</td>
                                                                    <td class="text-center">
                                                                        <h6 class="text-danger">Failed</h6>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <p> {{ $installment->status }} </p>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @elseif ($pdc->installments->isNotEmpty())
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
                                                                @foreach ($pdc->installments
                                                                as $installment)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        @if ($installment->data1 > 0)
                                                                        <del>{{
                                                                            number_format($installment->amount,
                                                                            2) }}</del><br>
                                                                        <strong class="text-success">{{
                                                                            number_format($installment->amount
                                                                            - $installment->data1, 2)
                                                                            }}</strong>
                                                                        @else
                                                                        <strong class="text-success"> {{
                                                                            number_format($installment->amount,
                                                                            2) }} </strong>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $installment->chequeno ?? 'N/A'
                                                                        }}</td>
                                                                    <td>{{ $installment->bankName ?? 'N/A'
                                                                        }}</td>
                                                                    <td>{{ $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d')
                                                                        : 'N/A' }}</td>
                                                                    <td>
                                                                        @php
                                                                        $dueDate = $installment->due_date ?
                                                                        \Carbon\Carbon::parse($installment->due_date)
                                                                        : null;
                                                                        $remainingDays = $dueDate ?
                                                                        today()->startOfDay()->diffInDays($dueDate,
                                                                        false) : null;
                                                                        @endphp
                                                                        @if ($installment->status ===
                                                                        'Failed')
                                                                        <h6 class="text-danger">Failed</h6>
                                                                        @elseif ($installment->status ===
                                                                        'Complete')
                                                                        <h6 class="text-success">Complete
                                                                        </h6>
                                                                        @elseif ($dueDate)
                                                                        @if ($remainingDays < 0) Overdue by {{
                                                                            abs($remainingDays) }} days
                                                                            @elseif($remainingDays==0) Due Today @else
                                                                            {{ $remainingDays }} days left @endif @else
                                                                            <span class="text-warning">
                                                                            No Due
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
                                                                @if($pdc->followups->where('pdc_installment_id',
                                                                $installment->id)->isNotEmpty())
                                                                <tr>
                                                                    <td colspan="6">
                                                                        <h5 class="text-muted mb-2 mt-2 text-center">
                                                                            Follow-ups for Installment #{{
                                                                            $installment->id }}</h5>
                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-sm table-bordered mb-0 text-nowrap">
                                                                                <thead class="table-light text-center">
                                                                                    <tr>
                                                                                        <th>Follow-up Date
                                                                                        </th>
                                                                                        <th>Original Due
                                                                                            Date</th>
                                                                                        <th>Comments</th>
                                                                                        <th>Status</th>
                                                                                        <th>Next Due Date
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($pdc->followups->where('pdc_installment_id',
                                                                                    $installment->id)
                                                                                    as $followup)
                                                                                    <tr>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->followup_date
                                                                                            ?
                                                                                            \Carbon\Carbon::parse($followup->followup_date)->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->original_due_date
                                                                                            ?
                                                                                            \Carbon\Carbon::parse($followup->original_due_date)->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                        <td>{{
                                                                                            $followup->comments
                                                                                            ?? 'N/A' }}</td>
                                                                                        <td class="text-center">
                                                                                            <span
                                                                                                style="color: white; padding: 7px;"
                                                                                                class="badge {{ $followup->status === 'Complete' ? 'bg-success' : ($followup->status === 'Overdue' ? 'bg-danger' : 'bg-warning') }}">
                                                                                                {{
                                                                                                $followup->status
                                                                                                }}
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                            $followup->next_due_date
                                                                                            ?
                                                                                            \Carbon\Carbon::parse($followup->next_due_date)->format('Y-m-d')
                                                                                            : 'N/A' }}</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endif
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

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center g-3 bg-primary text-white">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="fw-bold">Name:</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="fw-bold">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="fw-bold">Phone:</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="company" class="fw-bold">Company:</label>
                            <input type="text" name="company" id="company" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="fw-bold">Department:</label>
                            <input type="text" name="department" id="department" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="fw-bold">Address:</label>
                            <textarea name="address" id="address" class="form-control" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select form-control" id="department" name="department" required>
                                    <option value="" disabled selected>-- Select Department --</option>
                                    <option value="Electrical" {{ old('department')=='Electrical' ? 'selected' : '' }}>
                                        Electrical</option>
                                    <option value="Electronics" {{ old('department')=='Electronics' ? 'selected' : ''
                                        }}>Electronics</option>
                                </select>
                                <label for="department">Department</label>
                            </div>
                            @error('department')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Client</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('.sidebar-toggle').on('click', function () {
            $('#sidebar').toggleClass('active');
            if ($('#sidebar').hasClass('active')) {
                $('.page-content').css('margin-left', '250px');
            } else {
                $('.page-content').css('margin-left', '0');
            }
        });
        if ($('#sidebar').hasClass('active')) {
            $('.page-content').css('margin-left', '250px');
        } else {
            $('.page-content').css('margin-left', '0');
        }

        // Show/hide full remarks
        window.showFullText = function(id) {
            $('#shortText' + id).hide();
            $('#fullText' + id).show();
        };
        window.hideFullText = function(id) {
            $('#shortText' + id).show();
            $('#fullText' + id).hide();
        };

        // Installment row management
        $('.addInstallmentRowBtn').on('click', function () {
            let recordId = $(this).attr('class').match(/recordId(\d+)/)[1];
            let table = $('#installmentsTable' + recordId + ' tbody');
            let rowCount = table.find('tr').length;
            let newRow = `
                <tr>
                    <td><input type="text" name="installments[${rowCount}][chequeno]" placeholder="Cheque No" class="form-control" required></td>
                    <td><input type="text" name="installments[${rowCount}][bankName]" placeholder="Bank Name" class="form-control" required></td>
                    <td><input type="number" name="installments[${rowCount}][amount]" placeholder="Amount" class="form-control inputAmountUpdate${recordId}" required></td>
                    <td><input type="date" name="installments[${rowCount}][due_date]" class="form-control" required></td>
                    <td>
                        <select name="installments[${rowCount}][status]" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="Complete">Complete</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm removeInstallmentUpdate recordId${recordId}">X</button></td>
                </tr>
            `;
            table.append(newRow);
            updateTotalEdit(recordId);
        });

        $(document).on('click', '.removeInstallmentUpdate', function () {
            let row = $(this).closest('tr');
            let recordId = $(this).attr('class').match(/recordId(\d+)/)[1];
            row.remove();
            updateTotalEdit(recordId);
        });

        $(document).on('input', '.inputAmountUpdate' + $('.addInstallmentRowBtn').attr('class').match(/recordId(\d+)/)[1], function () {
            let recordId = $(this).attr('class').match(/inputAmountUpdate(\d+)/)[1];
            updateTotalEdit(recordId);
        });

        function updateTotalEdit(recordId) {
            let total = 0;
            $('#installmentsTable' + recordId + ' .inputAmountUpdate' + recordId).each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('#commitment_amount_' + recordId).val(total.toFixed(2));
        }
    });
</script>
@endsection
