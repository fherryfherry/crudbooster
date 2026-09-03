@php use CrudBooster\Components\Icon\Icon;@endphp
<div>
    @if(isset($confirmTitle))
        {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
    @endif
        <x-header pageTitle="Module Builder"/>
        @include("cb.module-builder::module_top_button")

    <div class="flex h-screen gap-3 text-gray-600 mb-[100px]">
        <div class="button-steps lg:w-[400px]">
            @include("cb.module-builder::module_sidebar")
        </div>
        <div class="content flex-1">
            <div class="panel">
                <div class="panel-header">
                    <div class="item-start">
                        <h3 class="panel-title">Form Hook</h3>
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
                        <div class="form-group">
                            <label>On Form Saving</label>
                            <textarea @disabled(!$status) wire:model="input.onFormSaving" rows="7"
                                      placeholder="For example, we want to make upper case the name input:
$this->formData['name'] = strtoupper($this->formData['name']);"
                                      class="form-control"></textarea>
                            <div class="form-help">
                                This code will be executed before form saved (on process form saving). You can override
                                some variable here.
                            </div>
                        </div>

                        <div class="form-group">
                            <label>On Form Saved</label>
                            <textarea @disabled(!$status) wire:model="input.onFormSaved" rows="7"
                                      placeholder="For example, we want to clear cache after saved:
cache()->forget('cache_name_'.$id);"
                                      class="form-control"></textarea>
                            <div class="form-help">
                                This code will be executed after form saved. If you want to get the unique identifier
                                after the form is saved in this callback, you can use magic variable named
                                <code>$id</code>.
                            </div>
                        </div>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>Common Variables Available</th>
                                <th>Description</th>
                                <th>Callback Available</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><code class="text-red-500">$this->form['...']</code></td>
                                <td>
                                    Contains all form data as an array. You can manipulate the form data here.
                                </td>
                                <td>
                                    {!! Icon::TICK !!} <code>onFormSaving</code> <br/>
                                    {!! Icon::TICK !!} <code>onFormSaved</code>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="text-red-500">$this->formId</code></td>
                                <td class="whitespace-normal">
                                    A unique identifier for the form. If you are editing the form, <br/>this variable
                                    will contain the unique identifier of the form.
                                </td>
                                <td>
                                    {!! Icon::TICK !!} <code>onFormSaving</code> <br/>
                                    {!! Icon::TICK !!} <code>onFormSaved</code>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="text-red-500">$id</code></td>
                                <td class="whitespace-normal">
                                    A unique identifier for the saved data.
                                </td>
                                <td>
                                    {!! Icon::TICK !!} <code>onFormSaved</code>
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
                                        type="submit">Save & Build Module &raquo;
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
