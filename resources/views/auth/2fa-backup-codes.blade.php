@extends('layouts.app',['page'=>'2FA BackupCodes'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Backup Codes</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Important:</strong> Store these backup codes in a secure place.
                        Each code can be used only once.
                    </div>

                    <div class="bg-light p-4 rounded mb-4">
                        <div class="row text-center fw-bold font-monospace">
                            @foreach($backupCodes as $code)
                            <div class="col-md-6 mb-2">{{ $code }}</div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('2fa.download-backup-codes') }}" class="btn btn-primary">
                            Download Codes
                        </a>
                        <form action="{{ route('2fa.regenerate-backup-codes') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning"
                                onclick="return confirm('Are you sure? This will invalidate all existing backup codes.')">
                                Regenerate Codes
                            </button>
                        </form>
                        <a href="{{ route('dashboard.unified') }}" class="btn btn-secondary ms-auto">
                            Continue to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection