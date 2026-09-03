<label for="{{$column['key']}}">{{$column['label'] ?? 'No Label'}} @if(str_contains($column['validation']['formData.'.$column['key']]??'','required'))
        <sup class="text-red-500">*</sup>@endif</label>

{!! createInputTag($column, $formData[$column['key']] ?? '', $i==0) !!}

@if(array_key_exists('helpText', $column) && $column['helpText'])
    <small class="form-help">{!! $column['helpText'] !!}</small>
@endif
@error('formData.'.$column['key'])
<div class="form-error">{{ str_replace('form data.','',$message) }}</div>
@enderror
