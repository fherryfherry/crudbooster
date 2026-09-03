<div class="space-y-4">
    @if($withHeader)
        <x-header :pageTitle="$pageTitle"/>
    @endif

    <div class="panel"
         x-data="{modalImport: false, modalBulkActionConfirm: false, bulkActionId: null, bulkActionConfirmTitle: null, bulkActionConfirmText: null}"
         @close-modal-import.window="modalImport = false"
         @close-modal-bulk-action-confirm.window="modalBulkActionConfirm = false">
        <!-- Modal Import-->
        @cbModalImport
        <!-- Modal Bulk Confirmation -->
        @cbModalBulkConfirmation(['selectedIds'=>$selectedIds])

        @if(isset($confirmTitle))
            {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
        @endif

        <div class="panel-header flex-wrap gap-4">
            <div class="panel-header-title">
                <h2>{{$tableTitle}}</h2> <small class="text-gray-400">(Show {{ $result->firstItem() }}
                    ~ {{ $result->lastItem() }} data from total {{ $result->total() }} records)</small>
            </div>
            <div class="panel-header-action flex flex-wrap gap-1 items-center">
                @if($buttonSearch)
                    <div class="flex items-center rounded-md bg-gray-200 dark:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-5 h-5 text-gray-500 dark:text-gray-300 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>

                        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search..."
                               class="browse-search-bar dark:bg-gray-700 dark:text-gray-300">
                    </div>
                @endif
                @if($buttonFilter)
                    <div x-data="{ open: false }" class="relative">
                        <a title="Filter Advanced" href="javascript:" @click="open = !open; if(open) $wire.initializeFilterDraft()"
                           class="btn btn-default dark:btn-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="size-4 inline-block align-middle">
                                <path
                                    d="M6 12a.75.75 0 0 1-.75-.75v-7.5a.75.75 0 1 1 1.5 0v7.5A.75.75 0 0 1 6 12ZM18 12a.75.75 0 0 1-.75-.75v-7.5a.75.75 0 0 1 1.5 0v7.5A.75.75 0 0 1 18 12ZM6.75 20.25v-1.5a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0ZM18.75 18.75v1.5a.75.75 0 0 1-1.5 0v-1.5a.75.75 0 0 1 1.5 0ZM12.75 5.25v-1.5a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0ZM12 21a.75.75 0 0 1-.75-.75v-7.5a.75.75 0 0 1 1.5 0v7.5A.75.75 0 0 1 12 21ZM3.75 15a2.25 2.25 0 1 0 4.5 0 2.25 2.25 0 0 0-4.5 0ZM12 11.25a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5ZM15.75 15a2.25 2.25 0 1 0 4.5 0 2.25 2.25 0 0 0-4.5 0Z"/>
                            </svg>
                        </a>
                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg text-sm"
                             style="width:560px;max-width:100vw;">
                            <div
                                class="p-3 text-gray-500 dark:text-gray-200 text-sm font-bold border-b dark:border-gray-600">
                                Advanced Filter
                            </div>
                            @foreach($filterable as $filter)
                                <div class="flex items-center mb-3 p-4 border-b border-gray-300 dark:border-gray-600">
                                    <div class="font-bold text-sm dark:text-gray-200 text-right min-w-[160px] w-1/3 pr-4">
                                        {{$filter['label']}}
                                    </div>
                                    <div class="flex-1">
                                        {{-- input di sini, pindahkan semua input/select ke sini --}}
                                        @php
                                            $filterType = $filter['filter_type'] ?? 'contains';
                                            $filterOptions = $filter['filter_options'] ?? [];
                                        @endphp
                                        @if($filterType === 'contains')
                                            <input type="text"
                                                   class="p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none dark:bg-gray-700 dark:text-gray-200 w-full"
                                                   wire:model.defer="filterDraft.{{$filter['key']}}.value"
                                                   value="{{ $filter[$filter['key']]['value'] ?? '' }}"
                                                   placeholder="Contains...">
                                        @elseif($filterType === '>' || $filterType === '>=' || $filterType === '<' || $filterType === '<=')
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $filterType }}</span>
                                                <input type="number"
                                                       class="p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none dark:bg-gray-700 dark:text-gray-200 w-full"
                                                       wire:model.defer="filterDraft.{{$filter['key']}}.value"
                                                       value="{{ $filter[$filter['key']]['value'] ?? '' }}"
                                                       placeholder="Value">
                                            </div>
                                        @elseif($filterType === 'date_range')
                                            <div class="flex items-center gap-2">
                                                <input type="date"
                                                       class="p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none dark:bg-gray-700 dark:text-gray-200 text-xs"
                                                       wire:model.defer="filterDraft.{{$filter['key']}}.value.start"
                                                       value="{{ $filter[$filter['key']]['value']['start'] ?? '' }}"
                                                       placeholder="Start date">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">to</span>
                                                <input type="date"
                                                       class="p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none dark:bg-gray-700 dark:text-gray-200 text-xs"
                                                       wire:model.defer="filterDraft.{{$filter['key']}}.value.end"
                                                       value="{{ $filter[$filter['key']]['value']['end'] ?? '' }}"
                                                       placeholder="End date">
                                            </div>
                                        @elseif($filterType === 'select_enum')
                                            <select
                                                class="p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none dark:bg-gray-700 dark:text-gray-200 text-xs w-full"
                                                wire:model.defer="filterDraft.{{$filter['key']}}.value">
                                                <option value="">Select...</option>
                                                @foreach($filterOptions['options'] ?? [] as $value => $label)
                                                    <option value="{{ $value }}" {{ ($filter[$filter['key']]['value'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($filterType === 'select_query')
                                            <select
                                                class="p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none dark:bg-gray-700 dark:text-gray-200 text-xs w-full"
                                                wire:model.defer="filterDraft.{{$filter['key']}}.value">
                                                <option value="">Select...</option>
                                                @foreach($filterOptions['query_options'] ?? [] as $option)
                                                    <option value="{{ $option['value'] }}" {{ ($filter[$filter['key']]['value'] ?? '') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text"
                                                   class="p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none dark:bg-gray-700 dark:text-gray-200 w-full"
                                                   wire:model.defer="filterDraft.{{$filter['key']}}.value"
                                                   value="{{ $filter[$filter['key']]['value'] ?? '' }}"
                                                   placeholder="Filter...">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex justify-end p-4">
                                <button type="button" wire:click="applyAdvancedFilter" class="btn btn-primary">
                                    Apply Filter
                                </button>
                                <button type="button" wire:click="resetAdvancedFilter" class="btn btn-default ml-2">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="relative">
                    @can('create', $module['key'])
                        @if($buttonCreate)
                            @php
                                $createUrl = url(getCmsUrl($browsePath . '/create'));
                                $queryParams = [];
                                
                                // Add ref parameter if exists
                                if($ref) {
                                    $queryParams['ref'] = $ref;
                                }
                                
                                // Add encrypted parent module parameter from session if available
                                $encryptedParent = session('encrypted_parent_module_' . $browsePath);
                                if($encryptedParent) {
                                    $queryParams['parent-module'] = $encryptedParent;
                                }
                                
                                if (!empty($queryParams)) {
                                    $createUrl .= '?' . http_build_query($queryParams);
                                }
                            @endphp
                            <a title="Add data"
                               href="{{ !$formDialog ? $createUrl : 'javascript:' }}"
                               @if($formDialog) wire:click="openFormCreate" @else wire:navigate @endif
                               class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                     class="size-4 inline-block align-middle">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z"
                                          clip-rule="evenodd"/>
                                </svg>
                                <span class="align-middle">Add {{ $module['name'] ?? 'Data'}}</span>
                            </a>
                        @endif
                    @endcan
                </div>

                @if($buttonImport)
                    <div class="relative">
                        <a title="Add data" @click="modalImport = true" href="javascript:" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="size-4 inline-block align-middle">
                                <path fill-rule="evenodd"
                                      d="M11.47 2.47a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1-1.06 1.06l-3.22-3.22V16.5a.75.75 0 0 1-1.5 0V4.81L8.03 8.03a.75.75 0 0 1-1.06-1.06l4.5-4.5ZM3 15.75a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z"
                                      clip-rule="evenodd"/>
                            </svg>

                            <span class="align-middle">Import</span>
                        </a>
                    </div>
                @endif

                @if($buttonExportCsv || $buttonExportPdf || $buttonExportXls)
                    <div x-data="{ open: false }" class="relative">
                        <a title="Export" href="javascript:" @click="open = !open" class="btn btn-primary">
                            <span class="align-middle">Export</span>

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="size-4 inline-block align-middle">
                                <path fill-rule="evenodd"
                                      d="M11.47 13.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 0 0-1.06-1.06L12 11.69 5.03 4.72a.75.75 0 0 0-1.06 1.06l7.5 7.5Z"
                                      clip-rule="evenodd"/>
                                <path fill-rule="evenodd"
                                      d="M11.47 19.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 1 0-1.06-1.06L12 17.69l-6.97-6.97a.75.75 0 0 0-1.06 1.06l7.5 7.5Z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>

                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg text-sm">
                            @if($buttonExportXls)
                                @php
                                    $payload = encrypt(json_encode([
                                        'module' => $module['key'] ?? '',
                                        'title' => $pageTitle ?? $tableTitle ?? 'Exported Data',
                                    ]));
                                @endphp
                                <a href="{{ route('crudbooster.export.xls', ['key' => $payload]) }}" target="_blank"
                                   class="px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                         class="size-4 inline-block align-middle mr-1">
                                        <path fill-rule="evenodd"
                                              d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Export as .xlsx</span>
                                </a>
                            @endif
                            @if($buttonExportPdf)
                                @php
                                    $payload = encrypt(json_encode([
                                        'module' => $module['key'] ?? '',
                                        'title' => $pageTitle ?? $tableTitle ?? 'Exported Data',
                                    ]));
                                @endphp
                                <a href="{{ route('crudbooster.export.pdf', ['key' => $payload]) }}" target="_blank"
                                   class="px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                         class="size-4 inline-block align-middle mr-1">
                                        <path fill-rule="evenodd"
                                              d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Export as .pdf</span>
                                </a>
                            @endif
                            @if($buttonExportCsv)
                                @php
                                    $payload = encrypt(json_encode([
                                        'module' => $module['key'] ?? '',
                                        'title' => $pageTitle ?? $tableTitle ?? 'Exported Data',
                                    ]));
                                @endphp
                                <a href="{{ route('crudbooster.export.csv', ['key' => $payload]) }}" target="_blank"
                                   class="px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                         class="size-4 inline-block align-middle mr-1">
                                        <path fill-rule="evenodd"
                                              d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Export as .csv</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($buttonBulkAction)
                    <div x-data="{ open: false }" class="relative">
                        <a title="Bulk Action" href="javascript:" @click="open = !open" class="btn btn-default">
                            <span class="align-middle">Bulk Action</span>

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="size-4 inline-block align-middle">
                                <path fill-rule="evenodd"
                                      d="M11.47 13.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 0 0-1.06-1.06L12 11.69 5.03 4.72a.75.75 0 0 0-1.06 1.06l7.5 7.5Z"
                                      clip-rule="evenodd"/>
                                <path fill-rule="evenodd"
                                      d="M11.47 19.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 1 0-1.06-1.06L12 17.69l-6.97-6.97a.75.75 0 0 0-1.06 1.06l7.5 7.5Z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>

                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg text-sm">
                            @foreach($bulkActions as $bulkAction)
                                <a href="javascript:"
                                   x-on:click="modalBulkActionConfirm = true; bulkActionId = '{{ addslashes($bulkAction['id']) }}'; bulkActionConfirmTitle = '{{ addslashes($bulkAction['confirm_title']) }}'; bulkActionConfirmText = '{{ addslashes($bulkAction['confirm_text']) }}'"
                                   class="px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                         class="size-4 inline-block align-middle mr-1">
                                        <path fill-rule="evenodd"
                                              d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{$bulkAction['label']}}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($tableActionButtons) > 0)
                    <div x-data="{ open: false }" class="relative">
                        <a title="Actions" href="javascript:" @click="open = !open" class="btn btn-primary">
                            <span class="align-middle">Actions</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 inline-block align-middle">
                                <path fill-rule="evenodd" d="M11.47 13.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 0 0-1.06-1.06L12 11.69 5.03 4.72a.75.75 0 0 0-1.06 1.06l7.5 7.5Z" clip-rule="evenodd"/>
                                <path fill-rule="evenodd" d="M11.47 19.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 1 0-1.06-1.06L12 17.69l-6.97-6.97a.75.75 0 0 0-1.06 1.06l7.5 7.5Z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg text-sm z-50">
                            @foreach($tableActionButtons as $button)
                                @php
                                    $textColor = 'text-blue-600';
                                    if(str_contains($button['class'] ?? '', 'btn-success')) $textColor = 'text-green-600';
                                    elseif(str_contains($button['class'] ?? '', 'btn-warning')) $textColor = 'text-yellow-600';
                                    elseif(str_contains($button['class'] ?? '', 'btn-danger')) $textColor = 'text-red-600';
                                    elseif(str_contains($button['class'] ?? '', 'btn-info')) $textColor = 'text-cyan-600';
                                @endphp
                                <a href="javascript:"
                                   wire:click="__doActionTableActionButton('{{$button['id']}}')"
                                   class="px-4 py-2 {{ $textColor }} dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center whitespace-nowrap">
                                    @if($button['templateMode'] == 'ICON_ONLY')
                                        {!! $button['icon']??'' !!}
                                    @elseif($button['templateMode'] == 'ICON_TEXT')
                                        {!! $button['icon']??'' !!} <span class="ml-2">{{ $button['label'] }}</span>
                                    @else
                                        {!! $button['label'] !!}
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="panel-content overflow-auto">
            @if($browseDraggable)
                @include('cb.themes::table-list')
            @else
                @include('cb.themes::table-grid')
            @endif
        </div>
    </div>

    @if($formDialog)
        <livewire:cb-form-dialog :module="$module" :foreignKey="$foreignKey" :foreignKeyValue="$foreignKeyFilter" :$formDialogShow :$formDialogId/>
    @endif
</div>
