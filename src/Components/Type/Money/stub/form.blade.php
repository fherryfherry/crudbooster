{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<input type="text"
       id="{{$column['key']}}"
       placeholder="{{$column['placeholder'] ?? ''}}"
       wire:loading.attr="readonly"
       @readonly($column['readonly'] ?? false)
       wire:target="formSave"
       @if(isset($column['live']))
           wire:model.live.debounce.{{$column['live']}}ms="formData.{{$column['key']}}"
       @else
           wire:model="formData.{{$column['key']}}"
       @endif
       onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');"
       @if(isset($column['isOnChange']) && $column['isOnChange']) @change="$wire.__onChangeFormField('{{$column['key']}}', $event.target.value)" @endif
       class="form-control">
