{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<div class="panel mt-2" id="{{$column['key']}}">
    <div class="panel-fluid-max overflow-auto">
        <table class="table">
            <thead>
            <tr>
                <th>{{$column['option']['item_label'] ?? 'Item'}}</th>
                @foreach($column['option']['inputs'] as $input)
                    <th class="w-[300px]" wire:key="{{$input['name']}}"><div class="w-full text-center">{{$input['name']}}</div></th>
                @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach($column['option']['data'] as $data)
                    <tr wire:key="{{\Illuminate\Support\Str::slug(strtolower($data['name']))}}">
                        <td>{{$data['name']}}</td>
                        @foreach($data['inputs'] as $input)
                            <td class="align-middle">
                                @if($input['type'] == 'checkbox')
                                    <div class="w-full text-center">
                                        <input type="checkbox" @readonly($column['readonly'] ?? false) @readonly($input['readonly'] ?? false) wire:loading.attr="readonly" wire:target="formSave"  wire:model="formData.{{$column['key']}}.{{\Illuminate\Support\Str::slug(strtolower($data['name']))}}.{{\Illuminate\Support\Str::slug(strtolower($input['name']))}}" class="h-4 w-4 text-sky-600 cursor-pointer focus:ring-sky-500 border-gray-300 rounded">
                                    </div>
                                @endif
                                @if($input['type'] == 'text')
                                    <input type="text" placeholder="{{$input['placeholder']}}" @readonly($column['readonly'] ?? false) @readonly($input['readonly'] ?? false) wire:target="formSave" wire:loading.attr="readonly"  wire:model="formData.{{$column['key']}}.{{\Illuminate\Support\Str::slug(strtolower($data['name']))}}.{{\Illuminate\Support\Str::slug(strtolower($input['name']))}}" class="form-control-mini">
                                @endif
                                @if($input['type'] == 'number')
                                    <input type="number" placeholder="{{$input['placeholder']}}" @readonly($column['readonly'] ?? false) @readonly($input['readonly'] ?? false) wire:target="formSave" wire:loading.attr="readonly"  wire:model="formData.{{$column['key']}}.{{\Illuminate\Support\Str::slug(strtolower($data['name']))}}.{{\Illuminate\Support\Str::slug(strtolower($input['name']))}}" class="form-control-mini">
                                @endif
                                @if(isset($input['helpText']))
                                    <small class="text-gray-500 block">{{$input['helpText']}}</small>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
