@extends('layouts.app', ['page' => 'Profile'])

@section('content')
<div class="">
    <section class="content-header mb-4">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="h2 fw-bold mb-1">Profile</h1>
                <p class="text-muted">Manage your account details</p>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb" class="float-sm-end">
                    <ol class="breadcrumb bg-transparent mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.unified') }}"
                                class="text-primary text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
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
    </section>

    <section class="content">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-md-3">
                <div class="card border-0 shadow-lg rounded-3">
                    <div class="card-body text-center p-4">
                        <img src="{{ $user->image ? asset('uploads/images/' . $user->image) : asset('admincss/dist/img/avatar.png') }}"
                            alt="User image" class="img-fluid rounded-circle mb-3"
                            style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="card-title fw-bold">{{ $user->name }}</h5>
                        <p class="text-muted mb-0">{{ ucfirst($user->role) }}</p>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Two-Factor Authentication</h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->google2fa_enabled)
                    <div class="alert alert-success">
                        <strong>2FA Enabled</strong> - Your account is protected with two-factor authentication.
                    </div>
                    <p>Enabled on: {{ auth()->user()->google2fa_enabled_at->format('M j, Y g:i A') }}</p>

                    <div class="d-flex gap-2">
                        <a href="{{ route('2fa.backup-codes') }}" class="btn btn-outline-primary">
                            View Backup Codes
                        </a>
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#disable2faModal">
                            Disable 2FA
                        </button>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <strong>2FA Not Enabled</strong> - Enhance your account security by enabling two-factor
                        authentication.
                    </div>
                    <a href="{{ route('2fa.setup') }}" class="btn btn-primary">
                        Enable Two-Factor Authentication
                    </a>
                    @endif
                </div>
            </div>

            <!-- Disable 2FA Modal -->
            <div class="modal fade" id="disable2faModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Disable Two-Factor Authentication</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('2fa.disable') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p>Are you sure you want to disable two-factor authentication? This will make your
                                    account less
                                    secure.</p>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Confirm Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning">Disable 2FA</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="col-md-9">
                <div class="card border-0 shadow-lg rounded-3">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 text-center fw-bold">Edit Profile</h4>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <!-- Profile Update Form -->
                        <div class="card border-0 rounded-3 mb-3">
                            <div class="card-body">
                                <h5 class="mb-2 fw-bold text-primary"><i class="fas fa-user-edit me-2"></i> Edit
                                    Profile</h5>
                                <form action="{{ route('profile.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label fw-bold">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $user->name }}" required>
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label fw-bold">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ $user->email }}" required>
                                            @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label fw-bold">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ $user->phone }}">
                                            @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="image" class="form-label fw-bold">Profile Picture</label>
                                            <input type="file" class="form-control" id="image" name="image"
                                                accept="image/*">
                                            <small class="text-muted">Upload JPG, PNG (max 2MB)</small>
                                            @error('image') <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary px-4 py-2">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change Password Form -->
                        <div class="card border-0 rounded-3">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0 text-center fw-bold">Change Password</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('change-password') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Current Password -->
                                    <div class="mb-4">
                                        <label for="current_password" class="form-label fw-bold">Current
                                            Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" placeholder="Enter Current Password" required>
                                            <span class="input-group-text bg-white"
                                                onclick="let input=document.getElementById('current_password');
                                                         let icon=this.querySelector('i');
                                                         if(input.type==='password'){input.type='text'; icon.classList.replace('fa-eye','fa-eye-slash');}
                                                         else {input.type='password'; icon.classList.replace('fa-eye-slash','fa-eye');}">
                                                <i class="fas fa-eye" style="cursor:pointer;"></i>
                                            </span>
                                        </div>
                                        @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- New Password -->
                                    <div class="mb-4">
                                        <label for="new_password" class="form-label fw-bold">New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" placeholder="Enter New Password" required>
                                            <span class="input-group-text bg-white"
                                                onclick="let input=document.getElementById('new_password');
                                                         let icon=this.querySelector('i');
                                                         if(input.type==='password'){input.type='text'; icon.classList.replace('fa-eye','fa-eye-slash');}
                                                         else {input.type='password'; icon.classList.replace('fa-eye-slash','fa-eye');}">
                                                <i class="fas fa-eye" style="cursor:pointer;"></i>
                                            </span>
                                        </div>
                                        @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="mb-4">
                                        <label for="new_password_confirmation" class="form-label fw-bold">Confirm New
                                            Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" placeholder="Confirm New Password"
                                                required>
                                            <span class="input-group-text bg-white"
                                                onclick="let input=document.getElementById('new_password_confirmation');
                                                         let icon=this.querySelector('i');
                                                         if(input.type==='password'){input.type='text'; icon.classList.replace('fa-eye','fa-eye-slash');}
                                                         else {input.type='password'; icon.classList.replace('fa-eye-slash','fa-eye');}">
                                                <i class="fas fa-eye" style="cursor:pointer;"></i>
                                            </span>
                                        </div>
                                        @error('new_password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-success px-4 py-2">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('customJs')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('.sidebar-toggle').on('click', function () {
            $('#sidebar').toggleClass('active');
            if ($('#sidebar').hasClass('active')) {
                $('.page-content').css('margin-left', '250px');
            } else {
                $('.page-content').css('margin-left', '0');
            }
        });
        // Initialize sidebar state
        if ($('#sidebar').hasClass('active')) {
            $('.page-content').css('margin-left', '250px');
        } else {
            $('.page-content').css('margin-left', '0');
        }
    });
</script>
@endsection