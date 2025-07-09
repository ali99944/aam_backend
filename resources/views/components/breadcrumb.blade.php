<nav class="flex {{ $className }}">
    <ol class="flex items-center">
        @foreach($items as $index => $item)
            @php
                $isLast = $loop->last;
                $icon = $item['icon'] ?? null;
                $iconPosition = $item['iconPosition'] ?? 'left';
            @endphp

            <li class="flex items-center">
                @isset($item['href'])
                    @if(!$isLast)
                        <a
                            href="{{ $item['href'] }}"
                            class="text-sm text-gray-600 hover:text-primary flex items-center gap-1"
                        >
                            @if($icon && $iconPosition === 'left')
                                <x-dynamic-component :component="$icon" class="w-4 h-4 text-gray-500" />
                            @endif
                            {{ $item['label'] }}
                            @if($icon && $iconPosition === 'right')
                                <x-dynamic-component :component="$icon" class="w-4 h-4 text-gray-500" />
                            @endif
                        </a>
                    @else
                        <span class="text-sm font-medium text-gray-900 flex items-center gap-1">
                            @if($icon && $iconPosition === 'left')
                                <x-dynamic-component :component="$icon" class="w-4 h-4 text-gray-900" />
                            @endif
                            {{ $item['label'] }}
                            @if($icon && $iconPosition === 'right')
                                <x-dynamic-component :component="$icon" class="w-4 h-4 text-gray-900" />
                            @endif
                        </span>
                    @endif
                @else
                    <span class="text-sm font-medium text-gray-900 flex items-center gap-1">
                        @if($icon && $iconPosition === 'left')
                            <x-dynamic-component :component="$icon" class="w-4 h-4 {{ $isLast ? 'text-gray-900' : 'text-gray-500' }}" />
                        @endif
                        {{ $item['label'] }}
                        @if($icon && $iconPosition === 'right')
                            <x-dynamic-component :component="$icon" class="w-4 h-4 {{ $isLast ? 'text-gray-900' : 'text-gray-500' }}" />
                        @endif
                    </span>
                @endisset

                @if(!$isLast)
                    {!! $separator !!}
                @endif
            </li>
        @endforeach
    </ol>
</nav>