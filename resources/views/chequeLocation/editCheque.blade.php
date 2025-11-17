@extends('layouts.superApp', ['page' => 'Edit Cheque Location'])

@section('content')
<div class="card border-0 shadow-lg rounded-3">
    <!-- Header Section -->
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 px-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Edit Cheque Location</h1>
            <nav aria-label="breadcrumb mb-0">
                <ol class="breadcrumb bg-transparent mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.unified') }}"
                            class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cheque.locations.index') }}"
                            class="text-decoration-none">Cheque Location</a></li>
                    <li class="breadcrumb-item">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('cheque.locations.index') }}" class="btn btn-secondary">Back
        </a>
        <!-- Alerts Top-Right -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
            @if (session('success'))
            <div class="alert alert-success text-white bg-success alert-dismissible custom-alert  fade-in" role="alert"
                style="white-space: normal; word-wrap: break-word;">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade-in" role="alert"
                style="white-space: normal; word-wrap: break-word;">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
            @endif
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card-body p-3 p-md-4    ">
        <form action="{{ route('cheque.locations.update', $cheque->id) }}" method="POST" id="editChequeForm">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6 col-12">
                    <div>
                        <label for="cheque_no" class="form-label fw-bold">Cheque Number</label>
                        <input type="text" class="form-control @error('cheque_no') is-invalid @enderror" id="cheque_no"
                            name="cheque_no" value="{{ old('cheque_no', $cheque->cheque_no) }}" required
                            placeholder="e.g., CHQ123456">
                        @error('cheque_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div>
                        <label for="amount" class="form-label fw-bold">Amount</label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror"
                            id="amount" name="amount" value="{{ old('amount', $cheque->amount) }}" required
                            placeholder="e.g., 1500.00">
                        @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div>
                        <label for="client_id" class="form-label fw-bold">Party Name</label>
                        <select class="form-select @error('client_id') is-invalid @enderror" id="client_id"
                            name="client_id" required>
                            <option value="" disabled>Select Party</option>
                            @foreach ($clients as $client)
                            <option value="{{ $client->id }}" {{ $cheque->client_id == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div>
                        <label for="agent_id" class="form-label fw-bold">Salesman</label>
                        <select class="form-select @error('agent_id') is-invalid @enderror" id="agent_id"
                            name="agent_id" required>
                            <option value="" disabled>Select Salesman</option>
                            @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $cheque->agent_id == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('agent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div>
                        <label for="location" class="form-label fw-bold">Location</label>
                        <select class="form-select @error('location') is-invalid @enderror" id="location"
                            name="location" required>
                            <option value="" disabled>Select Location</option>
                            <option value="In Ktm Branch" {{ $cheque->location == 'In Ktm Branch' ? 'selected' : ''
                                }}>In Ktm Branch</option>
                            <option value="With Salesman" {{ $cheque->location == 'With Salesman' ? 'selected' : ''
                                }}>With Salesman</option>
                            <option value="Received but Not Cleared" {{ $cheque->location == 'Received but Not Cleared'
                                ? 'selected' : '' }}>Received but Not Cleared</option>
                            <option value="Received & Cleared" {{ $cheque->location == 'Received & Cleared' ? 'selected'
                                : '' }}>Received & Cleared</option>
                        </select>
                        @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div>
                        <label for="details" class="form-label fw-bold">Status</label>
                        <select class="form-select @error('details') is-invalid @enderror" id="details" name="details"
                            required>
                            <option value="" disabled>Select Status</option>
                            <option value="Pending" {{ $cheque->details == 'Pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="Processed" {{ $cheque->details == 'Processed' ? 'selected' : '' }}>Processed
                            </option>
                            <option value="Collected" {{ $cheque->details == 'Collected' ? 'selected' : '' }}>Collected
                            </option>
                        </select>
                        @error('details')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div>
                        <label for="remarks" class="form-label fw-bold text-muted">Remarks</label>
                        <input type="text" class="form-control rounded p-3" value="{{old('remarks',$cheque->remarks)}}"
                            id="remarks" name="remarks" required>
                        @error('remarks') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    @error('remarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('cheque.locations.index') }}" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-success" id="editChequeSaveButton">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection