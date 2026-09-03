<div x-data="{open: false}" class="inline-block">
    <button type="button" title="Configuration column" @click="open = true">
        {!! \CrudBooster\Components\Icon\Icon::COG !!}
    </button>
    <div x-show="open" class="modal">
        <div class="modal-content relative w-1/3" @click.away="open = false">
            <h3 class="modal-title pb-4">Config Column</h3>

            <div class="frame">
                <div class="frame-title">Template Format</div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Enable
                    </div>
                    <div class="input w-full">
                        <x-toggle-button id="toggle-transform-template-{{$key}}" type="checkbox"
                                         model="columns.{{ $key }}.config.transformTemplate"
                                         value="1"/>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Prefix
                    </div>
                    <div class="input w-full">
                        <input @disabled(!($column['config']['transformTemplate']??false)) type="text"
                               placeholder="E.g: $"
                               wire:model="columns.{{ $key }}.config.prefix"
                               class="form-control">
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Suffix
                    </div>
                    <div class="input w-full">
                        <input @disabled(!($column['config']['transformTemplate']??false)) type="text"
                               placeholder="E.g: %"
                               wire:model="columns.{{ $key }}.config.suffix"
                               class="form-control">
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="label w-[300px]">
                        Letter Case
                    </div>
                    <div class="input w-full">
                        <select
                            @disabled(!($column['config']['transformTemplate']??false)) wire:model="columns.{{ $key }}.config.letterCase"
                            class="form-control">
                            <option value="">Default</option>
                            <option value="uppercase">Uppercase</option>
                            <option value="lowercase">Lowercase</option>
                            <option value="capitalize">Capitalize</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="frame">
                <div class="frame-title">Transform Date Format</div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Enable
                    </div>
                    <div class="input w-full">
                        <x-toggle-button id="toggle-transform-date-format-{{$key}}" type="checkbox"
                                         model="columns.{{ $key }}.config.transformDateFormat"
                                         value="1"/>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="label w-[300px]">
                        Format
                    </div>
                    <div class="input w-full">
                        <input wire:model="columns.{{ $key }}.config.dateFormat"
                               type="text"
                               @disabled(!($column['config']['transformDateFormat']??false)) class="form-control">
                    </div>
                </div>
            </div>

            <div class="frame">
                <div class="frame-title">Transform Image Thumbnail</div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Enable
                    </div>
                    <div class="input w-full">
                        <x-toggle-button id="toggle-image-{{$key}}" type="checkbox"
                                         model="columns.{{ $key }}.config.transformImage"
                                         value="1"/>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Style
                    </div>
                    <div class="input w-full">
                        <select wire:model="columns.{{$key}}.config.style"
                                @disabled(!($column['config']['transformImage']??false)) class="form-control">
                            <option value="rounded">Rounded</option>
                            <option value="circle">Circle</option>
                            <option value="square">Square</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Size
                    </div>
                    <div class="input w-full">
                        <div class="flex justify-between gap-2 items-center">
                            <input type="number" placeholder="Width (px)"
                                   @disabled(!($column['config']['transformImage']??false)) class="form-control"
                                   wire:model="columns.{{$key}}.config.imageWidth">
                            <span>x</span>
                            <input type="number" placeholder="Height (px)"
                                   @disabled(!($column['config']['transformImage']??false)) class="form-control"
                                   wire:model="columns.{{$key}}.config.imageHeight">
                        </div>
                    </div>
                </div>
            </div>

            <div class="frame">
                <div class="frame-title">Transform Formatted Number</div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Enable
                    </div>
                    <div class="input w-full">
                        <x-toggle-button id="toggle-number-format-{{$key}}"
                                         type="checkbox"
                                         model="columns.{{ $key }}.config.transformNumberFormat"
                                         value="1"/>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Decimal
                    </div>
                    <div class="input w-full">
                        <input wire:model="columns.{{ $key }}.config.decimal"
                               type="number"
                               @disabled(!($column['config']['transformNumberFormat']??false)) class="form-control">
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Decimal Separator
                    </div>
                    <div class="input w-full">
                        <input
                            wire:model="columns.{{ $key }}.config.decimalSeparator"
                            type="text"
                            @disabled(!($column['config']['transformNumberFormat']??false)) class="form-control">
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="label w-[300px]">
                        Thousand Separator
                    </div>
                    <div class="input w-full">
                        <input
                            wire:model="columns.{{ $key }}.config.thousandSeparator"
                            type="text"
                            @disabled(!($column['config']['transformNumberFormat']??false)) class="form-control">
                    </div>
                </div>
            </div>

            <div class="frame">
                <div class="frame-title">Transform Link</div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Enable
                    </div>
                    <div class="input w-full">
                        <x-toggle-button id="toggle-transform-link-{{$key}}"
                                         type="checkbox"
                                         model="columns.{{ $key }}.config.transformLink"
                                         value="1"/>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        URL
                    </div>
                    <div class="input w-full">
                        <input wire:model="columns.{{ $key }}.config.url"
                               type="url"
                               @disabled(!($column['config']['transformLink']??false)) placeholder="E.g: https://yourexternal.com/{uuid}"
                               class="form-control">
                        <div class="form-help">
                            <span class="text-xs text-gray-500">Available variable: {uuid}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="label w-[300px]">
                        Target
                    </div>
                    <div class="input w-full">
                        <select
                            wire:model="columns.{{ $key }}.config.target"
                            @disabled(!($column['config']['transformLink']??false)) class="form-control">
                            <option value="_self">_self</option>
                            <option value="_blank">_blank</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
