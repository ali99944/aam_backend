@props(['count' => 4])

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 {{ $attributes->get('class') }}">
    @for($i = 0; $i < $count; $i++)
        <x-card-loader />
    @endfor
</div>