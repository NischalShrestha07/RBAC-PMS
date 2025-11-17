@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>User Dashboard</h2>
                    <p class="text-muted">Welcome, {{ Auth::user()->name }}</p>
                    <span class="badge bg-secondary">{{ $primaryRole }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- User-specific Stats -->
    <div class="row">
        @if(isset($userStats['my_commitments']))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                My Commitments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['my_commitments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-journal-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($userStats['my_pdcs']))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                My PDCs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['my_pdcs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Common actions for all users -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Available Actions</h6>
                </div>
                <div class="card-body">
                    <p>Based on your role (<strong>{{ $primaryRole }}</strong>), you have access to the following
                        features:</p>

                    <div class="row mt-3">
                        @foreach(Auth::user()->getAllPermissions() as $permission)
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>{{ $permission->name }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection