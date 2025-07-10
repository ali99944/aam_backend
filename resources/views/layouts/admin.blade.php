<!DOCTYPE html>
{{-- Determine language and direction based on user preference or default --}}
@php
    // Simple logic for now, replace with your actual localization logic
    $currentLang = session('locale', config('app.locale', 'ar')); // Default to Arabic
    $direction = ($currentLang == 'ar') ? 'rtl' : 'ltr';
@endphp
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- CSRF Token for AJAX requests if needed later --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AAM Store Admin')</title>

    {{-- Google Fonts (Optional - Choose nice ones) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Main Admin CSS --}}
    {{-- <link rel="stylesheet" href="{{ asset('app.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('app.css') }}" />

    {{-- Add page-specific CSS if needed --}}
    @stack('styles')

</head>
<body class="admin-body">

    <div class="admin-layout">
        {{-- Sidebar Component --}}
        <x-sidebar />

        {{-- Main Content Area (includes Navbar + Page Content) --}}
        <div class="admin-main-content">
            {{-- Navbar Component --}}
            <x-navbar />

            {{-- Page Specific Content --}}
            <main class="admin-content">
                 {{-- Flash Messages Container --}}
                 @if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
                 <div class="flash-messages">
                     @if (session('success'))
                         <div class="flash flash-success">{{ session('success') }}</div>
                     @endif
                     @if (session('error'))
                         <div class="flash flash-danger">{{ session('error') }}</div>
                     @endif
                      @if (session('warning'))
                         <div class="flash flash-warning">{{ session('warning') }}</div>
                     @endif
                      @if (session('info'))
                         <div class="flash flash-info">{{ session('info') }}</div>
                     @endif
                     {{-- Display Validation Errors --}}
                     @if ($errors->any())
                         <div class="flash flash-danger">
                             <ul style="margin: 0; padding-left: 15px;">
                                 @foreach ($errors->all() as $error)
                                     <li>{{ $error }}</li>
                                 @endforeach
                             </ul>
                         </div>
                     @endif
                 </div>
                 @endif

                @yield('content')
            </main>

            {{-- Footer (Optional - can be placed here or outside main-content) --}}
            <footer class="admin-footer">
                 {{-- Use dynamic year and potentially configurable app name --}}
                <p>© {{ date('Y') }} {{ config('app.name', 'AAM Store') }} - Admin Area.</p>
            </footer>
        </div>
    </div>

    {{-- Core JS (e.g., for sidebar toggle) --}}
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    {{-- Add page-specific JS if needed --}}
    @stack('scripts')

</body>
</html>