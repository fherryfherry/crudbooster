{{-- There are variables $column, $value, and $formData that you can use --}}
@if(isset($column['option']['dataset']))
    @php $columnValue = collect($column['option']['dataset'])->firstWhere('key', $value); @endphp
    {{ $columnValue ? $columnValue['label'] : '' }}
@endif