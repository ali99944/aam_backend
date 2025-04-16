<!DOCTYPE html>
{{-- Basic Language/Direction (adapt if needed) --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Delivery Company Portal') - {{ config('app.name', 'AAM Store') }}</title>

    {{-- Include your compiled CSS (can share admin.css or create separate) --}}
    <link rel="stylesheet" href="{{ asset('app.css') }}"> {{-- Or company.css --}}
    @stack('styles')
</head>
<body class="admin-body"> {{-- Use same base class? --}}

    <div class="admin-layout"> {{-- Re-use layout structure? --}}
        {{-- Company Sidebar --}}
        <x-delivery-company.sidebar />

        <div class="admin-main-content"> {{-- Re-use main content class --}}
            {{-- Company Navbar --}}
            <x-delivery-company.navbar />

            <main class="admin-content">
                {{-- Flash Messages --}}
                 @if (session('success') || session('error') || $errors->any())
                 <div class="flash-messages">
                     @if (session('success')) <div class="flash flash-success">{{ session('success') }}</div> @endif
                     @if (session('error')) <div class="flash flash-danger">{{ session('error') }}</div> @endif
                     @if ($errors->any())
                         <div class="flash flash-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                     @endif
                 </div>
                 @endif

                @yield('content')
            </main>

            <footer class="admin-footer">
                <p>© {{ date('Y') }} {{ config('app.name', 'AAM Store') }} - Delivery Portal.</p>
            </footer>
        </div>
    </div>

    {{-- Core JS (e.g., for sidebar toggle if using same structure) --}}
    <script src="{{ asset('admin.js') }}"></script> {{-- Or company.js --}}
    @stack('scripts')
</body>
</html>