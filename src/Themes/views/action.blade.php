{{-- DEBUG: actionButtonMode={{ $actionButtonMode ?? 'undefined' }}, actionButtonCount={{ isset($row->__actionButton) ? count($row->__actionButton) : 'null' }} --}}
@if(isset($actionButtonMode) && $actionButtonMode === 'threedot')
    <div x-data="{ open: false }" class="relative inline-block text-left">
        <button @click="open = !open" class="focus:outline-none p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Actions">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="text-gray-600 dark:text-gray-300">
                <circle cx="12" cy="6" r="2"/>
                <circle cx="12" cy="12" r="2"/>
                <circle cx="12" cy="18" r="2"/>
            </svg>
        </button>
        <div x-show="open" @click.away="open = false" class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
            @foreach($row->__actionButton ?? [] as $button)
                <a
                    wire:loading.attr="disabled"
                    wire:loading.class="disabled"
                    @if(isset($button['is_callable']))
                        wire:click="__doActionButtonCallback('{{$button['callable_name']}}', '{{$rowId}}', false)"
                    @else
                        wire:click="__doActionButtonRedirect('{{$button['url']}}', {{$button['confirm'] ? 'true' : 'false'}}, '{{$button['label']}}', false)"
                    @endif
                    href="javascript:"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 last:border-b-0 dark:border-gray-700"                    
                >
                    <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">{!! $button['icon'] ?? '' !!}</span>
                    <span class="flex-1 text-left">{{$button['label'] ?? ''}}</span>
                </a>
            @endforeach
            
            {{-- Default buttons --}}
            @can('read', $module['key'])
                @if($buttonDetail && $row->__buttonDetailVisible)
                    @php
                        $detailUrl = url(getCmsUrl($browsePath . '/' . $rowId));
                        $detailParams = [];
                        if($ref) $detailParams['ref'] = $ref;
                        $encryptedParent = session('encrypted_parent_module_' . $browsePath);
                        if($encryptedParent) $detailParams['parent-module'] = $encryptedParent;
                        if(!empty($detailParams)) $detailUrl .= '?' . http_build_query($detailParams);
                    @endphp
                    <a title="Detail Data" href="{{ !$formDialog ? $detailUrl : 'javascript:' }}"
                       @if(!$formDialog) wire:navigate @else wire:click="openFormDetail('{{$rowId}}')" @endif
                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700">
                        <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                <path fill-rule="evenodd"
                                      d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span class="flex-1 text-left">Detail</span>
                    </a>
                @endif
            @endcan
            @can('update', $module['key'])
                @if($buttonEdit && $row->__buttonEditVisible)
                    @php
                        $editUrl = url(getCmsUrl($browsePath . '/' . $rowId . '/edit'));
                        $editParams = [];
                        if($ref) $editParams['ref'] = $ref;
                        $encryptedParent = session('encrypted_parent_module_' . $browsePath);
                        if($encryptedParent) $editParams['parent-module'] = $encryptedParent;
                        if(!empty($editParams)) $editUrl .= '?' . http_build_query($editParams);
                    @endphp
                    <a title="Edit Data"
                       href="{{ !$formDialog ? $editUrl : 'javascript:' }}"
                       @if(!$formDialog) wire:navigate @else wire:click="openFormEdit('{{$rowId}}')" @endif
                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700">
                        <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path
                                    d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z"/>
                                <path
                                    d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z"/>
                            </svg>
                        </span>
                        <span class="flex-1 text-left">Edit</span>
                    </a>
                @endif
            @endcan
            @can('delete', $module['key'])
                @if($buttonDelete && $row->__buttonDeleteVisible)
                    <a title="Delete Data" href="javascript:" wire:click="deleteConfirmation('{{$rowId}}')" 
                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 last:border-b-0 dark:border-gray-700">
                        <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd"
                                      d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span class="flex-1 text-left">Delete</span>
                    </a>
                @endif
            @endcan
        </div>
    </div>
