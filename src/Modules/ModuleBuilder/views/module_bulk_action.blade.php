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
                        <h3 class="panel-title">Bulk Action</h3>
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
                            <strong>Info</strong>. Bulk action is a feature to perform an action to multiple data at
                            once.
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Label</th>
                                <th>Icon</th>
                                <th>Action</th>
                                <th>Confirmation</th>
                                <th>Permission</th>
                                <th>Delete</th>
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
                                            <select @disabled(!$status) wire:model.live="columns.{{$index}}.icon"
                                                    class="form-control">
                                                @foreach($this->iconList as $icon)
                                                    <option value="{{$icon}}">{{$icon}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group inline-block">
                                            <select @disabled(!$status) wire:model.live="columns.{{$index}}.action"
                                                    class="form-control">
                                                <option value="">- Select an Action -</option>
                                                @foreach($this->actionList as $action)
                                                    <option value="{{$action}}">{{$action}}</option>
                                                @endforeach
                                                <option value="_CUSTOM_">Custom Callback</option>
                                            </select>
                                        </div>
                                        @if($columns[$index]['action'] === '_CUSTOM_')
                                            <div x-data="{open: false}" class="inline-block">
                                                <a href="javascript:" @click="open = true">{!! Icon::COG !!}</a>
                                                <div x-show="open" class="modal">
                                                    <div class="modal-content w-1/3">
                                                        <h3 class="modal-title">Custom Callback</h3>
                                                        <div class="form-group">
                                                            <p class="whitespace-normal py-2 text-gray-500 text-sm">
                                                                You can write a custom callback to perform an action.
                                                                Use var <code class="text-red-500">$ids</code> to get
                                                                the selected ids.
                                                            </p>
                                                            <textarea @disabled(!$status)
                                                                      wire:model="columns.{{$index}}.actionCustomCallback"
                                                                      rows="5" class="form-control"
                                                                      placeholder="E.g:
\DB::table('tableName')->whereIn('id',$ids)->delete();"></textarea>
                                                        </div>

                                                        <div class="flex justify-end space-x-2">
                                                            <button type="button" class="btn btn-default"
                                                                    x-on:click="open = false">Close
                                                            </button>
                                                            <button @disabled(!$status) type="button"
                                                                    class="btn btn-info"
                                                                    @click="open = false">Apply
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td x-data="{open: false}">
                                        <div class="form-group text-center">
                                            <a href="javascript:" @click="open = true">{!! Icon::PENCIL !!}</a>
                                        </div>
                                        <div x-show="open" class="modal">
                                            <div class="modal-content w-[500px]">
                                                <h3 class="modal-title">Confirmation Dialog</h3>
                                                <div class="alert-simple alert-info">
                                                    <strong>Tips</strong> Leave it empty if you don't want to show a
                                                    confirmation dialog.
                                                </div>
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input @disabled(!$status) type="text"
                                                           wire:model="columns.{{$index}}.confirmation.title"
                                                           class="form-control" placeholder="Title">
                                                </div>
                                                <div class="form-group">
                                                    <label>Message</label>
                                                    <textarea
                                                        @disabled(!$status) wire:model="columns.{{$index}}.confirmation.message"
                                                        class="form-control" rows="3"
                                                        placeholder="Message"></textarea>
                                                </div>

                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" class="btn btn-default"
                                                            x-on:click="open = false">Close
                                                    </button>
                                                    <button @disabled(!$status) type="button" class="btn btn-info"
                                                            @click="open = false">
                                                        Apply
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select @disabled(!$status) wire:model.live="columns.{{$index}}.permission"
                                                    class="form-control">
                                                <option value="">- Select a Permission -</option>
                                                <option value="CREATE">Create</option>
                                                <option value="READ">Read</option>
                                                <option value="UPDATE">Update</option>
                                                <option value="DELETE">Delete</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group text-center">
                                            <button @disabled(!$status) type="button" title="Remove action"
                                                    wire:click="removeColumn({{$index}})">{!! Icon::TRASH !!}</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="8" class="text-center">
                                    <button @disabled(!$status) type="button" wire:click="addColumn"
                                            class="btn btn-primary">
                                        <span class="flex items-center gap-1">{!! Icon::PLUS !!} Add New Action</span>
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
