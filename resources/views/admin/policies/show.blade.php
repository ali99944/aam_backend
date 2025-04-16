<!DOCTYPE html>
{{-- Determine language and direction based on your app's logic --}}
@php
    $currentLang = session('locale', config('app.locale', 'ar'));
    $direction = ($currentLang == 'ar') ? 'rtl' : 'ltr';
@endphp
<html lang="{{ str_replace('_', '-', $currentLang) }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $policy->name }}</title>
    {{-- Link to your main frontend CSS or a basic style for preview --}}
    {{-- Option 1: Frontend CSS --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

    {{-- Option 2: Basic Preview Styles --}}
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; max-width: 900px; margin: auto; }
        h1, h2, h3 { margin-top: 1.5em; margin-bottom: 0.5em; }
        p { margin-bottom: 1em; }
        ul, ol { margin-bottom: 1em; padding-left: 2em; }
        a { color: #0d6efd; }
        img { max-width: 100%; height: auto; }
        /* Add more styles as needed to mimic frontend */
    </style>
</head>
<body>
    <h1>{{ $policy->name }}</h1>
    <hr>
    {{-- Render the HTML content directly --}}
    {{-- WARNING: Ensure content is sanitized on save if it comes from untrusted sources --}}
    {{-- If you used DOMPurifier or similar on save, this is safer. --}}
    <div>
        {!! $policy->content !!}
    </div>

    <hr style="margin-top: 30px;">
    <p><small>Last Updated: {{ $policy->updated_at->format('d F Y') }}</small></p>
    <p><small>Preview generated: {{ now()->format('d M Y, H:i:s') }}</small></p>
    <p><a href="{{ route('admin.policies.edit', $policy->id) }}" style="display: inline-block; margin-top: 20px; padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;">← Back to Edit</a></p>

</body>
</html>