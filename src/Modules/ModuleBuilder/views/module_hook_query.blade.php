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
                    <h3 class="panel-title">Hook Query Condition</h3>
                </div>
                <form id="form-data" method="POST" wire:submit.prevent="formSave">
                    @csrf
                    <div class="panel-content">
                        <div class="alert-simple alert-info">
                            <strong>Info</strong>. You can add a query to filter the data that will be displayed in the
                            module.
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Field</th>
                                <th>Operator</th>
                                <th>Value</th>
                                <th>Group</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($columns??[] as $index => $column)
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model="columns.{{$index}}.type" class="form-control">
                                                <option value="">- Select a Type -</option>
                                                <option value="AND">AND</option>
                                                <option value="OR">OR</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model.live="columns.{{$index}}.field" class="form-control">
                                                @foreach($this->fieldGroup as $group)
                                                    <optgroup label="{{$group['table']}}">
                                                        @foreach($group['fields'] as $field)
                                                            <option
                                                                value="{{$group['table'].'.'.$field}}">{{$field}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model="columns.{{$index}}.operator" class="form-control">
                                                <option value="">- Select an Operator -</option>
                                                <option value="=">=</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value=">=">>=</option>
                                                <option value="<="><=</option>
                                                <option value="!=">!=</option>
                                                <option value="like">Like</option>
                                                <option value="not like">Not Like</option>
                                                <option value="in">In</option>
                                                <option value="not in">Not In</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" wire:model="columns.{{$index}}.value"
                                                   class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" wire:model="columns.{{$index}}.group">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" title="Remove query"
                                                wire:click="removeQuery({{$index}})">{!! Icon::TRASH !!}</button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="8" class="text-center">
                                    <button type="button" wire:click="addQuery" class="btn btn-primary">
                                        <span class="flex items-center gap-1">{!! Icon::PLUS !!} Add Query</span>
                                    </button>
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
