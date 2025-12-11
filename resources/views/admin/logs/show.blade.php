@extends('layouts.app',['page'=>'Log Details'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Log Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Log ID:</th>
                                    <td><code>{{ $log->log_uid }}</code></td>
                                </tr>
                                <tr>
                                    <th>Timestamp:</th>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>User:</th>
                                    <td>
                                        @if($log->user)
                                        <a href="{{ route('users.show', $log->user->id) }}">
                                            {{ $log->user->name }} ({{ $log->user->email }})
                                        </a>
                                        @else
                                        <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Action:</th>
                                    <td>
                                        <span class="badge text-muted badge-{{ $log->action_color }}">
                                            {{ $log->formatted_action }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Model:</th>
                                    <td>
                                        @if($log->model_type && $log->model)
                                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span
                                            class="badge bg-{{ $log->status == 'success' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Description</h4>
                                </div>
                                <div class="card-body">
                                    <p>{{ $log->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($log->changes && count($log->changes) > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Changes Made</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Old Value</th>
                                                <th>New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($log->changes as $field => $change)
                                            <tr>
                                                <td><strong>{{ $field }}</strong></td>
                                                <td>
                                                    <span class="text-danger">
                                                        {{ is_array($change['old']) ? json_encode($change['old']) :
                                                        $change['old'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-success">
                                                        {{ is_array($change['new']) ? json_encode($change['new']) :
                                                        $change['new'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($log->request_data && count($log->request_data) > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Request Data</h4>
                                </div>
                                <div class="card-body">
                                    <pre
                                        class="bg-light p-3"><code>{{ json_encode($log->request_data, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection