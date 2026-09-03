{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
@if($value)
    <div class="block relative">
        @if(is_array($value))
            @foreach($value as $i=>$image)
                @if(is_array($image)) @continue @endif
                <div class="inline-block relative m-2">
                    {{-- If image saved --}}
                    @if(!is_object($image) && is_string($image))
                        @if(getStorageFileExists($image))
                            <a title="Click here to remove image" class="absolute z-90 top-0 right-1 text-white text-3xl font-bold hover:text-gray-500" href="javascript:void(0)" wire:click="removeImage('{{$column['key']}}', '{{ $image }}')">×</a>
                        @else
                            <a title="Click here to remove broken image reference" class="absolute z-90 top-0 right-1 text-red-500 text-3xl font-bold hover:text-red-700" href="javascript:void(0)" wire:click="removeImage('{{$column['key']}}', '{{ $image }}')">×</a>
                        @endif
                    @endif

                    {{-- If image temporary --}}
                    @if(is_object($image) && !is_string($image))
                        <a title="Click here to remove image" class="absolute z-90 top-0 right-1 text-white text-3xl font-bold hover:text-gray-500" href="javascript:void(0)" wire:click="removeTempImage('{{$column['key']}}', '{{ $i }}')">×</a>
                    @endif
                    
                    <a href="javascript:"
                       @click="openThumbnail = true; thumbnailSrc = '{{ is_object($image) && !is_string($image) ? $image->temporaryUrl() : getStorageUrl($image)}}'">
                        <img alt="{{$column['label']}}" title="{{$column['label']}}" class="form-image-preview"
                             src="{{is_object($image) && !is_string($image) ? $image->temporaryUrl() : getStorageUrl($image)}}"
                             @if(!is_object($image) && is_string($image) && !getStorageFileExists($image)) onerror="this.src='{{ config('cb.no_image_browse', 'https://placehold.co/48') }}'; this.title='Image not found'" @endif>
                    </a>
                </div>
            @endforeach
        @else
            <div class="inline-block relative">
                @if(!is_object($value))
                    @if(getStorageFileExists($value))
                        <a title="Click here to remove image" class="absolute z-90 top-0 right-1 text-white text-3xl font-bold hover:text-gray-500" href="javascript:void(0)" wire:click="removeImage('{{$column['key']}}', '{{$value}}')">×</a>
                    @else
                        <a title="Click here to remove broken image reference" class="absolute z-90 top-0 right-1 text-red-500 text-3xl font-bold hover:text-red-700" href="javascript:void(0)" wire:click="removeImage('{{$column['key']}}', '{{$value}}')">×</a>
                    @endif
                @endif
                @if(is_object($value) && !is_string($value))
                    <a title="Click here to remove image" class="absolute z-90 top-0 right-1 text-white text-3xl font-bold hover:text-gray-500" href="javascript:void(0)" wire:click="removeTempImage('{{$column['key']}}')">×</a>
                @endif
                <a href="javascript:"
                   @click="openThumbnail = true; thumbnailSrc = '{{ is_object($value) && !is_string($value) ? $value->temporaryUrl() : getStorageUrl($value)}}'">
                    <img alt="{{$column['label']}}" title="{{$column['label']}}" class="form-image-preview"
                         src="{{is_object($value) && !is_string($value) ? $value->temporaryUrl() : getStorageUrl($value)}}"
                         @if(!is_object($value) && is_string($value) && !getStorageFileExists($value)) onerror="this.src='{{ config('cb.no_image_browse', 'https://placehold.co/48') }}'; this.title='Image not found'" @endif>
                </a>
            </div>
        @endif
    </div>
@else
    {{-- Image Upload Input & Thin Blue Progress Bar (Livewire + Alpine, Inline CSS, with Percent & Pulse) --}}
    <style>
    @keyframes pulseBar {
        0% { opacity: 0.7; }
        50% { opacity: 1; }
        100% { opacity: 0.7; }
    }
    .pulse-bar {
        animation: pulseBar 1.2s infinite;
    }
    </style>
    <div
        x-data="{ uploading: false, progress: 0 }"
        x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false"
        x-on:livewire-upload-cancel="uploading = false"
        x-on:livewire-upload-error="uploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
    >
        <input type="file"
               accept=".jpg,.jpeg,.png,.gif,.bmp,.svg,.ttf"
               @if(isset($column['option']['multiple'])) multiple @endif
               id="{{$column['key']}}"
               @readonly($column['readonly'] ?? false)
               @if(isset($column['live']))
                   wire:model.live.debounce.{{$column['live']}}ms="formData.{{$column['key']}}"
               @else
                   wire:model="formData.{{$column['key']}}"
               @endif
               wire:target="formSave"
               wire:loading.attr="readonly"
               @if(isset($column['isOnChange']) && $column['isOnChange']) @change="$wire.__onChangeFormField('{{$column['key']}}', $event.target.value)" @endif
               class="form-control"
               style="display: block; width: 100%; font-size: 0.95em; border: 1px solid #d1d5db; border-radius: 4px; background: #fff; padding: 6px 8px;" />
        
        <div x-show="uploading" style="width: 100%; margin-top: 4px; display: flex; align-items: center;">
            <div style="flex:1; background: #e5e7eb; border-radius: 2px; height: 3px; overflow: hidden; margin-right: 8px; position: relative;">
                <div class="pulse-bar"
                     style="background: #3490dc; height: 3px; border-radius: 2px; transition: width 0.2s; position: absolute; left: 0; top: 0;"
                     :style="'width: ' + progress + '%; background: #3490dc; height: 3px; border-radius: 2px;'">
                </div>
            </div>
            <span style="font-size: 12px; color: #6b7280; min-width: 32px; text-align: right;" x-text="progress + '%'" x-cloak></span>
        </div>
    </div>
@endif

@once
    @include('cb.themes::components.imagepreview')
@endonce