@props(['checked' => false])

<label class="inline-flex items-center">
    <input
        type="checkbox"
        {{ $attributes->merge(['class' => 'h-5 w-5 rounded-sm border border-gray-300 focus:ring-primary text-primary transition']) }}
        @if($checked) checked @endif
    >
    <span class="ml-2">{{ $slot }}</span>
</label>