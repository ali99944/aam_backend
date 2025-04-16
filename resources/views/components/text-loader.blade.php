@props([
    'width' => 'w-1/3'
])

<div class="h-4 bg-[#D3EBE8] rounded animate-pulse {{ $width }} {{ $attributes->get('class') }}"></div>