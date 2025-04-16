@props([
    'type' => 'button', // button, submit
    'href' => null,
    'variant' => 'primary', // primary, secondary, danger, success, warning, info
    'size' => null, // sm, lg
    'icon' => null, // Optional icon class or SVG path
])

@php
    $baseClass = 'btn';
    $variantClass = 'btn-' . $variant;
    $sizeClass = $size ? 'btn-' . $size : '';
    $classes = trim("$baseClass $variantClass $sizeClass");
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <i class="{{ $icon }} btn-icon"></i> @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
         @if($icon) <i class="{{ $icon }} btn-icon"></i> @endif
        {{ $slot }}
    </button>
@endif

{{-- Optional: Add simple style for icon spacing if not handled globally --}}
@pushOnce('styles')
<style> .btn-icon { margin-left: 5px; } </style>
@endPushOnce