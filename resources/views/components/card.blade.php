<div {{ $attributes->merge(['class' => 'bg-white rounded ' .
    ($bordered ? 'border border-gray-200 ' : '') ]) }}>
    @if($header)
        <div class="border-b border-gray-200 p-4">
            {{ $header }}
        </div>
    @endif

    <div class="p-4">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="border-t border-gray-200 p-4">
            {{ $footer }}
        </div>
    @endif
</div>