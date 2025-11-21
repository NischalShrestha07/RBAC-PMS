@extends('layouts.auth-layout')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-lg-5 col-md-7 col-sm-10 p-5 bg-light rounded-3 shadow-lg">
        <div class="text-center mb-4">
            <h1 class="fw-bold fs-1">Two-Factor Authentication</h1>
            <p class="text-muted">Enter the 6-digit code from your authenticator app</p>
        </div>

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('2fa.verify') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="code" class="form-label fw-semibold">Verification Code</label>
                <input type="text" name="code" id="code"
                    class="form-control form-control-lg text-center font-monospace fs-2" placeholder="000000"
                    maxlength="6" required autofocus pattern="[0-9]{6}" title="Enter 6-digit code">
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary btn-lg">Verify</button>
            </div>

            <div class="text-center">
                <a href="#" class="text-muted small" data-bs-toggle="modal" data-bs-target="#backupCodeModal">
                    Use backup code instead
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Backup Code Modal -->
<div class="modal fade" id="backupCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Use Backup Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('2fa.verify') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Enter one of your backup codes:</p>
                    <div class="mb-3">
                        <label for="backup_code" class="form-label">Backup Code</label>
                        <input type="text" name="code" id="backup_code" class="form-control font-monospace"
                            placeholder="xxxx-xxxx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length === 6) {
        this.form.submit();
    }
});
</script>
@endsection