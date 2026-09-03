@php use Illuminate\Support\Str; @endphp
{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<div class="panel mt-2" id="{{$column['key']}}">
    <div class="panel-fluid-max overflow-auto">
        <table class="table">
            <thead>
            <tr>
                <th>{{$column['option']['item_label'] ?? 'Item'}}</th>
                @foreach($column['option']['checklist'] as $checklist)
                    <th class="w-5" wire:key="{{$checklist}}">
                        <div class="w-full text-center">{{$checklist}}</div>
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($column['option']['data'] as $data)
                <tr wire:key="{{Str::slug(strtolower($data['name']))}}">
                    <td class="flex items-center gap-3">
                        <input type="checkbox"
                               class="h-4 w-4 text-blue-600 cursor-pointer focus:ring-blue-500 border-gray-300 rounded"
                               wire:click="__jsonCheckListTickHorizontal('{{$column['key']}}','{{Str::slug(strtolower($data['name']))}}')">
                        {{$data['name']}}</td>
                    @foreach($column['option']['checklist'] as $checklist)
                        @php $enable = !$data['is_disabled'] && in_array($checklist, $data['checklist']); @endphp
                        <td wire:key="{{ Str::slug(strtolower($checklist))}}" class="align-middle text-center">
                            <input type="checkbox"
                                   @disabled(!$enable)
                                   @readonly($column['readonly'] ?? false)
                                   wire:loading.attr="readonly"
                                   wire:target="formSave"
                                   wire:model="formData.{{$column['key']}}.{{Str::slug(strtolower($data['name']))}}.{{Str::slug(strtolower($checklist))}}"
                                   class="h-4 w-4 text-blue-600 cursor-pointer focus:ring-blue-500 border-gray-300 rounded">
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
