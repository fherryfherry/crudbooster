{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<input type="url"
       id="{{$column['key']}}"
       {{ $focus ? 'autofocus': '' }}
       placeholder="{{$column['placeholder'] ?? ''}}"
       @readonly($column['readonly'] ?? false)
       @if(isset($column['live']))
              wire:model.live.debounce.{{$column['live']}}ms="formData.{{$column['key']}}"
       @else
              wire:model="formData.{{$column['key']}}"
       @endif
       wire:target="formSave"
       wire:loading.attr="readonly"
       @if(isset($column['inputEvents'])) @input="{{ $column['inputEvents'] }}" @change="{{ $column['inputEvents'] }}" @endif
       @if(isset($column['isOnChange']) && $column['isOnChange']) @change="$wire.__onChangeFormField('{{$column['key']}}', $event.target.value)" @endif
       class="form-control">
