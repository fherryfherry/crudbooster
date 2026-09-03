{{-- There are variables $column, $value, and $formData that you can use --}}
@if(isset($column['option']['html']))
    <p>{!! is_array($value) ? implode(', ', $value) : $value !!}</p>
@else
    <p>{{ is_array($value) ? implode(', ', $value) : $value }}</p>
@endif