<div>
    @if($formDialogShow === 'CREATE')
        <div
            class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10 dark:bg-opacity-50 z-20">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-3/4 lg:w-[900px]"
                 @click.away="$wire.closeForm">
                <div style="overflow: auto; max-height: 80vh;">
                    @livewire($module['key'].'-form', ['actionOne' => 'create', 'moduleKey' => $module['key'],
                    'formDialog'=> true, 'foreignKey'=> $foreignKey, 'foreignKeyValue'=> $foreignKeyValue])
                </div>
            </div>
        </div>
    @endif
    @if($formDialogShow === 'DETAIL')
        <div
            class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10 dark:bg-opacity-50 z-20">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-3/4 lg:w-[900px]"
                 @click.away="$wire.closeForm">
                <div style="overflow: auto; max-height: 80vh;">
                    @livewire($module['key'].'-form', ['actionOne' => $formDialogId, 'moduleKey' => $module['key'],
                    'formDialog'=> true, 'foreignKey'=> $foreignKey, 'foreignKeyValue'=> $foreignKeyValue])
                </div>
            </div>
        </div>
    @endif
    @if($formDialogShow === 'EDIT')
        <div
            class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10 dark:bg-opacity-50 z-20">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-3/4 lg:w-[900px]"
                 @click.away="$wire.closeForm">
                <div style="overflow: auto; max-height: 80vh;">
                    @livewire($module['key'].'-form', ['actionOne' => $formDialogId, 'actionTwo' => 'edit', 'moduleKey'
                    => $module['key'], 'formDialog'=> true, 'foreignKey'=> $foreignKey, 'foreignKeyValue'=>
                    $foreignKeyValue])
                </div>
            </div>
        </div>
    @endif
</div>
