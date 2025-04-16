@props(['lines' => 4])

<div class="space-y-2 {{ $attributes->get('class') }}">
    @for($i = 0; $i < $lines; $i++)
        <div class="h-4 bg-[#D3EBE8] rounded animate-pulse {{ $i === $lines - 1 ? 'w-3/4' : 'w-full' }}"></div>
    @endfor
</div>