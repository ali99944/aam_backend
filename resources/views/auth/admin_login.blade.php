{{-- resources/views/admin/auth/login.blade.php --}}
@extends('layouts.admin_auth') {{-- Or a simpler layout for auth pages --}}

@section('title', 'Admin Login')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Admin Panel Login</h4>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">
                                Login
                            </button>
                        </div>

                        {{-- Optional: Forgot Password Link --}}
                        {{-- @if (Route::has('admin.password.request'))
                            <div class="text-center mt-3">
                                <a class="btn btn-link" href="{{ route('admin.password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        @endif --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection