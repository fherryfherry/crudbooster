<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Title</label>
            <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse"
                class="form-control" wire:model="form.title">
            <div class="form-help">Enter the title widget</div>
            @error('form.title') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="">Icon</label>
            <livewire:select-icon wire:model="form.icon"/>
        </div>
        <div class="form-group">
            <label>Chart Type</label>
            <select wire:model="form.chartType" class="form-control">
                <option value="">- Select a Chart Type -</option>
                <option value="line">Line</option>
                <option value="bar">Bar</option>
                <option value="radar">Radar</option>
                <option value="bubble">Bubble</option>
                <option value="doughnut">Doughnut</option>
                <option value="pie">Pie</option>
                <option value="polarArea">Polar Area</option>
            </select>
        </div>
        <!-- option chart: monthly, yearly, etc -->
        <div class="form-group">
            <label>Chart X Axis</label>
            <select wire:model.live="form.dataType" class="form-control">
                <option value="">- Select a Type -</option>
                <option value="YEARLY">Yearly</option>
                <option value="SEMIANNUALLY">Semiannually / Semester</option>
                <option value="QWARTERLY">Qwarterly</option>
                <option value="MONTHLY">Monthly</option>
                <option value="WEEKLY">Weekly</option>
                <option value="DAILY">Daily</option>
                <option value="BYDAY">By Day</option>
                <option value="HOURLY">Hourly</option>
            </select>
            <div class="form-help">
                Choose monthly for x-axis data in months, yearly for x-axis data in years. E.g: January, February, etc,
                or 2021, 2022, etc.
            </div>
        </div>

        <div class="flex items-center gap-2 mb-4">
            <div class="form-group w-full">
                <label>Min X Axis</label>
                <input type="{{isset($form['dataType']) && $form['dataType'] == 'DAILY'?'date':'number'}}" min="1"
                    placeholder="E.g: 1" inputmode="numeric" wire:model="form.minXAxis" class="form-control">
                <div class="form-help">
                    The minimum value of the x-axis. E.g: 1 for January, 2020 for Yearly, etc
                </div>
            </div>
            <div class="form-group w-full">
                <label>Max X Axis</label>
                <input type="{{isset($form['dataType']) && $form['dataType'] == 'DAILY'?'date':'number'}}"
                    placeholder="E.g: 1" inputmode="numeric" wire:model="form.maxXAxis" class="form-control">
                <div class="form-help">
                    The maximum value of the x-axis. E.g: 1 for January, 2020 for Yearly, etc
                </div>
            </div>
            @if(isset($form['dataType']) && $form['dataType'] != 'YEARLY' && $form['dataType'] != 'DAILY')
            <div class="form-group w-full">
                <label>Year</label>
                <input type="number" placeholder="E.g: 2020" inputmode="numeric" wire:model="form.yearXAxis"
                    class="form-control">
                <div class="form-help">
                    The year value of the x-axis. E.g: 2020.
                </div>
            </div>
            @endif
        </div>
        <!-- config for indexAxis / Horizontal Bar -->
        @if($form['chartType'] == 'bar')
        <div class="form-group">
            <label>Index Axis</label>
            <select wire:model="form.indexAxis" class="form-control">
                <option value="x">No</option>
                <option value="y">Yes</option>
            </select>
            <div class="form-help">
                If you want to make the chart as a horizontal bar, choose Yes
            </div>
        </div>
        @endif

        @foreach($form['datasets']??[] as $index=>$dataset)
        <div wire:key='{{$index}}' class="frame">
            <div class="frame-title">Dataset {{$index}}</div>

            <div class="form-group">
                <label for="">Label</label>
                <input type="text" wire:model="form.datasets.{{$index}}.label" class="form-control">
                <div class="form-help">Label of chart</div>
            </div>
            <div class="form-group">
                <label>Data Query</label>
                <div class="flex items-center gap-1">
                    <select wire:model="form.datasets.{{$index}}.query" wire:loading.attr="disabled" wire:target="save"
                        wire:loading.class="animate-pulse" class="form-control">
                        <option value="">- Select a Query -</option>
                        @foreach($this->queryBuilderList as $query)
                        <option value="{{ $query['id'] }}">{{ $query['name'] }}</option>
                        @endforeach
                    </select>
                    <a href="{{getCmsUrl('query-builder/create')}}" title="Add new query" target="_blank"
                        class="btn btn-outline-light">
                        {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
                    </a>
                    <a href="javascript:" wire:click="$refresh" title="Refresh" class="btn btn-outline-light">
                        {!! \CrudBooster\Components\Icon\Icon::REFRESH !!}
                    </a>
                </div>

                <div class="form-help">
                    You have to define the query in the Query Builder module
                </div>
            </div>

            <div class="form-group">
                <label for="">Comparator Field</label>
                <input type="text" wire:model="form.datasets.{{$index}}.comparatorField" class="form-control">
                <div class="form-help">
                    The comparator field from data query. E.g: The field that you want to compare
                </div>
            </div>
            @if(isset($form['dataType']) && $form['dataType'] != 'YEARLY' && $form['dataType'] != 'DAILY')
            <div class="form-group w-full">
                <label>Year Field</label>
                <input type="text" inputmode="numeric" wire:model="form.datasets.{{$index}}.yearField"
                    class="form-control">
                <div class="form-help">
                    The year field from data query.
                </div>
            </div>
            @endif

            <div class="form-group">
                <label for="">Point Field</label>
                <input type="text" wire:model="form.datasets.{{$index}}.pointField" class="form-control">
                <div class="form-help">The point field from data query</div>
            </div>

            <div class="flex justify-start items-center gap-2">
                <!-- background color config -->
                <div class="form-group w-full">
                    <label>Background Color</label>
                    <input type="color" wire:model="form.datasets.{{$index}}.backgroundColor" class="form-control">
                    <div class="form-help">Choose a background color for the chart</div>
                </div>
                <!-- border color config -->
                <div class="form-group w-full">
                    <label>Border Color</label>
                    <input type="color" wire:model="form.datasets.{{$index}}.borderColor" class="form-control">
                    <div class="form-help">Choose a border color for the chart</div>
                </div>
            </div>
            <div class="flex justify-start items-start gap-2">
                <div class="form-group w-full">
                    <label for="">Border Width</label>
                    <input type="number" wire:model="form.datasets.{{$index}}.borderWidth" class="form-control">
                    <div class="form-help">Border width for the chart</div>
                </div>
                <!-- config borderRadius -->
                <div class="form-group w-full">
                    <label for="">Border Radius</label>
                    <input type="number" wire:model="form.datasets.{{$index}}.borderRadius" class="form-control">
                    <div class="form-help">Border radius for the chart</div>
                </div>
            </div>
            @if($form['chartType'] == 'line' || $form['chartType'] == 'radar')
            <div class="form-group justify-start">
                <label for="">Fill</label>
                <select wire:model="form.datasets.{{$index}}.fill" class="form-control">
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>
                <div class="form-help">Fill the chart with background color</div>
            </div>
            @endif
            <!-- config stack name -->
            <div class="form-group">
                <label for="">Stack Name</label>
                <input type="text" placeholder="Optional" wire:model="form.datasets.{{$index}}.stack"
                    class="form-control">
                <div class="form-help">Stack name for the chart. Leave empty if doesn't want to stacked</div>
            </div>

            <button type="button" wire:click='removeDataset({{$index}})' title='Remove dataset'
                class="btn btn-outline-danger">
                {!! \CrudBooster\Components\Icon\Icon::TRASH !!}
            </button>

        </div>
        @endforeach


        <div class="flex justify-end gap-1">
            <button type="button" wire:click="addDataset" class="btn btn-outline-primary">Add Dataset</button>
            <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
