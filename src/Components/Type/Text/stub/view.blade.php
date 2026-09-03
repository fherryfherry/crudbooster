{{-- There are variables $column, $value, and $formData that you can use --}}
@if(isset($column['option']['html']))
    <p>{!! $value ?? '-' !!}</p>
@else
    <p>{{ $value ?? '-' }}</p>
@endif