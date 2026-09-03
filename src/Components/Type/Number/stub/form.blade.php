{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<input type="number"
       id="{{$column['key']}}"
       {{ $focus ? 'autofocus': '' }}
       placeholder="{{$column['placeholder'] ?? ''}}"
       @readonly($column['readonly'] ?? false)
       wire:loading.attr="readonly"
       wire:target="formSave"
       @if(isset($column['live']))
              wire:model.live.debounce.{{$column['live']}}ms="formData.{{$column['key']}}"
       @else
              wire:model="formData.{{$column['key']}}"
       @endif
       @if(isset($column['isOnChange']) && $column['isOnChange']) @change="$wire.__onChangeFormField('{{$column['key']}}', $event.target.value)" @endif
       class="form-control">
