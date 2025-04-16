<div class="flex flex-col items-center justify-center text-center {{ $variantClasses()[$variant] }} {{ $attributes->get('class') }}">
    @if($illustration)
        <div class="mb-4 {{ $illustrationSizeClasses()[$illustrationSize] }}">
            {{ $illustration }}
        </div>
    @elseif($icon)
        <div class="text-[#00998F] bg-[#D2EAE8] mb-4 flex items-center justify-center w-20 h-20 rounded-full">
            <x-dynamic-component :component="$icon" class="w-12 h-12" />
        </div>
    @else
        <div class="text-[#00998F] bg-[#D2EAE8] mb-4 flex items-center justify-center w-20 h-20 rounded-full">
            <x-dynamic-component :component="$defaultIcon" class="w-12 h-12" />
        </div>
    @endif

    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $title }}</h3>

    @if($description)
        <p class="text-base text-gray-500 max-w-md mb-4">{{ $description }}</p>
    @endif

    @if($actions)
        <div class="flex flex-wrap gap-3 justify-center">
            @isset($actions['primary'])
                <x-button
                    :variant="$actions['primary']['variant'] ?? 'primary'"
                    :href="$actions['primary']['href'] ?? null"
                    :onClick="$actions['primary']['onClick'] ?? null"
                >
                    {{ $actions['primary']['label'] }}
                </x-button>
            @endisset

            @isset($actions['secondary'])
                <x-button
                    :variant="$actions['secondary']['variant'] ?? 'outline'"
                    :href="$actions['secondary']['href'] ?? null"
                    :onClick="$actions['secondary']['onClick'] ?? null"
                >
                    {{ $actions['secondary']['label'] }}
                </x-button>
            @endisset
        </div>
    @endif
</div>