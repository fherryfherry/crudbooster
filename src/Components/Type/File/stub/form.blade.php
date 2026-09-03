@php use CrudBooster\Components\Icon\Icon; @endphp
{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
@if($value && !is_object($value))
    <div class="inline-block relative">
        @if(getStorageFileExists($value, $column['option']['disk'] ?? null))
            <a href="{{ getStorageUrl($value, null, $column['option']['disk'] ?? null) }}" target="_blank" class="text-sky-600 hover:underline text-sm">
                Download File {{ strtoupper(pathinfo($value, PATHINFO_EXTENSION))}}
                ({{ round(getStorageFileSize($value, $column['option']['disk'] ?? null) / 1024, 2)}} KB) @if(!is_object($value))
                    <a title="Click here to remove file" href="javascript:void(0)"
                       wire:click="removeFile('{{$column['key']}}')" class="font-semibold">x</a>
                @endif
            </a>
        @else
            <div class="text-red-500 text-sm">
                ⚠️ File not found: {{ basename($value) }}
                <a title="Click here to remove broken file reference" href="javascript:void(0)"
                   wire:click="removeFile('{{$column['key']}}')" class="font-semibold text-red-600">Remove</a>
            </div>
        @endif
    </div>
@endif

{{-- File Upload Input & Thin Blue Progress Bar (Livewire + Alpine, Inline CSS, with Percent & Pulse) --}}
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
           {!! isset($column['option']['accept']) ? 'accept="' . $column['option']['accept'] . '"' : null !!}
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
