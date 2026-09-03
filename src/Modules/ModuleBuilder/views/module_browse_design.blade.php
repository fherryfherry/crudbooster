@php use CrudBooster\Components\Icon\Icon;@endphp
<div>
    @if(isset($confirmTitle))
        {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
    @endif
        <x-header pageTitle="Module Builder"/>
        @include("cb.module-builder::module_top_button")

    <div class="flex items-start justify-between gap-3 text-gray-600">
        <div class="button-steps">
            @include("cb.module-builder::module_sidebar")
        </div>
        <div class="content w-full">
            <div class="panel">
                <div class="panel-header">
                    <div class="item-start">
                        <h3 class="panel-title">Browse Grid Design</h3>
                    </div>
                    <div class="item-end">
                        <button type="button" wire:click="resetColumns" class="btn btn-outline-primary">
                            <div class="flex items-center gap-1">
                                <div>{!! Icon::REFRESH !!}</div>
                                Reset Columns
                            </div>
                        </button>
                    </div>
                </div>
                <form id="form-data" method="POST" wire:submit.prevent="formSave">
                    @csrf
                    <div class="panel-content">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Label</th>
                                <th>Key / Field</th>
                                <th class="text-center">Searchable</th>
                                <th class="text-center">Sortable</th>
                                <th class="text-center">Filterable</th>
                                <th class="text-center">Exportable</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($columns as $key=>$column)
                                <tr wire:key="{{$key}}">
                                    <td>
                                        <div class="form-group">
                                            <input type="text" wire:model="columns.{{$key}}.label"
                                                   placeholder="Input column label" class="form-control-mini">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model="columns.{{$key}}.key" class="form-control-mini">
                                                <option value="">- Select a Key -</option>
                                                @foreach($this->fieldGroup as $group)
                                                    <optgroup label="{{$group['table']}}">
                                                        @foreach($group['fields'] as $field)
                                                            <option
                                                                value="{{$group['table'].'.'.$field}}">{{$field}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            @if(str_contains($column['key'], '_RELATION_'))
                                                <div x-data="{open: false}" class="inline-block">
                                                    <button type="button" @click="open = true"
                                                            title="Setting relationship">
                                                        {!! Icon::COG !!}
                                                    </button>
                                                    <div x-show="open" class="modal">
                                                        <div class="modal-content"
                                                             @click.away="open = false">
                                                            <h3 class="modal-title">Config Relationship</h3>
                                                            @if($column['key'] == '_RELATION_')
                                                                @include('cb.module-builder::components.modal-column.modal_input_relation')
                                                            @endif
                                                            @if($column['key'] == '_RELATION_MANY_')
                                                                @include('cb.module-builder::components.modal-column.modal_input_relation_many')
                                                            @endif
                                                            <div class="flex justify-end space-x-2">
                                                                <button type="button" @click="open = false"
                                                                        class="btn btn-default">Cancel
                                                                </button>
                                                                <button type="button" @click="open = false"
                                                                        class="btn btn-primary">Save
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-group">
                                            <input type="checkbox" wire:model="columns.{{$key}}.searchable">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-group">
                                            <input type="checkbox" wire:model="columns.{{$key}}.sortable">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-group">
                                            <input type="checkbox" wire:model="columns.{{$key}}.filterable">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-group">
                                            <input type="checkbox" wire:model="columns.{{$key}}.exportable">
                                        </div>
                                    </td>
                                    <td class="td-no-wrap">
                                        <div class="form-group">
                                            <x-config-column :key="$key" :column="$column"/>
                                            <button type="button" title="Remove column"
                                                    wire:click="removeColumn({{$key}})">
                                                {!! Icon::TRASH !!}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="7" class="text-center w-full">
                                    <div class="flex justify-center">
                                        <button type="button" wire:click="addColumn" class="btn btn-primary">
                                            <span class="flex items-center gap-1">{!! Icon::PLUS !!} Add Column</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>


                        <div class="w-full mt-4">
                            <div class="flex justify-end space-x-2">
                                <a href="{{getCmsUrl('module-builder')}}" wire:navigate
                                   class="btn btn-default">Cancel</a>
                                <button class="btn btn-primary" wire:loading.attr="disabled" wire:target="formSave"
                                        type="submit">Save &raquo;
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
