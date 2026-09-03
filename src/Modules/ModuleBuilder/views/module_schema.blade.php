@php use CrudBooster\Components\Icon\Icon;use Illuminate\Support\Facades\DB; @endphp
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
                    <h3 class="panel-title">Database Table Schema
                        <strong>`{{$form['table_name'] ?? $form['table'] ?? ''}}
                            `</strong></h3>
                </div>
                <form id="form-data" method="POST" wire:submit.prevent="formSave">
                    @csrf
                    <div class="panel-content">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Column Name</th>
                                <th>Data Type</th>
                                <th class="text-center">
                                    <div class="text-center">Primary Key</div>
                                </th>
                                <th class="text-center">
                                    <div class="text-center">Auto Increment</div>
                                </th>
                                <th class="text-center">
                                    <div class="text-center">Action</div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               placeholder="E.g: name"
                                               @input="event.target.value = event.target.value.replace(/[^a-zA-Z0-9_]/g, '')"
                                               @keyup.enter="$wire.addColumn"
                                               wire:model="columnName">
                                        <div class="form-help">Enter new field name</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select class="form-control" wire:model.live="columnDataType">
                                            @foreach(getLaravelSchemaTypeList() as $type)
                                                <option value="{{$type}}">{{$type}}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-help">Select a field data type</div>
                                    </div>
                                </td>
                                <td></td>
                                <td>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:" title="Add column to table" class="text-green-500"
                                       wire:click="addColumn">{!! Icon::PLUS !!}</a>
                                </td>
                            </tr>
                            @foreach($columns as $key => $column)
                                <tr wire:key="{{$key}}">
                                    <td>
                                        <div class="form-group">
                                            <input type="text"
                                                   @readonly($column['disabled'] ?? false) class="form-control"
                                                   @input="event.target.value = event.target.value.replace(/[^a-zA-Z0-9_]/g, '')"
                                                   wire:model="columns.{{$key}}.name">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select @disabled($column['disabled'] ?? false) class="form-control"
                                                    wire:model="columns.{{$key}}.type">
                                                @foreach(getLaravelSchemaTypeList() as $type)
                                                    <option value="{{$type}}">{{$type}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex justify-center">
                                            <input type="radio" wire:model="form.primaryKey" value="{{$column['name']??''}}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <input type="checkbox" wire:model="columns.{{$key}}.config.autoIncrement">
                                        </div>
                                    </td>
                                    <td>
                                        @if(! ($column['disabled'] ?? false))
                                            <div class="text-center">
                                                <a href="javascript:"
                                                   wire:click="removeColumn('{{$column['name']??''}}')"
                                                >{!! Icon::DELETE !!}</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        <div class="w-full">
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
