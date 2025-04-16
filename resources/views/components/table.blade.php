<div x-data="{
    searchQuery: '',
    sortColumn: '',
    sortDirection: 'asc',
    currentPage: 1,
    perPage: 10,

    get filteredData() {
        let data = {{ json_encode($data) }};

        // Apply search
        if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            data = data.filter(item => {
                return Object.values(item).some(value =>
                    String(value).toLowerCase().includes(query)
            });
        }

        // Apply sorting
        if (this.sortColumn) {
            data.sort((a, b) => {
                const valA = a[this.sortColumn];
                const valB = b[this.sortColumn];

                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
        }

        return data;
    },

    get paginatedData() {
        const start = (this.currentPage - 1) * this.perPage;
        return this.filteredData.slice(start, start + this.perPage);
    },

    get totalPages() {
        return Math.ceil(this.filteredData.length / this.perPage);
    },

    sort(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }
    },

    nextPage() {
        if (this.currentPage < this.totalPages) this.currentPage++;
    },

    prevPage() {
        if (this.currentPage > 1) this.currentPage--;
    }
}">
    @if($searchable)
        <div class="mb-4">
            <x-input
                x-model="searchQuery"
                placeholder="Search..."
                icon="heroicon-o-search"
                class="w-full md:w-1/3"
            />
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $key => $header)
                        <th
                            scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                            @if($sortable) @click="sort('{{ $key }}')" class="cursor-pointer" @endif
                        >
                            <div class="flex items-center justify-end">
                                {{ $header }}
                                @if($sortable)
                                    <span x-show="sortColumn === '{{ $key }}'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="ml-1 h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            :class="{ 'transform rotate-180': sortDirection === 'desc' }"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach

                    @if($actions)
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="filteredData.length === 0">
                    <tr>
                        <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                </template>

                <template x-for="(item, index) in paginatedData" :key="index">
                    <tr>
                        @foreach($headers as $key => $header)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span x-text="item.{{ $key }}"></span>
                            </td>
                        @endforeach

                        @if($actions)
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @foreach($actions as $action)
                                        @if($action['type'] === 'button')
                                            <x-button
                                                :variant="$action['variant'] ?? 'outline'"
                                                size="sm"
                                                @click="{{ $action['onClick'] }}(item)"
                                            >
                                                {{ $action['label'] }}
                                            </x-button>
                                        @elseif($action['type'] === 'link')
                                            <x-button
                                                :variant="$action['variant'] ?? 'outline'"
                                                size="sm"
                                                :href="`{{ $action['href'] }}/${item.id}`"
                                            >
                                                {{ $action['label'] }}
                                            </x-button>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Showing <span x-text="(currentPage - 1) * perPage + 1"></span> to
            <span x-text="Math.min(currentPage * perPage, filteredData.length)"></span> of
            <span x-text="filteredData.length"></span> results
        </div>

        <div class="flex space-x-2">
            <x-button
                @click="prevPage"
                :disabled="currentPage === 1"
                variant="outline"
                size="sm"
            >
                Previous
            </x-button>

            <x-button
                @click="nextPage"
                :disabled="currentPage >= totalPages"
                variant="outline"
                size="sm"
            >
                Next
            </x-button>
        </div>
    </div>
</div>