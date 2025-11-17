@extends('layouts.app',['page'=>'Add ChequeLocation'])

@section('content')
<section class="content-header d-flex gap-3 justify-content-between">
    <div class="d-flex align-items-center breadcrumb mb-0">
        <li class="breadcrumb-item"><a class="link-primary fw-bold text-decoration-none"
                href="{{ route('dashboard.unified') }}">Home</a></li>
        <li class="breadcrumb-item active">Add New Cheque Location</li>
    </div>
    <div class="d-flex">
        <a href="{{ route('cheque.locations.index') }}" class="btn btn-secondary fw-semibold">
            Back
        </a>
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


<script>
    document.addEventListener('DOMContentLoaded', function () {
                // Auto-dismiss success alert after 5 seconds
                const successAlert = document.getElementById('successAlert');
                if (successAlert) {
                    setTimeout(() => {
                        successAlert.classList.remove('show');
                        successAlert.classList.add('fade');
                        setTimeout(() => successAlert.remove(), 300); // Remove from DOM after fade out
                    }, 4000); // 5 seconds
                }

                // Auto-dismiss error alert after 5 seconds
                const errorAlert = document.getElementById('errorAlert');
                if (errorAlert) {
                    setTimeout(() => {
                        errorAlert.classList.remove('show');
                        errorAlert.classList.add('fade');
                        setTimeout(() => errorAlert.remove(), 300); // Remove from DOM after fade out
                    }, 4000); // 5 seconds
                }
            });
</script>


<div class="card mt-3 shadow-sm">
    <div class="card-body">
        <h5 class="card-title text-muted fw-bold mb-4">Add New Cheque Location</h5>
        <form action="{{ route('cheque.locations.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="client_id" class="form-label fw-bold text-muted">Party Name</label>
                    <select class="form-select rounded shadow-sm" id="client_id" name="client_id" required>
                        <option value="">Select a Party</option>
                        @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="cheque_no" class="form-label fw-bold text-muted">Cheque No</label>
                    <input type="text" class="form-control rounded shadow-sm" id="cheque_no" name="cheque_no"
                        placeholder="Enter Cheque Number" required>
                    @error('cheque_no')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-bold text-muted">Amount</label>
                    <input type="number" class="form-control rounded shadow-sm" id="amount" name="amount"
                        placeholder="e.g., 5000.00" min="0" step="0.01" required>
                    @error('amount')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="agent_id" class="form-label fw-bold text-muted">Salesman</label>
                    <select class="form-select rounded shadow-sm" id="agent_id" name="agent_id" required>
                        <option value="">Select a Salesman</option>
                        @foreach ($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    @error('agent_id')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="location" class="form-label fw-bold text-muted">Cheque Location</label>
                    <select class="form-select rounded shadow-sm" id="location" name="location" required>
                        <option value="Ktm Branch">Ktm Branch</option>
                        <option value="Salesman">Salesman</option>
                        <option value="Received but Not Cleared">Received but Not Cleared</option>
                        <option value="Received & Cleared">Received & Cleared</option>
                    </select>
                    @error('location')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="details" class="form-label fw-bold text-muted">Status</label>
                    <select class="form-select rounded shadow-sm" id="details" name="details" required>
                        <option value="Pending">Pending</option>
                        <option value="Processed">Processed</option> <!-- Fixed typo: "Proccessed" to "Processed" -->
                        <option value="Collected">Collected</option>
                    </select>
                    @error('details')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-12">
                    <label for="remarks" class="form-label fw-bold text-muted">Remarks</label>
                    <textarea class="form-control rounded shadow-sm" id="remarks" name="remarks"
                        placeholder="Add any remarks (optional)" rows="4"></textarea>
                    @error('remarks')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('cheque.locations.index') }}" class="btn btn-danger fw-bold px-4 py-2">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
                <button type="submit" class="btn btn-success fw-bold px-4 py-2">
                    <i class="fas fa-save me-2"></i> Save Cheque
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
