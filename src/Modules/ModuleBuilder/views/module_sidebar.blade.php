@php use CrudBooster\Components\Icon\Icon; @endphp
<ul class="rounded-md bg-white dark:bg-gray-800 border dark:border-gray-700">
    <li class="p-3 border-b dark:border-gray-700"><a title="Basic Info"
                                class="flex gap-3 items-center @if($menu === 'BASIC_INFO') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/info') : getCmsUrl('module-builder/create') }}"
                                wire:navigate>{!! Icon::INFO !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Basic Info <br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Basic information like name, etc</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Database Table Schema"
                                class="flex gap-3 items-center @if($menu === 'TABLE_SCHEMA') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/table-schema') : '#'}}"
                                wire:navigate>{!! Icon::DB !!}
            <span class="{{($menuIconOnly??false) ? 'hidden': null}}">Database Table<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Manage the table schema</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Relationship"
                                class="flex gap-3 items-center @if($menu === 'RELATIONSHIP') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/relationship') : '#'}}"
                                wire:navigate>{!! Icon::LINK !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Relationship<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Configure table relationships</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Hook Query"
                                class="flex gap-3 items-center @if($menu === 'HOOK_QUERY') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/hook-query') : '#'}}"
                                wire:navigate>{!! Icon::CODE !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Query Condition<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Modify the browse module query</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Browse Grid Design"
                                class="flex gap-3 items-center @if($menu === 'BROWSE_DESIGN') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/browse-design') : '#'}}"
                                wire:navigate>{!! Icon::TABLE !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Browse Grid Design<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Adjust column to show at grid</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Bulk Action"
                                class="flex gap-3 items-center @if($menu === 'BULK_ACTION') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/bulk-action') : '#'}}"
                                wire:navigate>{!! Icon::BOLT !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Bulk Action Button<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Add more bulk action</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Action Button"
                                class="flex gap-3 items-center @if($menu === 'ACTION_BUTTON') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/action-button') : '#'}}"
                                wire:navigate>{!! Icon::BOLT !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Row Action Button<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Additional action button at row</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Form Design"
                                class="flex gap-3 items-center @if($menu === 'FORM_DESIGN') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/form-design') : '#'}}"
                                wire:navigate>{!! Icon::FORM !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Form Design<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Adjust field to show at form</small></span></a></li>
    <li class="p-3 border-b dark:border-gray-700"><a title="Form Hook"
                                class="flex gap-3 items-center @if($menu === 'FORM_HOOK') text-sky-600 dark:text-sky-400 @endif hover:text-sky-600 dark:hover:text-sky-400"
                                href="{{ $uuid ? getCmsUrl('module-builder/'.$uuid.'/form-hook') : '#'}}"
                                wire:navigate>{!! Icon::CODE !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Form Hook<br/><small
                    class="text-gray-400 dark:text-gray-500 whitespace-nowrap">Intercept on form processing</small></span></a></li>
</ul>

<ul class="rounded-md bg-white dark:bg-gray-800 border dark:border-gray-700 mt-4 hidden lg:block">
    <li>
        <button title="Click here to build / rebuild the module"
                wire:click="buildModuleConfirm"
                class="btn btn-block btn-primary"
            @disabled(!$this->validateBuild)>{!! Icon::PLAY !!} <span
                class="{{($menuIconOnly??false) ? 'hidden': null}}">Re/Build Module</span></button>
    </li>
</ul>
