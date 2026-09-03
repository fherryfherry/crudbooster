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

    <div class="panel">
        <div class="panel-header">
            <div class="panel-header-title">
                <h2>@cbFormTitle(['formId'=>$formId])</h2>
                @if($containRequired)
                    <small class="text-gray-400">Fields marked <span class="text-red-500">*</span> are required</small>
                @endif
            </div>
            <div class="panel-header-action">
                <!-- Add any header actions here -->
            </div>
        </div>
        <div class="panel-content">
            <form id="form-data" method="POST" wire:submit.prevent="formSave">
                @csrf

                @cbForm(['formColumns'=> $formColumns, 'formData'=> $formData])

                <div class="w-full">
                    <div class="flex justify-end space-x-2">
                        @php
                            $cancelUrl = $ref ? urldecode($ref) : getCmsUrl($redirectBackPath);
                        @endphp
                        <a href="{{ !$formDialog ? $cancelUrl : 'javascript:' }}" wire:loading.attr="disabled" wire:target="formSave"
                           @if($formDialog) wire:click="$dispatch('closeFormDialog')" @else wire:navigate @endif
                           class="btn btn-default">Cancel</a>
                        @if($formId)
                            <button class="btn btn-primary" wire:loading.attr="disabled" wire:target="formSave"
                                    wire:click="formSave"
                                    type="submit">Update
                            </button>
                        @else
                            <button class="btn btn-primary" wire:loading.attr="disabled"
                                    wire:click="$set('saveMode', 'saveAddMore')" wire:target="formSave"
                                    type="submit">Save & Add More
                            </button>
                            <button class="btn btn-primary" wire:loading.attr="disabled"
                                    wire:click="$set('saveMode', 'save')" wire:target="formSave"
                                    type="submit">Save
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
