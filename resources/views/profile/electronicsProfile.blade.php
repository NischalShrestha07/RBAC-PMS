@extends('layouts.electronicsApp',['page' => 'Profile'])

@section('content')
<div class="page-content">
    <div class="content-wrapper">

        <!-- Alerts Top-Right -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; min-width: 300px; max-width: 350px;">
            @if (session('success'))
            <div class="alert alert-success text-white bg-success alert-dismissible custom-alert  fade-in show mb-2"
                role="alert" style="white-space: normal; word-wrap: break-word;">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade-in show mb-2"
                role="alert" style="white-space: normal; word-wrap: break-word;">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>

        <!-- Content Header -->
        <section class="content-header">
            <div class="">
                <div class="row mb-2">
                    <h1 class="mb-0">User Profile</h1>
                    <p>Manage your account details</p>
                </div>
            </div>
        </section>

        <!-- Profile Content -->
        <section class="content">
            <div class="">
                <div class="row">

                    <!-- Profile Sidebar -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center p-4">
                                <img src="{{ $user->image ? asset('uploads/images/' . $user->image) : asset('admincss/dist/img/avatar.png') }}"
                                    alt="User image" class="img-fluid rounded-circle profile-img mb-3"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                <h5 class="card-title">{{ $user->name }}</h5>
                                <p class="text-muted">{{ ucfirst($user->role) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="col-md-9">
                        <div class="card border-0 shadow-lg">

                            <!-- Card Header -->
                            <div class="card-header text-white" style="background-color: #007bff;">
                                <h4 class="mb-0 text-center">Update Profile</h4>
                            </div>

                            <div class="mt-4">

                                <!-- Profile Update Card -->
                                <div class="card shadow-sm border-0 rounded-lg mb-4">
                                    <div class="card-body p-4">
                                        <form action="{{ route('electronics.profile.update') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="row g-3">
                                                <div class="col-md-6 mb-3">
                                                    <label for="name" class="form-label fw-semibold">Full Name</label>
                                                    <input type="text" class="form-control form-control-lg rounded-pill"
                                                        id="name" name="name" value="{{ $user->name }}" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label fw-semibold">Email</label>
                                                    <input type="email"
                                                        class="form-control form-control-lg rounded-pill" id="email"
                                                        name="email" value="{{ $user->email }}" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="phone" class="form-label fw-semibold">Phone</label>
                                                    <input type="text" class="form-control form-control-lg rounded-pill"
                                                        id="phone" name="phone" value="{{ $user->phone }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="image" class="form-label fw-semibold">Profile
                                                        Picture</label>
                                                    <input type="file" class="form-control rounded-pill" id="image"
                                                        name="image" accept="image/*">
                                                    <small class="text-muted">Upload JPG, PNG (max 2MB)</small>
                                                </div>
                                            </div>

                                            <div class="text-center mt-4">
                                                <button type="submit"
                                                    class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">Save
                                                    Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Change Password Card -->
                                <div class="card shadow-sm border-0 rounded-lg">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0 text-center">Change Password</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <form action="{{ route('electronics.change-password') }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-4">
                                                <label for="current_password" class="form-label">Current
                                                    Password</label>
                                                <input type="password" class="form-control form-control-lg"
                                                    id="current_password" name="current_password"
                                                    placeholder="Enter Current Password" required>
                                            </div>

                                            <div class="mb-4">
                                                <label for="new_password" class="form-label">New Password</label>
                                                <input type="password" class="form-control form-control-lg"
                                                    id="new_password" name="new_password"
                                                    placeholder="Enter New Password" required>
                                            </div>

                                            <div class="mb-4">
                                                <label for="new_password_confirmation" class="form-label">Confirm New
                                                    Password</label>
                                                <input type="password" class="form-control form-control-lg"
                                                    id="new_password_confirmation" name="new_password_confirmation"
                                                    placeholder="Enter Confirm New Password" required>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit"
                                                    class="btn btn-success px-4 py-2 rounded-pill shadow-sm">Save
                                                    Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('customJs')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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
        if ($('#sidebar').hasClass('active')) {
            $('.page-content').css('margin-left', '250px');
        } else {
            $('.page-content').css('margin-left', '0');
        }
    });
</script>
@endsection