<div class="w-full">
    @if($label)
        <label class="block text-base font-medium mb-1 text-right">
            {{ $label }}
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        class="w-full bg-white border border-gray-200 outline-none rounded-sm py-2 px-4 text-base focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-colors resize-vertical
            {{ $error ? 'border-red-500' : '' }}"
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>

    @if($error)
        <p class="mt-1 text-sm text-red-500 text-right">{{ $error }}</p>
    @endif
</div>