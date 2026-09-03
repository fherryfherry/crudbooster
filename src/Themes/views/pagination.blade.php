<div>
    <div class="flex justify-end items-center space-x-4">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            <label for="perPage" class="mr-2">Items per page:</label>
            <select id="perPage" wire:model.live="perPage" class="border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none cursor-pointer">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        @if ($paginator->hasPages())
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-4 py-2 text-sm font-light text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 cursor-default rounded-md">
                    &laquo; Previous
                </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev"
                            class="px-4 py-2 text-sm font-light text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:text-gray-500 dark:hover:text-gray-400">
                        &laquo; Previous
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-4 py-2 text-sm font-light text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 cursor-default rounded-md">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-4 py-2 text-sm font-light text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 cursor-default rounded-md">{{ $page }}</span>
                            @else
                                <button wire:click="gotoPage({{ $page }})"
                                        class="px-4 py-2 text-sm font-light text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:text-gray-500 dark:hover:text-gray-400">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"
                            class="px-4 py-2 text-sm font-light text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:text-gray-500 dark:hover:text-gray-400">
                        Next &raquo;
                    </button>
                @else
                    <span class="px-4 py-2 text-sm font-light text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 cursor-default rounded-md">
                    Next &raquo;
                </span>
                @endif
            </nav>
        @endif
    </div>
</div>
