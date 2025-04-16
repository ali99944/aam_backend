{{-- resources/views/auth/admin_login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - {{ config('app.name', 'Laravel') }}</title>
    {{-- Basic Styles (Tailwind or Bootstrap recommended) - Minimal inline styles for example --}}
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { background-color: white; padding: 2.5rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .login-card img { max-width: 150px; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.25rem; text-align: right; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9em; }
        .form-control { display: block; width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.375rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: #1abc9c; box-shadow: 0 0 0 2px rgba(26, 188, 156, 0.2); }
        .btn { display: inline-block; background-color: #1abc9c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.375rem; cursor: pointer; font-weight: 600; transition: background-color 0.2s; width: 100%; text-align: center; text-decoration: none;}
        .btn:hover { background-color: #16a085; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
        .links { margin-top: 1rem; font-size: 0.9em; }
        .links a { color: #1abc9c; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
         @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap');
    </style>
</head>
<body>
    <div class="login-card">
        <img src="{{ asset('images/logo_email.png') }}" alt="{{ config('app.name') }} Logo">
        <h2 style="margin-bottom: 1.5rem; font-weight: 600; color: #333;">Admin Panel Login</h2>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }} {{-- Display first error --}}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}"> {{-- Use default login route --}}
            @csrf

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
            </div>

             {{-- Remember Me (Optional) --}}
            {{-- <div class="form-group" style="text-align: right; display: flex; align-items: center;">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="width: auto; margin-left: 0.5rem;">
                <label for="remember" style="margin-bottom: 0;"> تذكرني </label>
            </div> --}}

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn">
                    تسجيل الدخول
                </button>
            </div>

             {{-- Forgot Password Link (Optional) --}}
             {{-- @if (Route::has('password.request'))
                <div class="links">
                    <a href="{{ route('password.request') }}">
                        هل نسيت كلمة المرور؟
                    </a>
                </div>
            @endif --}}
        </form>
    </div>
</body>
</html>