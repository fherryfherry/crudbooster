@php use CrudBooster\Components\Icon\Icon; @endphp
<div x-data="{openThumbnail: false, thumbnailSrc: ''}">

    @if(isset($confirmTitle))
        {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
    @endif

    @if(!$formDialog)
        <x-header :pageTitle="$pageTitle"/>

        <div class="my-4">
            @php
                $backUrl = $ref ? urldecode($ref) : getCmsUrl($redirectBackPath);
            @endphp
            <a href="{{ $backUrl }}" wire:navigate class="text-sm text-gray-500 hover:text-sky-600">&laquo;
                Go Back To List</a>
        </div>
    @endif

    <div class="panel mb-4">
        <div class="panel-header">
            <div class="panel-header-title">
                <h2>Detail Data</h2>
            </div>
            <div class="panel-header-action">
                @if(!$formDialog)
                    @can('update', $module['key'])
                        @if($buttonEdit)
                            <a title="Edit Data"
                               href="{{getCmsUrl($module['key'])}}/{{$formId}}/edit?ref={{urlencode($currentUrl)}}"
                               wire:navigate>
                                {!! Icon::PENCIL !!}
                            </a>
                        @endif
                    @endcan
                @endif
            </div>
        </div>
        <div class="panel-content">
            @php
                // Transform display values for detail page only
                $this->__transformDisplayForDetail();
            @endphp
            @cbDetailContent(['formColumns'=> $formColumns, 'formData'=> $formData])

            <div class="w-full">
                <div class="flex justify-end space-x-2">
                    @php
                        $cancelUrl = $ref ? urldecode($ref) : getCmsUrl($redirectBackPath);
                    @endphp
                    <a href="{{ !$formDialog ? $cancelUrl : 'javascript:' }}"
                       @if(!$formDialog) wire:navigate @else wire:click="$dispatch('closeFormDialog')" @endif
                       class="btn btn-default">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    @foreach($subModules as $index => $subModule)
            @php
                /** @var $formData */
                    $foreignKeyValue = $subModule['localKey'] ? $formData[$subModule['localKey']] : $formId;
                    $openBehavior = $subModule['openBehavior'] ?? 'dialog';
                    $refUrl = urlencode($currentUrl);
                    
                    // Create encrypted parent data for SubModule security
                    $parentData = [
                        'parent_id' => $foreignKeyValue,
                        'parent_module_key' => $browsePath,
                        'foreign_key' => $subModule['foreignKey'],
                    ];
                    $encryptedParentModule = encrypt(json_encode($parentData));
            @endphp
            
            @if($openBehavior === 'page')
                <div wire:key="{{ $subModule['key'] }}-browse-{{ $foreignKeyValue }}-{{ $index }}">
                    @livewire($subModule['key'].'-browse', ['withHeader' => false, 'tableTitle' => $subModule['tableTitle'],'formDialog'=> false, 'moduleKey'=> $subModule['key'], 'foreignKey'=> $subModule['foreignKey'],'foreignKeyValue'=> $foreignKeyValue, 'ref' => $refUrl, 'encryptedParentModule' => $encryptedParentModule])
                </div>
            @else
                <div wire:key="{{ $subModule['key'] }}-browse-dialog-{{ $foreignKeyValue }}-{{ $index }}">
                    @livewire($subModule['key'].'-browse', ['withHeader' => false, 'tableTitle' => $subModule['tableTitle'],'formDialog'=> true, 'moduleKey'=> $subModule['key'], 'foreignKey'=> $subModule['foreignKey'],'foreignKeyValue'=> $foreignKeyValue, 'ref' => $refUrl, 'encryptedParentModule' => $encryptedParentModule])
                </div>
            @endif
    @endforeach
</div>
