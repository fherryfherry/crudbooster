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
                        <h3 class="panel-title">Action Row Button</h3>
                    </div>
                    <div class="item-end">
                        <div class="flex items-center">
                            <x-toggle-button id="toggle" model="status"/>
                        </div>
                    </div>
                </div>
                <form id="form-data" method="POST" wire:submit.prevent="formSave">
                    @csrf
                    <div class="panel-content">
                        <div class="alert-simple alert-info">
                            <strong>Info</strong>. This feature is used to add a button in the action row of the table.
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                                <th class="w-1/6">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($columns??[] as $index => $column)
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input @disabled(!$status) type="text" wire:model="columns.{{$index}}.label"
                                                   class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input @disabled(!$status) type="text"
                                                   placeholder="E.g: /your-module/custom-action/{id}"
                                                   wire:model="columns.{{$index}}.url"
                                                   class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div x-data="{open: false}" class="form-group inline-block">
                                            <a href="javascript:" title="Button option"
                                               @click="open = true">{!! Icon::COG !!}</a>
                                            <div x-show="open" class="modal">
                                                <div class="modal-content w-1/3">
                                                    <h3 class="modal-title">Button Option</h3>

                                                    <div class="form-group">
                                                        <label>Icon</label>
                                                        <select wire:model="columns.{{$index}}.icon"
                                                                class="form-control">
                                                            <option value="">- Select an Icon -</option>
                                                            @foreach($this->iconList as $icon)
                                                                <option value="{{$icon}}">{{$icon}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Class</label>
                                                        <input @disabled(!$status) placeholder="E.g: btn btn-primary"
                                                               type="text" wire:model="columns.{{$index}}.class"
                                                               class="form-control">
                                                        <div class="form-help">
                                                            Available: btn-primary, btn-info, btn-success, btn-warning, btn-danger. Don't forget to add btn class for first class. Or if you include your own CSS, you may use your own class.
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Confirmation</label>
                                                        <div class="flex items-center gap-4">
                                                            <label class="input-radio-group">
                                                                <input type="radio"
                                                                       wire:model="columns.{{$index}}.confirm"
                                                                       value="1"> Yes
                                                            </label>
                                                            <label class="input-radio-group">
                                                                <input type="radio"
                                                                       wire:model="columns.{{$index}}.confirm"
                                                                       value="0"> No
                                                            </label>
                                                        </div>
                                                        <small class="text-gray-400 text-xs">Confirmation message before
                                                            action</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Permission</label>
                                                        <select wire:model="columns.{{$index}}.permission"
                                                                class="form-control">
                                                            <option value="create">Create</option>
                                                            <option value="read">Read</option>
                                                            <option value="update">Update</option>
                                                            <option value="delete">Delete</option>
                                                        </select>
                                                        <small class="text-gray-400 text-xs">
                                                            Permission to show this button
                                                        </small>
                                                    </div>

                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" class="btn btn-default"
                                                                x-on:click="open = false">Close
                                                        </button>
                                                        <button @disabled(!$status) type="button" class="btn btn-info"
                                                                @click="open = false">Apply
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group text-center inline-block">
                                            <button @disabled(!$status) type="button" title="Remove button"
                                                    wire:click="removeColumn({{$index}})">{!! Icon::TRASH !!}</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4" class="text-center">
                                    <button @disabled(!$status) type="button" wire:click="addColumn"
                                            class="btn btn-primary">
                                        <span class="flex items-center gap-1">{!! Icon::PLUS !!} Add Button</span>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="w-full mt-4">
                            <div class="flex justify-end space-x-2">
                                <a href="{{getCmsUrl('module-builder')}}" wire:navigate
                                   class="btn btn-default">Cancel</a>
                                <button class="btn btn-primary" wire:loading.attr="disabled"
                                        wire:target="formSave"
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
