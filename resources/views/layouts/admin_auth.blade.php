{{-- resources/views/layouts/admin_auth.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Admin Area') - {{ config('app.name', 'AAM Store') }}</title>
        <link rel="stylesheet" href="/app.css" />

        @stack('styles')
        <style>
            body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        </style>
    </head>

    <body>
        @yield('content')
    </body>
</html>