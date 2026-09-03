{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
@foreach($column['option']['dataset']??[] as $group)
    <label class="input-radio-group">
        <input type="radio"
               name="{{$column['key']}}"
               wire:model.live="formData.{{$column['key']}}"
               wire:loading.attr="readonly"
               wire:target="formSave"
               @readonly($column['readonly'] ?? false)
               @if(isset($column['isOnChange']) && $column['isOnChange']) @change="$wire.__onChangeFormField('{{$column['key']}}', $event.target.value)" @endif
               class="mr-1"
               value="{{$group['key']}}"
        >
        <span>{{$group['label']??'-'}}</span>
    </label>
@endforeach
