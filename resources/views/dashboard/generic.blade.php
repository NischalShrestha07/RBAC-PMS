@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Dashboard</h2>
                    <p class="text-muted">Welcome to your dashboard, {{ Auth::user()->name }}</p>
                    <span class="badge bg-primary">{{ $primaryRole }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Basic Stats -->
    <div class="row">
        @can('view commitments')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Commitments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_commitments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-journal-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view pdcs')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active PDCs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_pdcs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view clients')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Clients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_clients'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view agents')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Agents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_agents'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-badge fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Quick Actions based on permissions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @can('create commitments')
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('commitments.create') }}" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle me-2"></i>New Commitment
                            </a>
                        </div>
                        @endcan

                        @can('create pdcs')
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('pdcs.create') }}" class="btn btn-success w-100">
                                <i class="bi bi-receipt me-2"></i>New PDC
                            </a>
                        </div>
                        @endcan

                        @can('view reports')
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-info w-100">
                                <i class="bi bi-graph-up me-2"></i>View Reports
                            </a>
                        </div>
                        @endcan

                        @can('view notifications')
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('notifications.index') }}" class="btn btn-warning w-100">
                                <i class="bi bi-bell me-2"></i>Notifications
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection