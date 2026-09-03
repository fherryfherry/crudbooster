@php use CrudBooster\Components\Icon\Icon;@endphp
<div>
    @if(isset($confirmTitle))
        {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
    @endif
        <x-header pageTitle="Module Builder"/>
        @include("cb.module-builder::module_top_button")

    <div class="flex items-start justify-between gap-3 text-gray-600">
        <div class="button-steps lg:w-[400px]">
            @include("cb.module-builder::module_sidebar")
        </div>
        <div class="content w-full">
            <div class="panel">
                <div class="panel-header">
                    <div class="item-start">
                        <h3 class="panel-title">Form Design</h3>
                    </div>
                    <div class="item-end">
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="addColumn(1)" class="btn btn-info">
                                <div class="flex items-center gap-1">
                                    <div>{!! Icon::PLUS !!}</div>
                                    1 Column Layout
                                </div>
                            </button>
                            <button type="button" wire:click="addColumn(2)" class="btn btn-info">
                                <div class="flex items-center gap-1">
                                    <div>{!! Icon::PLUS !!}</div>
                                    2 Column Layout
                                </div>
                            </button>
                            <button type="button" wire:click="addColumn(3)" class="btn btn-info">
                                <div class="flex items-center gap-1">
                                    <div>{!! Icon::PLUS !!}</div>
                                    3 Column Layout
                                </div>
                            </button>
                            <button type="button" wire:click="resetForm" class="btn btn-outline-primary">
                                <div class="flex items-center gap-1">
                                    <div>{!! Icon::REFRESH !!}</div>
                                    Reset Form
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <form id="form-data" method="POST" wire:submit.prevent="formSave">
                    @csrf
                    <div x-data="{openModalType: false, dragSrc: null}" class="panel-content">
                        <!-- FORM DESIGN -->

                        @foreach($columns as $indexRow=>$column)
                            <div class="flex items-center justify-between gap-2 mb-2">
                                @foreach($column as $indexCol=>$columnValue)
                                    <div class="border-dashed border-2 border-gray-300 p-5 text-center w-full"
                                         wire:key="slot-{{$indexRow}}-{{$indexCol}}"
                                         @dragover.prevent
                                         @drop.prevent="$wire.reorderElement(dragSrc?.row, dragSrc?.col, {{$indexRow}}, {{$indexCol}}); dragSrc=null">
                                        @if($columnValue === null)
                                            <a href="javascript:" class="cursor-pointer"
                                               title="Click here to set input element" @click="openModalType = true"
                                               wire:click="addInput({{$indexRow}}, {{$indexCol}})">
                                                <div class="text-2xl text-gray-200">
                                                    Empty
                                                </div>
                                                <div class="text-gray-200">
                                                    Element
                                                </div>
                                            </a>
                                            <div class="flex justify-end gap-2">
                                                <a href="javascript:" title="Set input element"
                                                   @click="openModalType = true"
                                                   wire:click="addInput({{$indexRow}}, {{$indexCol}})"
                                                   class="text-green-500">
                                                    {!! Icon::PLUS !!}
                                                </a>
                                                <a href="javascript:" title="Delete Column"
                                                   wire:click="removeColumn({{$indexRow}}, {{$indexCol}})"
                                                   class="text-gray-500">
                                                    {!! Icon::TRASH !!}
                                                </a>
                                                <a href="javascript:" title="Add right column"
                                                   wire:click="addSideColumn({{$indexRow}}, {{$indexCol}}, 'RIGHT')"
                                                   class="text-gray-500">
                                                    {!! Icon::COLUMN !!}
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-2xl text-gray-400"
                                                 draggable="true"
                                                 @dragstart="dragSrc = {row: {{$indexRow}}, col: {{$indexCol}}}">
                                                {{$columnValue['label']}}
                                            </div>
                                            <div class="text-gray-400">
                                                {{$columnValue['type']}}
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <a href="javascript:" title="Edit element" @click="openModalType = true"
                                                   wire:click="editInput({{$indexRow}}, {{$indexCol}})"
                                                   class="text-sky-500">
                                                    {!! Icon::COG !!}
                                                </a>
                                                <a href="javascript:" title="Delete element"
                                                   wire:click="removeInput({{$indexRow}}, {{$indexCol}})"
                                                   class="text-gray-500">
                                                    {!! Icon::TRASH !!}
                                                </a>
                                                <a href="javascript:" title="Add right column"
                                                   wire:click="addSideColumn({{$indexRow}}, {{$indexCol}})"
                                                   class="text-gray-500">
                                                    {!! Icon::COLUMN !!}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                        @if(count($columns) == 0)
                            <div class="border-dashed border-2 border-gray-300 p-5 text-center">
                                <div class="text-2xl text-gray-400">
                                    Add Column Layout
                                </div>
                                <div class="text-gray-400">
                                    You can add column layout by clicking the button above
                                </div>
                            </div>
                        @endif

                        <div x-show="openModalType" class="modal">
                            <div class="modal-content w-1/2" @click.away="openModalType = false">
                                <h3 class="modal-title">Form Input Element</h3>

                                <div class="form-group">
                                    <label>Label</label>
                                    <input type="text" wire:model="input.label" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Field / Key</label>
                                    <select class="form-control" wire:model="input.key">
                                        @foreach($this->fieldGroup as $group)
                                            <optgroup label="{{$group['table']}}">
                                                @foreach($group['fields'] as $field)
                                                    <option value="{{$group['table'].'.'.$field}}">{{$field}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Type</label>
                                    <select wire:model.live="input.type" class="form-control">
                                        <option value="">- Select a Type</option>
                                        @foreach($this->typeList as $group => $types)
                                            <optgroup label="{{$group}}">
                                                @foreach($types as $type)
                                                    <option value="{{$type['type']}}">{{$type['type']}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Option</label>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Option Name</th>
                                            <th class="w-[50px]">Enable</th>
                                            <th class="w-[50px]">
                                                <div>Config</div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(!$listOption)
                                            <tr>
                                                <td colspan="3" class="text-center"><span class="text-gray-400">No Option Available</span>
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach($listOption as $opt)
                                            <tr>
                                                <td>
                                                    {{$opt['label']}}
                                                    <div class="text-xs text-gray-400 whitespace-normal">
                                                        {{$opt['description']}}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <input type="checkbox"
                                                               wire:model.live="input.options.{{$opt['name']}}.enable">
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($opt['paramCount'] > 0)
                                                        <div x-data="{openOption: false}">
                                                            <a href="javascript:" @click="openOption = true"
                                                               title="Configuration">
                                                                {!! Icon::COG !!}
                                                            </a>
                                                            <div x-show="openOption" class="modal">
                                                                <div class="modal-content w-1/3">
                                                                    <h3 class="modal-title">Option Configuration</h3>
                                                                    @foreach($opt['paramList'] as $param)
                                                                        <div class="form-group">
                                                                            <label
                                                                                class="text-left">{{$param['name']}}</label>
                                                                            @if(str_contains($param['description'],'App\Models'))
                                                                                <select
                                                                                    wire:model.live="input.options.{{$opt['name']}}.{{$param['name']}}"
                                                                                    class="form-control">
                                                                                    <option value="">- Select a Model
                                                                                        -
                                                                                    </option>
                                                                                    @foreach($this->modelList as $model)
                                                                                        <option
                                                                                            value="{{$model}}">{{$model}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @elseif(str_contains($param['type'],'Closure'))
                                                                                <textarea rows="7"
                                                                                          wire:model="input.options.{{$opt['name']}}.{{$param['name']}}"
                                                                                          placeholder="{{$param['placeholder']}}"
                                                                                          class="form-control"></textarea>
                                                                            @elseif(str_contains($param['type'],'array'))
                                                                                <textarea rows="7"
                                                                                          wire:model="input.options.{{$opt['name']}}.{{$param['name']}}"
                                                                                          placeholder="{{$param['placeholder']}}"
                                                                                          class="form-control"></textarea>
                                                                            @elseif(in_array($param['name'], ['Key','Label']) && $this->relationFieldSuggestions($opt['name']))
                                                                                <input type="text"
                                                                                       placeholder="{{$param['placeholder']}}"
                                                                                       list="relation-field-suggestions-{{$opt['name']}}-{{$param['name']}}"
                                                                                       wire:model="input.options.{{$opt['name']}}.{{$param['name']}}"
                                                                                       class="form-control">
                                                                                <datalist id="relation-field-suggestions-{{$opt['name']}}-{{$param['name']}}">
                                                                                    @foreach($this->relationFieldSuggestions($opt['name']) as $field)
                                                                                        <option value="{{$field}}"></option>
                                                                                    @endforeach
                                                                                </datalist>
                                                                            @else
                                                                                <input type="text"
                                                                                       placeholder="{{$param['placeholder']}}"
                                                                                       wire:model="input.options.{{$opt['name']}}.{{$param['name']}}"
                                                                                       class="form-control">
                                                                            @endif
                                                                            <div
                                                                                class="help-text mt-1 text-xs text-gray-400 whitespace-normal">{{$param['description']}}</div>
                                                                        </div>
                                                                    @endforeach

                                                                    <div class="flex justify-end space-x-2">
                                                                        <button type="button" class="btn btn-default"
                                                                                @click="openOption = false">
                                                                            Cancel
                                                                        </button>
                                                                        <button type="button"
                                                                                @click="openOption = false"
                                                                                class="btn btn-primary">Apply
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <label>Readonly</label>
                                    <x-toggle-button id="readonly" model="input.readonly"/>
                                </div>
                                <div class="form-group">
                                    <label>Default Value</label>
                                    <input type="text" wire:model="input.default" placeholder="E.g: {auth.id}"
                                           class="form-control">
                                    <div class="form-help">
                                        <div class="text-xs text-gray-400">
                                            Default value for this field. You can use alias: {session.key} to get value from session or {auth.id} to get logged in id<br/>
                                            Alias auth available: {auth.id}, {auth.name}, {auth.email}. <br/>
                                            Alias date & time: {current_date}, {current_time}, {current_datetime}<br/>
                                            Alias other: {random_number}, {random_string}, {uuid}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    @if($input['validationSimple']??true)
                                    <label>Validation Required <sup><a href="javascript:" @click="$wire.set('input.validationSimple', false)">(Advance)</a></sup></label>
                                    <x-toggle-button id="validation-required" model="input.validationRequired"/>
                                    @else
                                    <label>Validation Required <sup><a href="javascript:" @click="$wire.set('input.validationSimple', true)">(Simple)</a></sup></label>
                                    <input type="text" wire:model="input.validation" placeholder="E.g: required|email"
                                           class="form-control">
                                    <div class="form-help">
                                        <div class="text-xs text-gray-400">Separate by pipe (|) character. Check laravel rule
                                            <a href="https://laravel.com/docs/12.x/validation#available-validation-rules" target="_blank">here</a>.</div>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="form-group w-1/2">
                                        <label>Placeholder</label>
                                        <input type="text" wire:model="input.placeholder" class="form-control">
                                        <div class="form-help">
                                            <div class="text-xs text-gray-400">A hint text for user input</div>
                                        </div>
                                    </div>
                                    <div class="form-group w-1/2">
                                        <label>Help Text</label>
                                        <input type="text" wire:model="input.helpText" class="form-control">
                                        <div class="form-help">
                                            <div class="text-xs text-gray-400">A help text for user input</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Show On Create Form</label>
                                    <x-toggle-button id="show-on-create" model="input.showCreate"/>
                                </div>

                                <div class="form-group">
                                    <label>Show On Edit Form</label>
                                    <x-toggle-button id="show-on-edit" model="input.showEdit"/>
                                </div>

                                <div class="form-group">
                                    <label>Show On Detail Page</label>
                                    <x-toggle-button id="show-on-detail" model="input.showDetail"/>
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" class="btn btn-default" @click="openModalType = false">
                                        Cancel
                                    </button>
                                    <button type="button" wire:click="saveInput" @click="openModalType = false"
                                            class="btn btn-primary">Add to Form
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- END FORM DESIGN -->
                        <div class="w-full mt-4">
                            <div class="flex justify-end space-x-2">
                                <a href="{{getCmsUrl('module-builder')}}" wire:navigate
                                   class="btn btn-default">Cancel</a>
                                <button class="btn btn-primary relative"
                                        wire:loading.attr="disabled"
                                        wire:target="formSave"
                                        type="submit">Save &raquo;
                                    @if($changed)
                                        <span class="ping-bullet"></span>
                                        <span class="ping-bullet-wave"></span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
