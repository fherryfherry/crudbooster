<div x-show="modalImport"
     class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10 dark:bg-opacity-50 z-20">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 xs:w-full md:w-[500px]" @click.away="modalImport = false">
        <h2 class="text-lg font-semibold border-b pb-3 dark:border-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-6 inline-block align-middle">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15"/>
            </svg>
            <span class="align-middle dark:text-gray-200">Import Data</span>
        </h2>
        <form wire:submit.prevent="import" method="POST">
            <div class="mt-3">
                <div class="alert alert-info alert-simple mb-3" role="alert">
                    <div>
                        Download import template <a title="Template file" href="javascript:"
                                                    wire:click="downloadTemplate">here</a></div>
                </div>
                <div class="form-group">
                    <label for="importFile" class="dark:text-gray-200">Choose file</label>
                    <input type="file" required wire:model="importFile" accept=".xls,.xlsx"
                           class="text-sm border border-gray-300 dark:border-gray-600 rounded-md p-2 w-full dark:bg-gray-700 dark:text-gray-200">
                    <div class="text-xs font-light text-gray-500 dark:text-gray-400">File supported: .xls,
                        .xlsx.
                        Max: {{config('cb.max_import_size', 1024)}} KB
                    </div>
                    @error('importFile')
                    <div class="form-error dark:text-red-400">{{$message}}</div>@enderror
                </div>
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button type="submit" class="btn btn-primary dark:btn-dark" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed">Import
                </button>
                <button type="button" @click="modalImport = false" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="btn btn-default dark:btn-dark">Cancel
                </button>
            </div>
        </form>
    </div>
</div>