@else
    {{-- Inline mode (default) --}}
    @if($row->__actionButton)
        @foreach($row->__actionButton as $button)
            {{-- DEBUG: button label={{ $button['label'] ?? 'no label' }} --}}
            <a title="{{$button['label']}}"
               wire:loading.attr="disabled"
               wire:loading.class="disabled"
               @if(isset($button['is_callable']))
                   wire:click="__doActionButtonCallback('{{$button['callable_name']}}', '{{$rowId}}', false)"
               @else
                   wire:click="__doActionButtonRedirect('{{$button['url']}}', {{$button['confirm'] ? 'true' : 'false'}}, '{{$button['label']}}', false)"
               @endif
               href="javascript:"
               class="{{$button['class']}} browse-action-button flex items-center justify-center gap-1 text-nowrap">
                @if($buttonActionStyle == 'ICON_ONLY' || $buttonActionStyle == 'ICON_TEXT')
                    {!! $button['icon'] !!}
                @endif
                @if($buttonActionStyle == 'ICON_TEXT' || $buttonActionStyle == 'TEXT_ONLY')
                    {!! $button['label'] !!}
                @endif
            </a>
        @endforeach
    @endif
    @can('read', $module['key'])
        @if($buttonDetail && $row->__buttonDetailVisible)
            @php
                $detailUrl = url(getCmsUrl($browsePath . '/' . $rowId));
                $detailParams = [];
                if($ref) $detailParams['ref'] = $ref;
                $encryptedParent = session('encrypted_parent_module_' . $browsePath);
                if($encryptedParent) $detailParams['parent-module'] = $encryptedParent;
                if(!empty($detailParams)) $detailUrl .= '?' . http_build_query($detailParams);
            @endphp
            <a title="Detail Data" href="{{ !$formDialog ? $detailUrl : 'javascript:' }}"
               @if(!$formDialog) wire:navigate @else wire:click="openFormDetail('{{$rowId}}')" @endif
               class="btn btn-primary browse-action-button flex items-center justify-center gap-1">
                @if($buttonActionStyle == 'ICON_ONLY' || $buttonActionStyle == 'ICON_TEXT')
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                    <path fill-rule="evenodd"
                          d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                          clip-rule="evenodd"/>
                </svg>
                @endif
                @if($buttonActionStyle == 'ICON_TEXT' || $buttonActionStyle == 'TEXT_ONLY')
                    Detail
                @endif
            </a>
        @endif
    @endcan
    @can('update', $module['key'])
        @if($buttonEdit && $row->__buttonEditVisible)
            @php
                $editUrl = url(getCmsUrl($browsePath . '/' . $rowId . '/edit'));
                $editParams = [];
                if($ref) $editParams['ref'] = $ref;
                $encryptedParent = session('encrypted_parent_module_' . $browsePath);
                if($encryptedParent) $editParams['parent-module'] = $encryptedParent;
                if(!empty($editParams)) $editUrl .= '?' . http_build_query($editParams);
            @endphp
            <a title="Edit Data"
               href="{{ !$formDialog ? $editUrl : 'javascript:' }}"
               @if(!$formDialog) wire:navigate @else wire:click="openFormEdit('{{$rowId}}')" @endif
               class="btn btn-primary browse-action-button flex items-center justify-center gap-1">
                @if($buttonActionStyle == 'ICON_ONLY' || $buttonActionStyle == 'ICON_TEXT')
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z"/>
                    <path
                        d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z"/>
                </svg>
                @endif
                @if($buttonActionStyle == 'ICON_TEXT' || $buttonActionStyle == 'TEXT_ONLY')
                    Edit
                @endif
            </a>
        @endif
    @endcan
    @can('delete', $module['key'])
        @if($buttonDelete && $row->__buttonDeleteVisible)
            <a title="Delete Data" href="javascript:" wire:click="deleteConfirmation('{{$rowId}}')" class="btn btn-danger browse-action-button flex items-center justify-center gap-1">
                @if($buttonActionStyle == 'ICON_ONLY' || $buttonActionStyle == 'ICON_TEXT')
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                          clip-rule="evenodd"/>
                </svg>
                @endif
                @if($buttonActionStyle == 'ICON_TEXT' || $buttonActionStyle == 'TEXT_ONLY')
                    Delete
                @endif
            </a>
        @endif
    @endcan
@endif
