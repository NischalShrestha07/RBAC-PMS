@extends('layouts.electricalApp', ['page' => 'Notifications'])

@section('content')
<div class="py-2">
    <!-- Content Header -->
    <section class="content-header mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-6 fw-bold mb-2">Notifications</h1>
                <p class="text-muted lead">Stay updated with your PDC and Commitment notifications</p>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb mb-0">
                    <ol class="breadcrumb bg-transparent mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="{{ route('electrical.dashboard') }}"
                                class="text-primary text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header py-4 px-4 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-semibold">
                    <i class="fas fa-bell me-2"></i> Your Notifications
                </h4>
                <a href="{{ route('electrical.dashboard') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
            <div class="card-body p-3 p-md-3">
                <!-- Navigation Tabs -->
                <ul class="nav nav-pills mb-4" id="notificationTabs" role="tablist">
                    <li class="nav-item me-2" role="presentation">
                        <button
                            class="nav-link active d-flex align-items-center justify-content-between px-4 py-2 rounded-pill border border-primary"
                            id="pdc-tab" data-bs-toggle="pill" data-bs-target="#pdc" type="button" role="tab"
                            aria-controls="pdc" aria-selected="true">
                            <span class="fw-semibold">PDC</span>
                            {{-- <span class="badge bg-danger text-white rounded-pill ms-2" id="pdcBadge">{{
                                $notifications['pdcCount'] }}</span> --}}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link d-flex align-items-center justify-content-between px-4 py-2 rounded-pill border border-primary"
                            id="commitment-tab" data-bs-toggle="pill" data-bs-target="#commitment" type="button"
                            role="tab" aria-controls="commitment" aria-selected="false">
                            <span class="fw-semibold">Commitment</span>
                            {{-- <span class="badge bg-danger text-white rounded-pill ms-2" id="commitmentBadge">{{
                                $notifications['commitmentCount'] }}</span> --}}
                        </button>
                    </li>
                </ul>
                <!-- Tab Content -->
                <div class="tab-content" id="notificationTabsContent">
                    <!-- PDC Notifications -->
                    <div class="tab-pane fade show active" id="pdc" role="tabpanel" aria-labelledby="pdc-tab">
                        @if ($notifications['PDC Notification']->isEmpty() ||
                        !$notifications['PDC Notification']->contains(fn($n) => $n->installments->isNotEmpty() &&
                        $n->client))
                        <div class="alert alert-info text-center rounded-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i> No PDC notifications available.
                        </div>
                        @else
                        @foreach ($notifications['PDC Notification'] as $pdcNotifications)
                        @if ($pdcNotifications->installments->isNotEmpty() && $pdcNotifications->client)
                        @foreach ($pdcNotifications->installments as $installment)
                        <div class="card mb-2 border-0 shadow-sm rounded-3 transition-all hover-shadow"
                            data-installment-id="{{ $installment->id }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-secondary">
                                        <i class="fas fa-file-invoice-dollar"></i> PDC Notification
                                    </h6>
                                    <span
                                        class="badge
                                                                            @if ($installment->status == 'Pending') bg-warning text-dark
                                                                            @elseif ($installment->status == 'Overdue') bg-danger text-white @endif rounded-pill px-3 py-2">
                                        {{ $installment->status }}
                                    </span>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong class="fw-semibold">Client Name:</strong> {{
                                            $pdcNotifications->client->name }}</p>
                                        <p class="mb-1"><strong class="fw-semibold">Phone:</strong> {{
                                            $pdcNotifications->client->phone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong class="fw-semibold">Installment Amount:</strong> {{
                                            number_format($installment->amount, 2) }}</p>
                                        <p class="mb-1"><strong class="fw-semibold">Salesman:</strong> {{
                                            $pdcNotifications->agent->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-muted mb-0">
                                        <strong>Due Date:</strong>
                                        {{ \Carbon\Carbon::parse($installment->due_date)->format('M d, Y') }}
                                    </p>
                                    <a href="{{ route('electrical.client.pdc.show', $pdcNotifications->id) }}"
                                        class="btn btn-secondary btn-sm view-details" data-type="pdc"
                                        data-installment-id="{{ $installment->id }}"
                                        data-notification-id="{{ $pdcNotifications->id }}">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        @endforeach
                        @endif
                    </div>

                    <!-- Commitment Notifications -->
                    <div class="tab-pane fade" id="commitment" role="tabpanel" aria-labelledby="commitment-tab">
                        @if ($notifications['Commitment Notification']->isEmpty() ||
                        !$notifications['Commitment Notification']->contains(fn($n) =>
                        $n->installments->isNotEmpty() && $n->client))
                        <div class="alert alert-info text-center rounded-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i> No Commitment notifications available.
                        </div>
                        @else
                        @foreach ($notifications['Commitment Notification'] as $commitNotification)
                        @if ($commitNotification->installments->isNotEmpty() && $commitNotification->client)
                        @foreach ($commitNotification->installments as $installment)
                        <div class="card mb-2 border-0 shadow-sm rounded-3 transition-all hover-shadow"
                            data-installment-id="{{ $installment->id }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-secondary">
                                        <i class="fas fa-file-contract"></i> Commitment Notification
                                    </h6>
                                    <span
                                        class="badge
                                                                            @if ($installment->status == 'Pending') bg-warning text-dark
                                                                            @elseif ($installment->status == 'Overdue') bg-danger text-white @endif rounded-pill px-3 py-2">
                                        {{ $installment->status }}
                                    </span>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong class="fw-semibold">Client Name:</strong>
                                            {{ $commitNotification->client->name }}
                                        </p>
                                        <p class="mb-1"><strong class="fw-semibold">Phone:</strong>
                                            {{ $commitNotification->client->phone ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong class="fw-semibold">Installment
                                                Amount:</strong>
                                            {{ number_format($installment->amount, 2) }}
                                        </p>
                                        <p class="mb-1"><strong class="fw-semibold">Salesman:</strong>
                                            {{ $commitNotification->agent->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-muted mb-0">
                                        <strong>Due Date:</strong>
                                        {{ \Carbon\Carbon::parse($installment->due_date)->format('M d, Y')
                                        }}
                                    </p>
                                    <a href="{{ route('electrical.client.commitment.show', $commitNotification->id) }}"
                                        class="btn btn-secondary btn-sm view-details" data-type="commitment"
                                        data-installment-id="{{ $installment->id }}"
                                        data-notification-id="{{ $commitNotification->id }}">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</main>
</div>
<style>
    .transition-all {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(90deg, #007bff, #0056b3);
    }

    .nav-pills .nav-link {
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    .card.notification-unviewed {
        background: lightgray;
    }
</style>

@endsection