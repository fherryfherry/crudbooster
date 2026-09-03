<div>
    <div class="mb-4" wire:ignore>
        @include('cb.query-builder::form_top')
    </div>

    <div x-data="queryBuilder()" class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Query Builder Form</h3>
        </div>
        <form id="form-data" method="POST" wire:submit.prevent="formSave">
            @csrf
            <div class="panel-content">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" wire:model="name">
                    <div class="form-help">Enter the name of the function</div>
                    @error('name')
                    <div class="form-feedback text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Builder Mode</label>
                    <select wire:model.live="builderMode" class="form-control">
                        <option value="">- Select a Builder Mode -</option>
                        <option value="QUERY_BUILDER">Query Builder</option>
                        <option value="QUERY_RAW">Query Raw</option>
                    </select>
                    <div class="form-help">
                        Choose the mode of the query builder
                    </div>
                    @error('builderMode')
                    <div class="form-feedback text-red-500 text-sm">{{ $message }}</div> @enderror
                </div>
                @if($builderMode == 'QUERY_RAW')
                    <div class="form-group">
                        <label>Raw Query</label>
                        <textarea class="form-control" wire:model="rawQuery"></textarea>
                        <div class="form-help">Enter the raw query</div>
                        @error('rawQuery')
                        <div class="form-feedback text-red-500 text-sm">{{ $message }}</div> @enderror
                    </div>
                @else
                    <div class="form-group">
                        <label>Model Table</label>
                        <select class="form-control" wire:model="modelName"
                                @change="$wire.changeModel($event.target.value)">
                            <option value="">-- Select a Model --</option>
                            @foreach($modelList as $model)
                                <option value="{{ $model }}">{{ $model }}</option>
                            @endforeach
                        </select>
                        @error('modelName')
                        <div class="form-feedback text-red-500 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group space-y-2">
                        <div class="flex justify-between items-center">
                            <label>Relationships</label>
                            <button type="button" wire:click="addRelationship" class="btn btn-outline-light">{!!
                            \CrudBooster\Components\Icon\Icon::PLUS !!} Add
                                Relationship
                            </button>
                        </div>
                        @if(count($relationships) == 0)
                            <div class="alert-simple alert-info text-center">No relationships added. <a
                                    class="hover:font-bold"
                                    wire:click="addRelationship" href="javascript:">Click here to
                                    add new</a></div>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>First Table</th>
                                    <th>First Field</th>
                                    <th>Operator</th>
                                    <th>Second Table</th>
                                    <th>Second Field</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($relationships as $index => $relationship)
                                    <tr>
                                        <td>
                                            <input type="text" placeholder="E.g: categories"
                                                   wire:model="relationships.{{ $index }}.first_table"
                                                   class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" placeholder="E.g: id"
                                                   wire:model="relationships.{{ $index }}.first_field"
                                                   class="form-control">
                                        </td>
                                        <td>
                                            <select wire:model="relationships.{{ $index }}.operator"
                                                    class="border rounded p-2">
                                                <option value="">-- Select Operator --</option>
                                                <option value="=">=</option>
                                                <option value="!=">!=</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" placeholder="E.g: articles"
                                                   wire:model="relationships.{{ $index }}.second_table"
                                                   class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" placeholder="E.g: category_id"
                                                   wire:model="relationships.{{ $index }}.second_field"
                                                   class="form-control">
                                        </td>
                                        <td>
                                            <button type="button" wire:click="removeRelationship({{ $index }})"
                                                    class="btn btn-outline-danger">{!! \CrudBooster\Components\Icon\Icon::DELETE !!}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- empty state -->
                                @if(count($relationships) === 0)
                                    <tr>
                                        <td colspan="6">No relationships added</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="form-group space-y-2">
                        <div class="flex justify-between items-center">
                            <label>Condition</label>
                            <button type="button" wire:click="addConditionGroup" class="btn btn-outline-light">
                                {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
                                Add Condition Group
                            </button>
                        </div>

                        @foreach($conditionGroups as $groupIndex => $group)
                            <div class="mb-4 p-4 border shadow-md rounded-lg space-y-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <select wire:model="conditionGroups.{{ $groupIndex }}.group_type"
                                                class="form-control">
                                            <option value="AND">AND</option>
                                            <option value="OR">OR</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-between items-center gap-1">
                                        <button type="button" title="Remove Group"
                                                wire:click="removeConditionGroup({{ $groupIndex }})"
                                                class="btn btn-outline-danger">
                                            {!! \CrudBooster\Components\Icon\Icon::DELETE !!}
                                            Remove Group
                                        </button>
                                        <button type="button" title="Add Condition"
                                                wire:click="addConditionToGroup({{ $groupIndex }})"
                                                class="btn btn-outline-light">
                                            {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
                                            Add Condition
                                        </button>
                                    </div>
                                </div>


                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Field</th>
                                        <th>Operator</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($group['conditions'] as $index => $condition)
                                        <tr>
                                            <td>
                                                <select
                                                    wire:model="conditionGroups.{{ $groupIndex }}.conditions.{{ $index }}.type"
                                                    class="form-control">
                                                    <option value="where">Where</option>
                                                    <option value="orWhere">Or Where</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select
                                                    wire:model="conditionGroups.{{ $groupIndex }}.conditions.{{ $index }}.field"
                                                    class="form-control">
                                                    <option value="">-- Select Field --</option>
                                                    @foreach($columns as $column)
                                                        <option value="{{ $column }}">{{ $column }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select
                                                    wire:model.live="conditionGroups.{{ $groupIndex }}.conditions.{{ $index }}.operator"
                                                    class="form-control">
                                                    <option value="">-- Select Operator --</option>
                                                    <option value="=">=</option>
                                                    <option value="!=">!=</option>
                                                    <option value=">">></option>
                                                    <option value="<"><</option>
                                                    <option value=">=">>=</option>
                                                    <option value="<="><=</option>
                                                    <option value="LIKE">LIKE</option>
                                                    <option value="NOT LIKE">NOT LIKE</option>
                                                    <option value="IN">IN</option>
                                                    <option value="NOT IN">NOT IN</option>
                                                    <option value="BETWEEN">BETWEEN</option>
                                                    <option value="NOT BETWEEN">NOT BETWEEN</option>
                                                    <option value="IS NULL">IS NULL</option>
                                                    <option value="IS NOT NULL">IS NOT NULL</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"
                                                       wire:model="conditionGroups.{{ $groupIndex }}.conditions.{{ $index }}.value"
                                                       @if($conditionGroups[$groupIndex]['conditions'][$index]['operator'] == 'IN' || $conditionGroups[$groupIndex]['conditions'][$index]['operator'] == 'NOT IN')
                                                           placeholder="E.g: 1,2,3"
                                                         @elseif($conditionGroups[$groupIndex]['conditions'][$index]['operator'] == 'BETWEEN' || $conditionGroups[$groupIndex]['conditions'][$index]['operator'] == 'NOT BETWEEN')
                                                              placeholder="E.g: 1,10"
                                                            @else
                                                                placeholder="E.g: 100"
                                                            @endif

                                                       @if($conditionGroups[$groupIndex]['conditions'][$index]['operator'] == 'IS NULL' || $conditionGroups[$groupIndex]['conditions'][$index]['operator'] == 'IS NOT NULL')
                                                              disabled
                                                         @endif
                                                       class="form-control">
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- empty state -->
                                    @if(count($group['conditions']) === 0)
                                        <tr>
                                            <td colspan="4">No conditions added</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                        <!-- empty state -->
                        @if(count($conditionGroups) === 0)
                            <div class="alert-simple alert-info text-center">No condition groups added. <a
                                    class="hover:font-bold" wire:click="addConditionGroup" href="javascript:">Click here
                                    to
                                    add new</a></div>
                        @endif
                    </div>

                    <div class="flex justify-start items-center gap-4">
                        <div class="form-group">
                            <label>Order By</label>
                            <select wire:model="orderByColumn" id="orderByColumn" class="form-control">
                                <option value="">-- Select Column --</option>
                                @foreach($columns as $column)
                                    <option value="{{ $column }}">{{ $column }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Direction</label>
                            <select wire:model="orderByDirection" class="form-control">
                                <option value="asc">Ascending</option>
                                <option value="desc">Descending</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Grouping</label>
                        <div class="alert-simple alert-info">You need to disable strict mode on sql config</div>
                        <div class="flex flex-wrap gap-3">
                            @foreach($columns as $index => $column)
                                @if($index % 5 === 0 && $index !== 0)
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @endif
                            <label class="input-checkbox-group">
                                <input type="checkbox" class="form-check-input" wire:model="groupByColumns"
                                       value="{{ $column }}"> <span>{{ $column }}</span>
                            </label>
                            @endforeach
                        </div>

                    </div>

                    <div class="form-group space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="antialiased text-sm text-gray-500">Having</span>
                            <button type="button" wire:click="addHavingCondition" class="btn btn-outline-light">
                                {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
                                <span>Add Having Condition</span>
                            </button>
                        </div>
                        @if(count($havingConditions) > 0)
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Operator</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($havingConditions as $index => $having)
                                    <tr>
                                        <td>
                                            <input list="fields" placeholder="E.g: amount_user" class="form-control"
                                                   wire:model="havingConditions.{{ $index }}.field">
                                            <datalist id="fields">
                                                <option value="">-- Select Field --</option>
                                                @foreach($columns as $column)
                                                    <option value="{{ $column }}">{{ $column }}</option>
                                                @endforeach
                                            </datalist>
                                        </td>
                                        <td>
                                            <select class="form-control"
                                                    wire:model="havingConditions.{{ $index }}.operator">
                                                <option value="">-- Select Operator --</option>
                                                <option value="=">=</option>
                                                <option value="!=">!=</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value=">=">>=</option>
                                                <option value="<="><=</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" placeholder="E.g: 100"
                                                   wire:model="havingConditions.{{ $index }}.value"
                                                   class="form-control">
                                        </td>
                                        <td>
                                            <button type="button" wire:click="removeHavingCondition({{ $index }})"
                                                    class="btn btn-outline-danger">
                                                {!! \CrudBooster\Components\Icon\Icon::DELETE !!}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                        <!-- empty state -->
                        @if(count($havingConditions) === 0)
                            <div class="alert-simple alert-info text-center">No having conditions added. <a
                                    class="hover:font-bold" wire:click="addHavingCondition" href="javascript:">Click
                                    here to
                                    add new</a></div>
                        @endif
                    </div>

                    <div wire:ignore class="form-group">
                        <label>Output Type</label>
                        <div class="flex justify-start gap-3">
                            <label class="input-radio-group">
                                <input type="radio" wire:model.live="aggregationType" value="ARRAY"> Array
                            </label>
                            <label class="input-radio-group">
                                <input type="radio" wire:model.live="aggregationType" value="SUM"> Sum
                            </label>
                            <label class="input-radio-group">
                                <input type="radio" wire:model.live="aggregationType" value="AVG"> Avg
                            </label>
                            <label class="input-radio-group">
                                <input type="radio" wire:model.live="aggregationType" value="MIN"> Min
                            </label>
                            <label class="input-radio-group">
                                <input type="radio" wire:model.live="aggregationType" value="MAX"> Max
                            </label>
                            <label class="input-radio-group">
                                <input type="radio" wire:model.live="aggregationType" value="COUNT"> Count
                            </label>
                        </div>
                    </div>

                    @if($model && $aggregationType == 'ARRAY')
                        <div class="form-group">
                            <label>Select Columns</label>
                            <div class="alert-simple alert-info">
                                You can select multiple columns for this aggregation type
                            </div>
                            <div>
                                @foreach($columns as $column)
                                    <label wire:key="{{$column}}" class="input-checkbox-group">
                                        <input type="checkbox" wire:model.defer="selectColumns"
                                               value="{{ $column }}"> {{ $column }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($model && $aggregationType != 'ARRAY')
                        <div class="form-group">
                            <label>Select Columns Aggregation</label>
                            <div class="alert-simple alert-info">
                                You can only select one column for this aggregation type
                            </div>
                            <div>
                                @foreach($columns as $column)
                                    <label wire:key="{{$column}}" class="input-radio-group">
                                        <input type="radio" wire:model.live="aggregationColumn"
                                               value="{{$column}}"> {{ $column }}
                                    </label>
                                @endforeach
                            </div>
                            @error('aggregationColumn')
                            <div class="form-feedback text-red-500 text-sm">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if(!$model)
                        <div class="form-group">
                            <label>Select Columns</label>
                            <div class="alert-simple alert-info">
                                Please select a model and aggregation type to select columns
                            </div>
                        </div>
                    @endif
                @endif

                <hr>
                <div class="flex justify-between items-center mt-4">
                    <a href="{{getCmsUrl('query-builder')}}" wire:navigate class="btn btn-default">Cancel</a>
                    <div class="flex justify-end items-center gap-1">
                        <button type="button" @click="testQuery" class="btn btn-primary">
                            {!! \CrudBooster\Components\Icon\Icon::BOLT !!} Test Query
                        </button>
                        <button type="submit" class="btn btn-success">
                            {!! \CrudBooster\Components\Icon\Icon::PENCIL !!}
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div x-show="showResultDialog"
             class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-gray-900 bg-opacity-10 z-20 dark:bg-gray-800 dark:bg-opacity-50">
            <div @click.away="closeResultDialog" id="results-container"
                 class="fixed bottom-0 left-0 right-0 bg-white overflow-auto dark:bg-gray-700">
                <div class="border border-gray-200 rounded-lg p-4 space-y-2 dark:border-gray-600">
                    <h3 class="text-lg font-bold antialiased dark:text-gray-300">Results:</h3>
                    <div class="overflow-auto max-w-full max-h-[500px]">
                        @if($results)
                            <table class="table table-bordered dark:border-gray-600">
                                <thead>
                                <tr>
                                    @foreach(($results[0]??[]) as $key=>$val)
                                        <th class="dark:text-gray-300">{{ $key }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($results as $result)
                                    <tr>
                                        @foreach($result as $key=>$val)
                                            <td class="td-no-wrap dark:text-gray-300">{{ \Illuminate\Support\Str::limit($result->{$key}??'', 50) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                        <!-- if more than 100 rows state -->
                        @if($results && count($results) === 100)
                            <div class="alert-simple alert-info text-center dark:text-gray-300">Only showing first 100
                                rows
                            </div>
                        @endif
                        @if(!$results)
                            <div class="alert-simple alert-info text-center dark:text-gray-300">There is no data
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function queryBuilder() {
            return {
                showResultDialog: false,
                testQuery() {
                    this.showResultDialog = true;
                @this.runQuery()
                    anime({
                        targets: '#results-container',
                        translateY: [100, 0],
                        duration: 1000,
                        easing: 'easeOutExpo'
                    });
                },
                closeResultDialog() {
                    setTimeout(() => {
                        this.showResultDialog = false;
                    }, 500);
                    anime({
                        targets: '#results-container',
                        translateY: [0, window.innerHeight],
                        easing: 'easeOutExpo'
                    });
                },
                init() {
                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            this.closeResultDialog();
                        }
                    });
                }
            }
        }
    </script>
</div>
