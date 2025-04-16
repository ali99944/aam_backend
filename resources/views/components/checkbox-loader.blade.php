@props(['checked' => false])

<label class="inline-flex items-center">
    <input
        type="checkbox"
        {{ $attributes->merge(['class' => 'h-5 w-5 rounded-sm border border-gray-300 focus:ring-[#00998F] text-[#00998F] transition']) }}
        @if($checked) checked @endif
    >
    <span class="ml-2">{{ $slot }}</span>
</label>