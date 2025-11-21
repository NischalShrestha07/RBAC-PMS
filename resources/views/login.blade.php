@extends('layouts.auth-layout',['page'=>'Login'])
@section('content')

<div class="login-page">
    <div>
        @if (session('success'))
        <div class="alert alert-success text-white bg-success alert-dismissible custom-alert fade-in" role="alert">
            <strong style="color: white">Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger text-white bg-danger alert-dismissible custom-alert fade-in" role="alert">
            <strong style="color: white">Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="aphlert"
                aria-label="Close"></button>
        </div>
        @endif
    </div>

    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="login-container col-lg-5 col-md-7 col-sm-10 p-5 bg-white rounded-4 shadow-lg border-0"
                style="animation: fadeIn 0.4s ease;">

                {{-- Logo perfectly centered --}}
                <div class="text-center">
                    <img src="{{ asset('admincss/img/pms.ico') }}" alt="Logo" class="img-fluid"
                        style="width: 50px; height: 50px;">
                </div>

                <h1 class="text-center fw-bold fs-1 text-primary">Welcome Back</h1>
                <p class="text-center text-muted mb-2">Sign in to continue</p>

                <form action="{{ route('login.authenticate') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold fs-6">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control rounded-3 p-4 border-secondary-subtle" required
                            placeholder="Enter Your Email">
                        @error('email')
                        <span class="text-danger small">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold fs-6">Password</label>
                        <input id="password" type="password" name="password"
                            class="form-control rounded-3 p-4 border-secondary-subtle" required
                            placeholder="Enter Your Password">
                        @error('password')
                        <span class="text-danger small">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input pointer" type="checkbox" name="remember" id="remember" {{
                                old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="remember">
                                Remember Me
                            </label>
                        </div>

                    </div>
                    <div class="mt-4">
                        <button type="submit"
                            class="btn w-100 bg-primary text-white rounded-3 pointer fs-5 p-3 fw-bold">
                            Login
                        </button>
                    </div>
                </form>

            </div>
        </div>

</div>


@endsection