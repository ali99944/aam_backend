@props(['active' => false, 'hasChildren' => false])

<li x-data="{ open: @json($active) }">
    <a
        href="{{ $href }}"
        @if($hasChildren) @click.prevent="open = !open" @endif
        class="flex items-center justify-between px-4 py-3 text-sm font-medium transition-colors rounded-sm"
        :class="{
            'bg-[#D2EAE8] text-[#00998F]': {{ $active ? 'true' : 'false' }} || open,
            'text-gray-600 hover:bg-gray-100 hover:text-[#00998F]': !{{ $active ? 'true' : 'false' }} && !open
        }"
    >
        <div class="flex items-center space-x-3">
            @if($icon)
                <x-dynamic-component :component="$icon" class="w-5 h-5" />
            @endif
            <span>{{ $label }}</span>
        </div>

        @if($hasChildren)
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 transform transition-transform duration-200"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                :class="{ 'rotate-90': open }"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        @endif
    </a>

    @if($hasChildren)
        <ul
            x-show="open"
            x-collapse
            class="pl-4 mt-1 space-y-1"
        >
            {{ $slot }}
        </ul>
    @endif
</li>