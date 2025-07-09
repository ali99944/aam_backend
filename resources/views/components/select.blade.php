<div
    x-data="{
        isOpen: false,
        searchTerm: '',
        selectedOption: @js(collect($options)->firstWhere('value', $value)),
        filteredOptions: @js($options),

        init() {
            this.filterOptions();
        },

        filterOptions() {
            if (this.searchTerm === '') {
                this.filteredOptions = @js($options);
            } else {
                this.filteredOptions = @js($options).filter(option =>
                    option.label.toLowerCase().includes(this.searchTerm.toLowerCase())
                );
            }
        }
    }"
    x-on:click.away="isOpen = false"
    class="w-full"
>
    @if($label)
        <label class="block text-base font-medium mb-1 text-right">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <button
            type="button"
            x-on:click="isOpen = !isOpen"
            class="w-full bg-white border border-gray-200 rounded py-1.5 px-4 text-base text-right flex items-center justify-between focus:outline-none transition-colors
                {{ $error ? 'border-red-500' : '' }}
                {{ $disabled ? 'bg-gray-100 cursor-not-allowed' : '' }}"
            {{ $disabled ? 'disabled' : '' }}
        >
            <span x-text="selectedOption ? selectedOption.label : @js($placeholder)"
                  :class="{'text-gray-400': !selectedOption}"></span>
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 transform duration-300 text-gray-400"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 :class="{'rotate-180': isOpen}">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <input type="hidden" name="{{ $name }}" x-bind:value="selectedOption ? selectedOption.value : ''">

        <div
            x-show="isOpen"
            x-transition
            class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-sm shadow-md max-h-60 overflow-auto"
        >
            @if($searchable)
                <div class="sticky top-0 p-2 bg-white border-b border-gray-100">
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="بحث..."
                            class="w-full border border-gray-200 rounded-sm py-1.5 px-3 pr-8 text-sm focus:outline-none"
                            x-model="searchTerm"
                            x-on:input.debounce.300ms="filterOptions()"
                            x-on:click.stop
                        />
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="absolute right-2 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button
                            x-show="searchTerm"
                            x-on:click="searchTerm = ''; filterOptions()"
                            x-on:click.stop
                            class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <ul class="p-1">
                <template x-if="filteredOptions.length > 0">
                    <template x-for="option in filteredOptions" :key="option.value">
                        <li>
                            <button
                                type="button"
                                class="w-full text-right px-4 py-2 text-base hover:bg-secondary flex items-center justify-between"
                                :class="{'bg-secondary text-primary': option.value === selectedOption?.value}"
                                x-on:click="selectedOption = option; isOpen = false; searchTerm = ''"
                            >
                                <template x-if="option.value === selectedOption?.value">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                <span x-text="option.label"></span>
                            </button>
                        </li>
                    </template>
                </template>
                <template x-if="filteredOptions.length === 0">
                    <li class="px-4 py-2 text-sm text-gray-500 text-center">لا توجد نتائج</li>
                </template>
            </ul>
        </div>
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-500 text-right">{{ $error }}</p>
    @endif
</div>