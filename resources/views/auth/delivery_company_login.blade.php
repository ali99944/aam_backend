{{-- resources/views/auth/delivery_company_login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delivery Portal Login - {{ config('app.name', 'Laravel') }}</title>
    {{-- Use same minimal style as admin login or link to a shared login CSS --}}
    <link rel="stylesheet" href="{{ asset('css/login_shared.css') }}"> {{-- Example shared CSS --}}
     <style> /* Or copy admin login styles */ </style>
</head>
<body>
    <div class="login-card">
        <img src="{{ asset('images/logo_email.png') }}" alt="{{ config('app.name') }} Logo">
        <h2 style="margin-bottom: 1.5rem; font-weight: 600; color: #333;">Delivery Company Portal</h2>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('email') ?: $errors->first('password') ?: 'Login failed.' }}
            </div>
        @endif

        {{-- Point form action to the delivery portal login route --}}
        <form method="POST" action="{{ route('delivery-portal.login') }}">
            @csrf

            <div class="form-group">
                <label for="email">البريد الإلكتروني للشركة</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn">
                    تسجيل الدخول
                </button>
            </div>

             {{-- Optional Forgot Password link for delivery companies --}}
            {{-- @if (Route::has('delivery-portal.password.request'))
                <div class="links">
                    <a href="{{ route('delivery-portal.password.request') }}">
                        هل نسيت كلمة المرور؟
                    </a>
                </div>
            @endif --}}
        </form>
    </div>
</body>
</html>