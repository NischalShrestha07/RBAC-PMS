@extends('layouts.auth-layout')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-lg-9 col-md-8 col-sm-10 p-5 bg-white rounded-4 shadow-lg border">
        <div class="text-center mb-4">
            <h1 class="fw-bold fs-1">Enable Two-Factor Authentication</h1>
            <p class="text-muted">Secure your account with 2FA</p>
        </div>

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="alert alert-info">
            <strong>Step 1:</strong> Scan the QR code with your authenticator app
        </div>

        <div class="text-center mb-4">
            <div class="mb-3">
                <!-- Display QR Code -->
                <div class="d-flex justify-content-center p-3 bg-light rounded-3 shadow-sm">
                    {!! $qrCode !!}
                </div>
            </div>
            <p class="text-muted small">Can't scan the code? Use this secret:</p>
            <div class="input-group mb-3">
                <input type="text" class="form-control text-center font-monospace" value="{{ $secret }}" readonly
                    id="secret-key">
                <button class="btn btn-outline-secondary" type="button" onclick="copySecret()">Copy</button>
            </div>
        </div>

        <div class="alert alert-warning">
            <strong>Step 2:</strong> Enter the 6-digit code from your authenticator app
        </div>

        <form action="{{ route('2fa.enable') }}" method="POST" class="mt-3">
            @csrf
            <div class="mb-4">
                <label for="code" class="form-label fw-semibold">Verification Code</label>
                <input type="text" name="code" id="code"
                    class="form-control form-control-lg text-center font-monospace shadow-sm rounded-3"
                    placeholder="000000" maxlength="6" required autofocus pattern="[0-9]{6}" title="Enter 6-digit code">
                @error('code')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm rounded-3">Enable 2FA</button>
                <a href="{{ route('dashboard.unified') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
            </div>
        </form>

        <div class="mt-4 text-center">
            <p class="small text-muted mb-0">
                <strong>Recommended Apps:</strong> Google Authenticator, Authy, Microsoft Authenticator
            </p>
        </div>
    </div>
</div>

<script>
    function copySecret() {
    const secretInput = document.getElementById('secret-key');
    secretInput.select();
    secretInput.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(secretInput.value);

    // Show temporary feedback
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = 'Copied!';
    button.classList.remove('btn-outline-primary');
    button.classList.add('btn-success');
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-primary');
    }, 2000);
}

// Auto-submit when 6 digits are entered
document.getElementById('code').addEventListener('input', function(e) {
this.value = this.value.replace(/[^0-9]/g, '');
if (this.value.length === 6) {
this.form.submit();
}
});
</script>
@endsection