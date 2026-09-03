@if($showCreateQueryModal ?? false)
<div x-show="showCreateQuery" x-cloak
    x-on:query-saved.window="if (showCreateQuery) { $wire.set(createQueryTargetField, $event.detail.id); showCreateQuery = false; $wire.$refresh(); }"
    x-on:close-query-modal.window="showCreateQuery = false"
    class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10"
    style="z-index: 1100;">
    <div class="relative bg-white rounded-lg shadow-lg overflow-y-auto dark:bg-gray-800"
        style="width: 95vw; max-width: 1100px; max-height: 90vh;">
        @livewire(\CrudBooster\Modules\QueryBuilder\Livewire\QueryBuilderForm::class, ['embedded' => true], 'query-picker-create-'.$pageId.'-'.$rowIndex.'-'.$colIndex)
    </div>
</div>
@endif
