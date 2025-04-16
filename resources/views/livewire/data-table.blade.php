<div class="p-6" x-data>
    <!-- Search and Controls -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <div class="w-full md:w-auto">
            <label for="table-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    id="table-search"
                    class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Search...">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="flex items-center">
                <label for="per-page" class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">Per Page:</label>
                <select
                    wire:model.live="perPage"
                    id="per-page"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto border border-gray-200 rounded-lg">
        {{-- <div wire:loading.flex class="absolute inset-0 bg-white bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-50 z-10 items-center justify-center">
            <div class="text-center">
                <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Loading...</p>
            </div>
        </div> --}}

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    @foreach($columns as $field => $column)
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center cursor-pointer" wire:click="sortBy('{{ $field }}')">
                                {{ $column['name'] }}
                                @if($sortField === $field)
                                    <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                    @if($sortDirection === 'asc')
                                        <span class="sr-only">Sorted ascending</span>
                                    @else
                                        <span class="sr-only">Sorted descending</span>
                                    @endif
                                @endif
                            </div>
                        </th>
                    @endforeach
                    @if(count($actions) > 0)
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        @foreach($columns as $field => $column)
                            <td class="px-6 py-4">
                                @php
                                    $value = $item;
                                    foreach(explode('.', $field) as $segment) {
                                        $value = $value->{$segment} ?? null;
                                    }
                                    echo $value;
                                @endphp
                            </td>
                        @endforeach

                        @if(count($actions) > 0)
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    @foreach($actions as $action)
                                        @if($action['type'] === 'delete')
                                            <button
                                                wire:click="confirmAction(@js($action), {{ $item->id }})"
                                                class="{{ $action['text_color'] }} hover:underline flex items-center"
                                            >
                                                <x-icon :name="$action['icon']" class="w-4 h-4 mr-1"/>
                                                {{-- {{ ucfirst($action['type']) }} --}}
                                            </button>
                                        @else
                                            <a
                                                href="{{ route($action['route'], $item->id) }}"
                                                class="{{ $action['text_color'] }} hover:underline flex items-center"
                                            >
                                                <x-icon :name="$action['icon']" class="w-4 h-4 mr-1"/>
                                                {{-- {{ ucfirst($action['type']) }} --}}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No records found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>

    <!-- Confirmation Modal -->
    @if($confirmingAction)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Confirm Action</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">
                    Are you sure you want to {{ $actionToConfirm['type'] }} this record?
                </p>
                <div class="mt-4 flex justify-end space-x-3">
                    <button
                        wire:click="$set('confirmingAction', null)"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        wire:click="performAction({{ $confirmingAction }})"
                        class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700"
                    >
                        Confirm {{ ucfirst($actionToConfirm['type']) }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>