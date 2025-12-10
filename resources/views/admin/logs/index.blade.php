@extends('layouts.app',['page'=>'Activity Logs'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0 text-primary">
                                <i class="fas fa-history mr-2"></i>Activity Logs
                            </h3>
                            <p class="text-muted mb-0 small">Track all system activities and user actions</p>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('logs.stats') }}" class="btn btn-info text-white">
                                <i class="fas fa-chart-pie mr-1"></i> Statistics
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="card mb-4 border-light">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-filter mr-2"></i>Filters
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="filter-form">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1">User</label>
                                        <select name="user_id" class="form-control select2">
                                            <option value="">All Users</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id')==$user->id ?
                                                'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1">Action</label>
                                        <select name="action" class="form-control">
                                            <option value="">All Actions</option>
                                            @foreach($actions as $action)
                                            <option value="{{ $action }}" {{ request('action')==$action ? 'selected'
                                                : '' }}>
                                                {{ ucfirst($action) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1">Model</label>
                                        <select name="model_type" class="form-control">
                                            <option value="">All Models</option>
                                            @foreach($models as $model)
                                            <option value="{{ $model }}" {{ request('model_type')==$model ? 'selected'
                                                : '' }}>
                                                {{ class_basename($model) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status')==$status ? 'selected'
                                                : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1">Date Range</label>
                                        <div class="input-group input-group-sm">
                                            <input type="date" name="date_from" class="form-control border-end-0"
                                                value="{{ request('date_from') }}" placeholder="From">
                                            <span
                                                class="input-group-text bg-transparent border-start-0 border-end-0">to</span>
                                            <input type="date" name="date_to" class="form-control border-start-0"
                                                value="{{ request('date_to') }}" placeholder="To">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Search Keywords</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search in logs..." value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-end gap-2 mt-3">
                                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                                <i class="fas fa-filter mr-1"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm px-4">
                                                <i class="fas fa-redo mr-1"></i> Clear
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted small">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }}
                                of {{ $logs->total() }} records</span>
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            {{ $logs->total() }} total logs
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div class="table-responsive border rounded">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">ID</th>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">Time</th>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">User</th>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">Action</th>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">Description</th>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">IP Address</th>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">Status</th>
                                    <th class="py-3 px-3 border-bottom text-muted small text-uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr class="align-middle">
                                    <td class="px-3 py-2">
                                        <code
                                            class="bg-light px-2 py-1 rounded small">{{ Str::limit($log->log_uid, 8) }}</code>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="small">{{ $log->created_at->format('Y-m-d') }}</div>
                                        <div class="text-muted smaller">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary small"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('users.show', $log->user->id) }}"
                                                    class="text-decoration-none">
                                                    {{ $log->user->name }}
                                                </a>
                                                <div class="text-muted small">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar-sm bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-cog text-secondary small"></i>
                                            </div>
                                            <span class="text-muted">System</span>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                        $actionColors = [
                                        'create' => 'success',
                                        'update' => 'info',
                                        'delete' => 'danger',
                                        'login' => 'primary',
                                        'logout' => 'secondary',
                                        'read' => 'warning',
                                        ];
                                        $color = $actionColors[$log->action] ?? 'dark';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25 rounded-pill px-3 py-1">
                                            <i
                                                class="fas fa-{{ $log->action == 'login' ? 'sign-in-alt' : ($log->action == 'logout' ? 'sign-out-alt' : $log->action) }} mr-1"></i>
                                            {{ $log->formatted_action }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="small text-truncate" style="max-width: 200px;"
                                            title="{{ $log->description }}">
                                            {{ $log->description }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <code class="bg-light px-2 py-1 rounded small">{{ $log->ip_address }}</code>
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                        $statusColors = [
                                        'success' => ['bg' => 'success', 'icon' => 'check-circle'],
                                        'failed' => ['bg' => 'danger', 'icon' => 'times-circle'],
                                        'warning' => ['bg' => 'warning', 'icon' => 'exclamation-circle'],
                                        'info' => ['bg' => 'info', 'icon' => 'info-circle'],
                                        ];
                                        $status = $statusColors[$log->status] ?? $statusColors['info'];
                                        @endphp
                                        <span
                                            class="badge bg-{{ $status['bg'] }} bg-opacity-10 text-{{ $status['bg'] }} border border-{{ $status['bg'] }} border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="fas fa-{{ $status['icon'] }} mr-1"></i>
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <a href="{{ route('logs.show', $log->id) }}"
                                            class="btn btn-outline-primary btn-sm btn-icon" title="View Details"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-3"></i>
                                            <p class="mb-0">No logs found</p>
                                            <small class="text-muted">Try adjusting your filters</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
                        </div>
                        <nav aria-label="Logs pagination">
                            <ul class="pagination pagination-sm mb-0">
                                {{ $logs->links('pagination::bootstrap-4') }}
                            </ul>
                        </nav>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Select user...',
            width: '100%'
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Auto focus search on page load
        $('input[name="search"]').focus();

        // Date range validation
        $('input[name="date_to"]').on('change', function() {
            const fromDate = $('input[name="date_from"]').val();
            const toDate = $(this).val();

            if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                alert('End date cannot be earlier than start date');
                $(this).val('');
            }
        });
    });
</script>
@endpush