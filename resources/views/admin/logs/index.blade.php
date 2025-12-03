@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Logs</h3>
                    <div class="card-tools">
                        <a href="{{ route('logs.stats') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Statistics
                        </a>
                        <a href="{{ route('logs.export') }}" class="btn btn-success btn-sm" id="exportBtn">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="user_id" class="form-control select2">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id')==$user->id ? 'selected' :
                                            '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Action</label>
                                    <select name="action" class="form-control">
                                        <option value="">All Actions</option>
                                        @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action')==$action ? 'selected' : ''
                                            }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Model</label>
                                    <select name="model_type" class="form-control">
                                        <option value="">All Models</option>
                                        @foreach($models as $model)
                                        <option value="{{ $model }}" {{ request('model_type')==$model ? 'selected' : ''
                                            }}>
                                            {{ class_basename($model) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status')==$status ? 'selected' : ''
                                            }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <div class="input-group">
                                        <input type="date" name="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                        <input type="date" name="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Search logs..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('logs.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td><code>{{ Str::limit($log->log_uid, 8) }}</code></td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        @if($log->user)
                                        <a href="{{ route('users.show', $log->user->id) }}">
                                            {{ $log->user->name }}
                                        </a>
                                        @else
                                        <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $log->action_color }}">
                                            {{ $log->formatted_action }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($log->description, 100) }}</td>
                                    <td><code>{{ $log->ip_address }}</code></td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $log->status == 'success' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('logs.show', $log->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No logs found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#exportBtn').click(function(e) {
            e.preventDefault();
            $('#exportModal').modal('show');
        });
    });
</script>
@endpush

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('logs.export') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Export Activity Logs</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
