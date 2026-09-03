<div x-show="modalBulkActionConfirm"
     class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10 dark:bg-opacity-50 z-20">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-1/3">
        <h2 class="text-lg font-semibold border-b pb-3 dark:border-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                 class="size-6 inline-block align-middle">
                <path fill-rule="evenodd"
                      d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                      clip-rule="evenodd"/>
            </svg>
            <span class="align-middle dark:text-gray-200" x-text="bulkActionConfirmTitle">...</span>
        </h2>
        <div class="mt-3">
            <span class="dark:text-gray-200" x-text="bulkActionConfirmText">...</span>
            <p class="text-gray-500 dark:text-gray-400">({{count($selectedIds)}} records selected)</p>
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            <button type="button" x-on:click="$wire.bulkActionProcess(bulkActionId)" class="btn btn-danger"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed">Ok
            </button>
            <button type="button" @click="modalBulkActionConfirm = false" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed" class="btn btn-default dark:btn-dark">
                Cancel
            </button>
        </div>
    </div>
</div>
