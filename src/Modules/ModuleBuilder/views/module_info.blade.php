<div>
    @if(isset($confirmTitle))
        {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
    @endif
    <x-header pageTitle="Module Builder"/>

    @include("cb.module-builder::module_top_button")

    <div class="flex flex-col lg:flex-row items-start justify-between gap-3 text-gray-600">
        <div class="button-steps w-full lg:w-[400px]">
            @include("cb.module-builder::module_sidebar")
        </div>
        <div class="content w-full">
            <div class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">Basic Info</h3>
                </div>
                <form id="form-data" method="POST" wire:submit.prevent="formSave">
                    @csrf
                    <div class="panel-content">
                        <div class="form-group">
                            <label>Module Name</label>
                            <input type="text" placeholder="Input the module name"
                                   wire:model.live.debounce.600ms="form.name" class="form-control">
                            @error('form.name') <span
                                class="text-red-500 text-xs">{{ str_replace('form.','',$message) }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Module URL Path</label>
                            <input type="text" @disabled(!isset($form['name'])) placeholder="Input the module url path"
                                   wire:model="form.path"
                                   class="form-control"
                                   @input="event.target.value = event.target.value.replace(/[^a-zA-Z0-9-_]/g, '')">
                            @error('form.path') <span
                                class="text-red-500 text-xs">{{ str_replace('form.','',$message) }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-4 justify-between">
                            <div class="form-group w-1/2">
                                <label>Table</label>
                                <select class="form-control"
                                        @disabled(!isset($form['name'])) wire:model.live="form.table">
                                    <option value="">** Select a Table Name</option>
                                    <option value="_NEW_">- CREATE NEW TABLE -</option>
                                    @foreach($tableList as $table)
                                        <option value="{{$table}}">{{$table}}</option>
                                    @endforeach
                                </select>
                                @error('form.table') <span
                                    class="text-red-500 text-xs">{{ str_replace('form.','',$message) }}</span> @enderror
                            </div>
                            <div class="form-group w-1/2">
                                @if(($form['table']??'') === '_NEW_')
                                    <label>Table Name</label>
                                    <input
                                        placeholder="{{($form['table']??'') === '_NEW_' ? 'Enter a new table name' : ''}}"
                                        type="text"
                                        @readonly(($form['table']??'') !== '_NEW_') wire:model.live.debounce.600ms="form.table_name"
                                        class="form-control"
                                        @input="event.target.value = event.target.value.replace(/[^a-zA-Z0-9_]/g, '')">
                                    @error('form.table_name') <span
                                        class="text-red-500 text-xs">{{ str_replace('form.','',$message) }}</span> @enderror
                                @endif
                            </div>
                        </div>


                        <div class="flex justify-between gap-4">
                            <div class="form-group w-1/2">
                                <label>Model Class</label>
                                <input type="text" @disabled(!isset($form['name'])) wire:model="form.model"
                                       placeholder="E.g: App\Cb\Models\FooBarModel::class" class="form-control">
                                @error('form.model') <span
                                    class="text-red-500 text-xs">{{ str_replace('form.','',$message) }}</span> @enderror
                            </div>

                            <div class="form-group w-1/2">
                                <label>Service Class</label>
                                <input type="text" @disabled(!isset($form['name'])) wire:model="form.service"
                                       placeholder="E.g: App\Cb\Models\FooBarService::class" class="form-control">
                                @error('form.service') <span
                                    class="text-red-500 text-xs">{{ str_replace('form.','',$message) }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-between gap-4">
                            <div class="form-group w-full lg:w-1/3">
                                <label>Action Button Style</label>
                                <select class="form-control" @disabled(!isset($form['name'])) wire:model="form.button_action_style">
                                    <option value="ICON_ONLY">Icon Only</option>
                                    <option value="ICON_TEXT">Icon & Text</option>
                                    <option value="TEXT_ONLY">Text Only</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name">Browse Button Available</label>
                            <div class="flex flex-wrap mt-2 gap-10">
                                <div class="space-y-2 w-48">
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_create"
                                               class="cursor-pointer">
                                        <span>Create Data</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_filter"
                                               class="cursor-pointer">
                                        <span>Advanced Filter</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_search_bar"
                                               class="cursor-pointer">
                                        <span>Search Bar</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_import"
                                               class="cursor-pointer">
                                        <span>Import</span>
                                    </label>
                                </div>
                                <div class="space-y-2 w-48">
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_export_xls"
                                               class="cursor-pointer">
                                        <span>Export XLSX</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_export_csv"
                                               class="cursor-pointer">
                                        <span>Export CSV</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_export_pdf"
                                               class="cursor-pointer">
                                        <span>Export PDF</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_bulk_action"
                                               class="cursor-pointer">
                                        <span>Bulk Action</span>
                                    </label>
                                </div>
                                <div class="space-y-2 w-48">
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_edit"
                                               class="cursor-pointer">
                                        <span>Edit</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_detail"
                                               class="cursor-pointer">
                                        <span>Detail</span>
                                    </label>
                                    <label class="input-checkbox-group">
                                        <input type="checkbox"
                                               @disabled(!isset($form['name'])) wire:model="form.button_delete"
                                               class="cursor-pointer">
                                        <span>Delete</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name">Permission Available</label>
                            <div class="flex flex-wrap mt-2 gap-10">
                                <label class="input-checkbox-group w-24">
                                    <input type="checkbox"
                                           @disabled(!isset($form['name'])) wire:model="form.permission_create"
                                           class="cursor-pointer">
                                    <span>Create</span>
                                </label>
                                <label class="input-checkbox-group w-24">
                                    <input type="checkbox"
                                           @disabled(!isset($form['name'])) wire:model="form.permission_read"
                                           class="cursor-pointer">
                                    <span>Read</span>
                                </label>
                                <label class="input-checkbox-group w-24">
                                    <input type="checkbox"
                                           @disabled(!isset($form['name'])) wire:model="form.permission_update"
                                           class="cursor-pointer">
                                    <span>Update</span>
                                </label>
                                <label class="input-checkbox-group w-24">
                                    <input type="checkbox"
                                           @disabled(!isset($form['name'])) wire:model="form.permission_delete"
                                           class="cursor-pointer">
                                    <span>Delete</span>
                                </label>
                            </div>
                        </div>

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
