@props([
    'title',
    'value',
    'icon' => null, // Lucide icon name
])

<div {{ $attributes->merge(['class' => 'stat-card']) }}>
    @if($icon)
        {{-- Use data-lucide attribute for JS initialization --}}
        <span class="stat-icon"><i data-lucide="{{ $icon }}"></i></span>
    @endif
    <h3>{{ $title }}</h3>
    <span class="stat-value">{{ $value }}</span>
    {{ $slot }} {{-- Allow adding extra content like comparison/links --}}
</div>