@extends('layouts.app')

@section('title', 'Activity Logs Statistics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Logs Statistics</h3>
                    <div class="card-tools">
                        <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Logs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-history"></i></span>
                                <div class="info-box-content">`
                                    <span class="info-box-text">Total Logs</span>
                                    <span class="info-box-number">{{ number_format($stats['total_logs']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-calendar-day"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Today's Logs</span>
                                    <span class="info-box-number">{{ number_format($stats['today_logs']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Users</span>
                                    <span class="info-box-number">{{ number_format($stats['top_users']->count())
                                        }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Failed Actions</span>
                                    <span class="info-box-number">
                                        {{ number_format(\App\Models\ActivityLog::where('status', 'failed')->count()) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Top Users by Activity</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>User</th>
                                                    <th>Total Activities</th>
                                                    <th>Last Activity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['top_users'] as $index => $user)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if($user->user)
                                                        {{ $user->user->name }}
                                                        <br><small>{{ $user->user->email }}</small>
                                                        @else
                                                        <span class="text-muted">Unknown User</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($user->total) }}</td>
                                                    <td>
                                                        @if($user->user)
                                                        {{ \App\Models\ActivityLog::where('user_id',
                                                        $user->user_id)->latest()->first()->created_at->diffForHumans()
                                                        }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Actions Distribution</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="actionsChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Models Distribution</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="modelsChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Activities</h3>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        @foreach($stats['recent_activities'] as $activity)
                                        <div class="time-label">
                                            <span class="bg-info">{{ $activity->created_at->format('M d') }}</span>
                                        </div>
                                        <div>
                                            <i class="fas fa-user bg-blue"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fas fa-clock"></i> {{
                                                    $activity->created_at->format('H:i') }}</span>
                                                <h3 class="timeline-header">
                                                    @if($activity->user)
                                                    <a href="#">{{ $activity->user->name }}</a>
                                                    @else
                                                    System
                                                    @endif
                                                    <span
                                                        class="badge badge-{{ $activity->status == 'success' ? 'success' : ($activity->status == 'failed' ? 'danger' : 'warning') }}">
                                                        {{ $activity->formatted_action }}
                                                    </span>
                                                </h3>
                                                <div class="timeline-body">
                                                    {{ $activity->description }}
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        <div>
                                            <i class="fas fa-clock bg-gray"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Actions Chart
    var actionsCtx = document.getElementById('actionsChart').getContext('2d');
    var actionsChart = new Chart(actionsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($stats['actions_distribution']->pluck('action')) !!},
            datasets: [{
                data: {!! json_encode($stats['actions_distribution']->pluck('total')) !!},
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#8AC926', '#1982C4',
                    '#6A4C93', '#F15BB5'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Models Chart
    var modelsCtx = document.getElementById('modelsChart').getContext('2d');
    var modelsChart = new Chart(modelsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($stats['models_distribution']->map(function($item) {
                return class_basename($item->model_type);
            })) !!},
            datasets: [{
                label: 'Activities',
                data: {!! json_encode($stats['models_distribution']->pluck('total')) !!},
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
