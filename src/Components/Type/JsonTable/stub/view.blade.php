@php use Illuminate\Support\Str; @endphp
{{-- There are variables $column, $value, and $formData that you can use --}}
<div class="panel mt-2" id="{{$column['key']}}">
    <div class="panel-fluid-max">
        <table class="table">
            <thead>
            <tr>
                <th class="w-1/2">{{$column['option']['item_label'] ?? 'Item'}}</th>
                @foreach($column['option']['inputs'] as $input)
                    <th wire:key="{{$input['name']}}">
                        <div class="w-full text-center">{{$input['name']}}</div>
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($column['option']['data'] as $data)
                <tr wire:key="{{Str::slug(strtolower($data['name']))}}">
                    <td>{{$data['name']}}</td>
                    @foreach($data['inputs'] as $input)
                        <td wire:key="{{ Str::slug(strtolower($input['name']))}}" class="align-middle text-center">
                            @if($input['type'] == 'checkbox')
                                <input type="checkbox" disabled
                                       wire:model="formData.{{$column['key']}}.{{Str::slug(strtolower($data['name']))}}.{{Str::slug(strtolower($input['name']))}}"
                                       class="h-4 w-4 text-blue-600 cursor-pointer focus:ring-blue-500 border-gray-300 rounded">
                            @endif
                            @if($input['type'] == 'text')
                                <span>{{ $formData[$column['key']][Str::slug(strtolower($data['name']))][Str::slug(strtolower($input['name']))] ?? '-' }}</span>
                            @endif
                            @if($input['type'] == 'number')
                                <span>{{
                                number_format($formData[$column['key']][Str::slug(strtolower($data['name']))][Str::slug(strtolower($input['name']))] ?? 0) }}</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
