{{-- There are variables $column, $value, and $formData that you can use --}}
<div class="panel mt-2" id="{{$column['key']}}">
    <div class="panel-fluid-max">
        <table class="table">
            <thead>
            <tr>
                <th class="w-1/2">{{$column['option']['item_label'] ?? 'Item'}}</th>
                @foreach($column['option']['checklist'] as $checklist)
                    <th wire:key="{{$checklist}}"><div class="w-full text-center">{{$checklist}}</div></th>
                @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach($column['option']['data'] as $data)
                    <tr wire:key="{{\Illuminate\Support\Str::slug(strtolower($data['name']))}}">
                        <td>{{$data['name']}}</td>
                        @foreach($column['option']['checklist'] as $checklist)
                            <td wire:key="{{ \Illuminate\Support\Str::slug(strtolower($checklist))}}" class="align-middle text-center">
                                <input type="checkbox" disabled wire:model="formData.{{$column['key']}}.{{\Illuminate\Support\Str::slug(strtolower($data['name']))}}.{{\Illuminate\Support\Str::slug(strtolower($checklist))}}" class="h-4 w-4 text-blue-600 cursor-pointer focus:ring-blue-500 border-gray-300 rounded">
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>