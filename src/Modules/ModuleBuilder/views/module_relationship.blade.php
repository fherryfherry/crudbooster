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
                    <h3 class="panel-title">Relationship</h3>
                </div>
                <form id="form-data" method="POST" wire:submit.prevent="formSave">
                    @csrf
                    <div class="panel-content">
                        <div class="alert-simple alert-info">
                            <strong>Info</strong>. You can add multiple relation to join multiple table. The first
                            relation will be the main relation.
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Key</th>
                                <th>Table 1st</th>
                                <th>Field</th>
                                <th>Op.</th>
                                <th>Table 2nd</th>
                                <th>Field</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($relationships??[] as $index => $relation)
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" wire:model="relationships.{{$index}}.key"
                                                   class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model.live="relationships.{{$index}}.tableFirst"
                                                    class="form-control">
                                                <option value="">- Select a Table -</option>
                                                @foreach($this->tableList as $table)
                                                    <option value="{{$table}}">{{$table}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model="relationships.{{$index}}.firstField"
                                                    class="form-control">
                                                <option value="">- Select a Field -</option>
                                                @foreach(($relation['tableFirstFields']??[]) as $field)
                                                    <option value="{{$field}}">{{$field}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model="relationships.{{$index}}.operator" class="form-control">
                                                <option value="">- Select an Operator -</option>
                                                <option value="=">=</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value=">=">>=</option>
                                                <option value="<="><=</option>
                                                <option value="!=">!=</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model.live="relationships.{{$index}}.tableSecond"
                                                    class="form-control">
                                                <option value="">- Select a Table -</option>
                                                @foreach($this->tableSecondList as $table)
                                                    <option value="{{$table['key']}}">{{$table['key']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model="relationships.{{$index}}.secondField"
                                                    class="form-control">
                                                <option value="">- Select a Field -</option>
                                                @foreach(($relation['tableSecondFields']??[]) as $field)
                                                    <option value="{{$field}}">{{$field}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select wire:model="relationships.{{$index}}.type" class="form-control">
                                                <option value="">- Select a Type -</option>
                                                <option value="inner">Inner</option>
                                                <option value="left">Left</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" title="Remove relation"
                                                wire:click="removeRelation({{$index}})">{!! Icon::TRASH !!}</button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="8" class="text-center">
                                    <button type="button" wire:click="addRelation" class="btn btn-primary">
                                        <span class="flex items-center gap-1">{!! Icon::PLUS !!} Add Relation</span>
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
