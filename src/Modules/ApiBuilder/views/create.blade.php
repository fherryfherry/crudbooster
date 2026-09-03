<div class="p-6">
    <style>
        .dark .cb-api-builder-create .text-gray-800,
        .dark .cb-api-builder-create .text-gray-700,
        .dark .cb-api-builder-create .text-slate-700,
        .dark .cb-api-builder-create .text-slate-800 { color: #e5e7eb; }

        .dark .cb-api-builder-create .text-gray-600,
        .dark .cb-api-builder-create .text-gray-500,
        .dark .cb-api-builder-create .text-slate-600,
        .dark .cb-api-builder-create .text-slate-500 { color: #9ca3af; }

        .dark .cb-api-builder-create .text-gray-400,
        .dark .cb-api-builder-create .text-gray-300,
        .dark .cb-api-builder-create .text-slate-400 { color: #6b7280; }

        .dark .cb-api-builder-create .bg-gray-50,
        .dark .cb-api-builder-create .bg-gray-100,
        .dark .cb-api-builder-create .bg-slate-50,
        .dark .cb-api-builder-create .bg-slate-100 { background-color: #1f2937; }

        .dark .cb-api-builder-create .border-gray-100,
        .dark .cb-api-builder-create .border-gray-200,
        .dark .cb-api-builder-create .border-gray-300,
        .dark .cb-api-builder-create .border-slate-100,
        .dark .cb-api-builder-create .border-slate-200 { border-color: #374151; }

        .dark .cb-api-builder-create .text-blue-500,
        .dark .cb-api-builder-create .text-blue-600,
        .dark .cb-api-builder-create .text-blue-700,
        .dark .cb-api-builder-create .text-blue-800 { color: #38bdf8; }
        .dark .cb-api-builder-create .bg-blue-50,
        .dark .cb-api-builder-create .bg-blue-100,
        .dark .cb-api-builder-create .bg-blue-300 { background-color: rgba(56, 189, 248, .12); }
        .dark .cb-api-builder-create .border-blue-100,
        .dark .cb-api-builder-create .border-blue-200 { border-color: rgba(56, 189, 248, .35); }

        .dark .cb-api-builder-create .text-red-500,
        .dark .cb-api-builder-create .text-red-600,
        .dark .cb-api-builder-create .text-red-800,
        .dark .cb-api-builder-create .text-red-900 { color: #f87171; }
        .dark .cb-api-builder-create .bg-red-50,
        .dark .cb-api-builder-create .bg-red-100 { background-color: rgba(248, 113, 113, .12); }
        .dark .cb-api-builder-create .border-red-100,
        .dark .cb-api-builder-create .border-red-200,
        .dark .cb-api-builder-create .border-red-300 { border-color: rgba(248, 113, 113, .35); }

        .dark .cb-api-builder-create .text-green-600 { color: #4ade80; }
        .dark .cb-api-builder-create .bg-green-100 { background-color: rgba(74, 222, 128, .12); }

        .dark .cb-api-builder-create .text-amber-600,
        .dark .cb-api-builder-create .text-amber-900 { color: #fbbf24; }
        .dark .cb-api-builder-create .bg-amber-50,
        .dark .cb-api-builder-create .bg-amber-100 { background-color: rgba(251, 191, 36, .12); }
        .dark .cb-api-builder-create .border-amber-100 { border-color: rgba(251, 191, 36, .35); }
    </style>
    <div class="cb-api-builder-create" style="max-width: 1024px; margin-left: auto; margin-right: auto;">
        {{-- Breadcrumb & Header --}}
        <div class="mb-8 flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ getCmsUrl('api-builder') }}" wire:navigate class="hover:text-blue-600 transition-colors">{{ __('api_builder::api_builder.tabs.list') }}</a>
                    <span>/</span>
                    <span class="text-gray-800 font-medium">{{ $editingApiId ? __('api_builder::api_builder.create.title_edit') : __('api_builder::api_builder.create.title') }}</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $editingApiId ? __('api_builder::api_builder.create.title_edit') : __('api_builder::api_builder.create.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ $editingApiId ? __('api_builder::api_builder.create.subtitle_edit') : __('api_builder::api_builder.create.subtitle') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ getCmsUrl('api-builder') }}" wire:navigate class="btn btn-default">{{ __('api_builder::api_builder.actions.cancel_project') }}</a>
                <button type="button" class="btn btn-primary" wire:click="saveDraft">{{ __('api_builder::api_builder.actions.save') }}</button>
            </div>
        </div>

        {{-- Step Indicator --}}
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                <div>{{ __('api_builder::api_builder.create.step_of', ['step' => $step]) }}</div>
                <div class="text-blue-600">
                    @if($step === 1) {{ __('api_builder::api_builder.create.step_title.1') }}
                    @elseif($step === 2) {{ __('api_builder::api_builder.create.step_title.2') }}
                    @elseif($step === 3) {{ __('api_builder::api_builder.create.step_title.3') }}
                    @else {{ __('api_builder::api_builder.create.step_title.4') }}
                    @endif
                </div>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden flex">
                <div class="h-full bg-blue-500 transition-all duration-500" style="width: {{ ($step/4)*100 }}%"></div>
            </div>
        </div>

        {{-- Step 1: Basic Information --}}
        @if($step === 1)
            <div class="panel">
                <div class="panel-header"><h3 class="panel-title">{{ __('api_builder::api_builder.create.step1.title') }}</h3></div>
                <div class="panel-body p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        <div class="space-y-3">
                            <label class="font-semibold text-gray-800">{{ __('api_builder::api_builder.create.step1.api_name') }}</label>
                            <div class="text-sm text-gray-500">{{ __('api_builder::api_builder.create.step1.api_name_desc') }}</div>
                            <input wire:model.defer="name" class="form-control" placeholder="{{ __('api_builder::api_builder.create.step1.placeholder_api_name') }}">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-3 mt-6 md:mt-0">
                            <label class="font-semibold text-gray-800">{{ __('api_builder::api_builder.create.step1.endpoint_path') }}</label>
                            <div class="text-sm text-gray-500">{{ __('api_builder::api_builder.create.step1.endpoint_path_desc') }}</div>
                            <div class="flex shadow-sm rounded-md">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">/api/</span>
                                <input wire:model.defer="endpointPath" class="form-control !rounded-l-none" placeholder="{{ __('api_builder::api_builder.create.step1.placeholder_endpoint') }}">
                            </div>
                            @error('endpointPath') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        <div class="space-y-3">
                            <label class="font-semibold text-gray-800">{{ __('api_builder::api_builder.create.step1.method') }}</label>
                            <div class="text-sm text-gray-500">{{ __('api_builder::api_builder.create.step1.method_desc') }}</div>
                            <select wire:model.defer="method" class="form-control">
                                <option value="GET">GET - Fetch resources</option>
                                <option value="POST">POST - Create resources</option>
                                <option value="PUT">PUT - Update resources (full)</option>
                                <option value="PATCH">PATCH - Update resources (partial)</option>
                                <option value="DELETE">DELETE - Remove resources</option>
                            </select>
                            @error('method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="font-semibold text-gray-800">{{ __('api_builder::api_builder.create.step1.description') }}</label>
                        <div class="text-sm text-gray-500">{{ __('api_builder::api_builder.create.step1.description_desc') }}</div>
                        <textarea wire:model.defer="description" rows="4" class="form-control" placeholder="{{ __('api_builder::api_builder.create.step1.placeholder_description') }}"></textarea>
                    </div>
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                            <div class="flex-1">
                                <label class="font-semibold text-gray-800">{{ __('api_builder::api_builder.create.step1.traffic_controls') }}</label>
                                <div class="text-sm text-gray-500">{{ __('api_builder::api_builder.create.step1.traffic_controls_desc') }}</div>
                                <div class="mt-4 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" wire:model.defer="rateLimitEnabled" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 font-medium cursor-pointer">{{ __('api_builder::api_builder.create.step1.enable_rate_limit') }}</span>
                                    </div>
                                    <div class="pl-7">
                                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ __('api_builder::api_builder.create.step1.requests_per_minute') }}</div>
                                        <input type="number" wire:model.defer="rateLimitRpm" class="form-control max-w-[200px]" placeholder="60">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 2: Payload Definition --}}
        @if($step === 2)
            <div class="panel">
                <div class="panel-header flex justify-between items-center">
                    <h3 class="panel-title">{{ __('api_builder::api_builder.create.step2.title') }}</h3>
                </div>
                <div class="panel-body p-6">
                    <div class="mb-6 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-gray-600 text-sm">{{ __('api_builder::api_builder.create.step2.desc') }}</div>
                        <button type="button" wire:click="addPayloadField" class="btn btn-primary">{{ __('api_builder::api_builder.actions.add_input_field') }}</button>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('api_builder::api_builder.create.step2.headers.key') }}</th>
                                <th>{{ __('api_builder::api_builder.create.step2.headers.type') }}</th>
                                <th class="text-center">{{ __('api_builder::api_builder.create.step2.headers.required') }}</th>
                                <th>{{ __('api_builder::api_builder.create.step2.headers.description') }}</th>
                                <th class="text-right">{{ __('api_builder::api_builder.create.step2.headers.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payloadFields as $index => $field)
                                <tr wire:key="payload-{{ $index }}">
                                    <td><input wire:model.defer="payloadFields.{{ $index }}.key" class="form-control" placeholder="e.g. email"></td>
                                    <td class="w-48">
                                        <select wire:model.defer="payloadFields.{{ $index }}.type" class="form-control">
                                            <option value="string">String</option>
                                            <option value="integer">Integer</option>
                                            <option value="boolean">Boolean</option>
                                            <option value="array">Array</option>
                                            <option value="object">Object</option>
                                            <option value="file">File / Image</option>
                                        </select>
                                    </td>
                                    <td class="text-center w-24">
                                        <input type="checkbox" wire:model.defer="payloadFields.{{ $index }}.required" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td><input wire:model.defer="payloadFields.{{ $index }}.description" class="form-control" placeholder="{{ __('api_builder::api_builder.create.step2.sample_email_input') }}"></td>
                                    <td class="text-right"><button type="button" class="btn btn-danger btn-sm" wire:click="removePayloadField({{ $index }})">{{ __('api_builder::api_builder.actions.delete') }}</button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-gray-500 bg-gray-50 rounded-xl">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.58 4 8 4s8-1.79 8-4M4 7c0-2.21 3.58-4 8-4s8 1.79 8 4m0 5c0 2.21-3.58 4-8 4s-8-1.79-8-4" /></svg>
                                            <p>{{ __('api_builder::api_builder.create.step2.empty') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @error('payload') <div class="mt-4 p-3 bg-red-50 border border-red-100 text-red-600 rounded-lg text-sm">{{ $message }}</div> @enderror
                </div>
            </div>
        @endif

        {{-- Step 3: Process Logic --}}
        @if($step === 3)
            <div class="panel">
                <div class="panel-header flex justify-between items-center">
                    <h3 class="panel-title">{{ __('api_builder::api_builder.create.step3.title') }}</h3>
                </div>
                <div class="panel-body p-6">
                    <div class="mb-6 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-gray-600 text-sm">{{ __('api_builder::api_builder.create.step3.desc') }}</div>
                        <div class="flex gap-2">
                             <button type="button" class="btn btn-default" wire:click="openCheckUpModal()">{{ __('api_builder::api_builder.create.check_up.check_up') }}</button>
                             <button type="button" class="btn btn-primary" wire:click="openAddActionModal()">{{ __('api_builder::api_builder.actions.add_action') }}</button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($processActions as $index => $action)
                            <div wire:key="action-{{ $index }}" class="bg-white border-2 border-gray-100 hover:border-blue-200 rounded-2xl p-4 transition-all duration-300 shadow-sm flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="font-bold text-gray-800 flex items-center gap-2 truncate">
                                        {{ __('api_builder::api_builder.create.step3.action_type.' . $action['action_type']) }}
                                        <span class="text-gray-400 font-normal">({{ $action['alias'] }})</span>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        @if(in_array($action['action_type'], ['select','insert','update','delete']))
                                            {{ __('api_builder::api_builder.create.step3.target_table', ['table' => $action['target_table'] ?: __('api_builder::api_builder.misc.dash')]) }}
                                        @elseif($action['action_type'] === 'call_api')
                                            {{ __('api_builder::api_builder.create.step3.url_method', ['url' => $action['http_url'] ?: __('api_builder::api_builder.misc.dash'), 'method' => $action['http_method']]) }}
                                        @elseif($action['action_type'] === 'condition')
                                            @php
                                                $logicalOp = strtoupper($action['condition_logical_operator'] ?? 'and');
                                                $rules = $action['condition_rules'] ?? [];
                                                $summary = collect($rules)->map(function($r) {
                                                    return ($r['source_ref'] ?? '-') . ' ' . ($r['operator'] ?? '=') . ' ' . ($r['value'] ?? '-');
                                                })->join(" $logicalOp ");
                                            @endphp
                                            If {{ $summary ?: 'no rules defined' }}
                                        @elseif($action['action_type'] === 'throw_error')
                                            Status: {{ $action['error_status_code'] ?? '400' }} | Message: {{ $action['error_message'] ?? '-' }}
                                        @endif
                                    </div>

                                    @if($action['action_type'] === 'condition' && !empty($action['true_actions']))
                                        <div class="mt-3 pl-4 border-l-2 border-blue-100 space-y-2">
                                            @foreach($action['true_actions'] as $subIndex => $subAction)
                                                <div class="flex items-center gap-2 group">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-300"></div>
                                                    <div class="flex-grow min-w-0">
                                                        <div class="text-xs font-bold text-blue-800 uppercase tracking-wider">
                                                            {{ $subAction['alias'] }}
                                                        </div>
                                                        <div class="text-xs text-blue-700/80 mt-0.5">
                                                            @if(in_array($subAction['action_type'], ['select','insert','update','delete']))
                                                                Table: {{ $subAction['target_table'] ?: '-' }}
                                                            @elseif($subAction['action_type'] === 'call_api')
                                                                API: {{ $subAction['http_method'] }} {{ $subAction['http_url'] ?: '-' }}
                                                            @elseif($subAction['action_type'] === 'throw_error')
                                                                Status: {{ $subAction['error_status_code'] ?? '400' }} | {{ $subAction['error_message'] ?? '-' }}
                                                            @elseif($subAction['action_type'] === 'condition')
                                                                @php
                                                                    $subLogicalOp = strtoupper($subAction['condition_logical_operator'] ?? 'and');
                                                                    $subRules = $subAction['condition_rules'] ?? [];
                                                                    $subSummary = collect($subRules)->map(function($r) {
                                                                        return ($r['source_ref'] ?? '-') . ' ' . ($r['operator'] ?? '=') . ' ' . ($r['value'] ?? '-');
                                                                    })->join(" $subLogicalOp ");
                                                                @endphp
                                                                If {{ $subSummary ?: 'no rules defined' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button class="btn btn-default btn-sm px-2 py-1 text-xs" wire:click="moveActionUp({{ $subIndex }}, {{ $index }})">&uarr;</button>
                                                        <button class="btn btn-default btn-sm px-2 py-1 text-xs" wire:click="moveActionDown({{ $subIndex }}, {{ $index }})">&darr;</button>
                                                        <button class="btn btn-default btn-sm px-2 py-1 text-xs" wire:click="openEditActionModal({{ $subIndex }}, {{ $index }})">Edit</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <button type="button" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors py-1" wire:click="openAddActionModal({{ $index }})">+ ADD SUB ACTION</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button class="btn btn-default btn-sm" wire:click="moveActionUp({{ $index }})">&uarr;</button>
                                    <button class="btn btn-default btn-sm" wire:click="moveActionDown({{ $index }})">&darr;</button>
                                    <button class="btn btn-primary btn-sm" wire:click="openEditActionModal({{ $index }})">{{ __('api_builder::api_builder.actions.edit') }}</button>
                                    <button class="btn btn-danger btn-sm" wire:click="deleteAction({{ $index }})">{{ __('api_builder::api_builder.actions.delete') }}</button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                                <div class="max-w-xs mx-auto">
                                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">{{ __('api_builder::api_builder.create.step3.empty') }}</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 4: Output Mapper --}}
        @if($step === 4)
            <div class="panel">
                <div class="panel-header"><h3 class="panel-title">{{ __('api_builder::api_builder.create.step4.title') }}</h3></div>
                <div class="panel-body p-6 space-y-6">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <label class="text-sm font-semibold">{{ __('api_builder::api_builder.create.step4.response_mode') }}</label>
                        <select wire:model.defer="responseMode" class="form-control max-w-[220px] mt-2 shadow-sm">
                            <option value="custom">{{ __('api_builder::api_builder.create.step4.custom_mapper') }}</option>
                            <option value="last_action">{{ __('api_builder::api_builder.create.step4.use_last_action') }}</option>
                        </select>
                    </div>

                    @if($responseMode === 'custom')
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div class="text-gray-600 text-sm">Define custom JSON structure for API response.</div>
                            <button type="button" class="btn btn-primary" wire:click="addResponseMapping">{{ __('api_builder::api_builder.actions.add_response_mapping') }}</button>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('api_builder::api_builder.create.step4.headers.output_key') }}</th>
                                    <th>{{ __('api_builder::api_builder.create.step4.headers.source_ref') }}</th>
                                    <th class="text-right">{{ __('api_builder::api_builder.create.step4.headers.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($responseMappings as $index => $mapping)
                                    <tr wire:key="out-{{ $index }}">
                                        <td><input wire:model.defer="responseMappings.{{ $index }}.output_key" class="form-control" placeholder="data.user_id"></td>
                                        <td>
                                            <input wire:model.defer="responseMappings.{{ $index }}.source_ref" class="form-control" placeholder="action_alias.field">
                                        </td>
                                        <td class="text-right"><button class="btn btn-danger btn-sm" wire:click="removeResponseMapping({{ $index }})">{{ __('api_builder::api_builder.actions.delete') }}</button></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-gray-500 py-12 bg-gray-50 rounded-xl">{{ __('api_builder::api_builder.create.step4.empty') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 bg-blue-50 border border-blue-100 rounded-xl flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="text-sm text-blue-800 font-medium">
                                {{ __('api_builder::api_builder.create.step4.fallback_note') }}
                            </div>
                        </div>
                    @endif

                    <div class="pt-6 border-t border-gray-100">
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="shrink-0 mt-0.5"><input type="checkbox" id="cacheResponse" wire:model.defer="cacheResponseEnabled" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"></div>
                            <div>
                                <label for="cacheResponse" class="text-sm font-bold text-gray-800 cursor-pointer">{{ __('api_builder::api_builder.create.step4.cache_response') }}</label>
                                <p class="mt-1 text-xs text-gray-500 leading-relaxed">{{ __('api_builder::api_builder.create.step4.cache_response_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Footer Controls --}}
        <div class="mt-8 flex justify-between items-center py-6 border-t border-gray-100">
            <button type="button" class="btn btn-default h-12 px-8" wire:click="prevStep" @disabled($step === 1)>{{ __('api_builder::api_builder.actions.back') }}</button>
            <div class="flex gap-3">
                @if($step < 4)
                    <button type="button" class="btn btn-primary h-12 px-10" wire:click="nextStep">{{ __('api_builder::api_builder.actions.save_and_continue') }}</button>
                @else
                    <button type="button" class="btn btn-default h-12 px-8" wire:click="saveDraft">{{ __('api_builder::api_builder.actions.save_api_draft') }}</button>
                    <button type="button" class="btn btn-primary h-12 px-10" wire:click="publishApi">{{ __('api_builder::api_builder.actions.publish') }}</button>
                @endif
            </div>
        </div>
    </div>

    {{-- Action Edit Modal --}}
    @if($showActionModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="background: rgba(2, 6, 23, 0.72);">
            <div class="bg-white rounded-2xl shadow-2xl transition-all transform scale-100 flex flex-col" style="width:min(760px,92vw);max-height:calc(100vh - 40px);">
                <div class="p-5 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-base text-slate-800">{{ $editingActionIndex === null ? __('api_builder::api_builder.create.step3.modal.add_title') : __('api_builder::api_builder.create.step3.modal.edit_title') }}</h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors" wire:click="closeActionModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-5 space-y-5 overflow-y-auto flex-1 min-h-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.alias') }}</label>
                            <input class="form-control" wire:model.defer="actionForm.alias">
                        </div>
                        <div>
                            <label class="text-sm font-semibold mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.action_type') }}</label>
                            <select class="form-control" wire:model.live="actionForm.action_type">
                                <option value="select">{{ __('api_builder::api_builder.create.step3.action_type.select') }}</option>
                                <option value="insert">{{ __('api_builder::api_builder.create.step3.action_type.insert') }}</option>
                                <option value="update">{{ __('api_builder::api_builder.create.step3.action_type.update') }}</option>
                                <option value="delete">{{ __('api_builder::api_builder.create.step3.action_type.delete') }}</option>
                                <option value="call_api">{{ __('api_builder::api_builder.create.step3.action_type.call_api') }}</option>
                                <option value="condition">{{ __('api_builder::api_builder.create.step3.action_type.condition') }}</option>
                                <option value="throw_error">{{ __('api_builder::api_builder.create.step3.action_type.throw_error') }}</option>
                            </select>
                        </div>
                    </div>

                    @if(in_array($actionForm['action_type'], ['select','insert','update','delete']))
                        <div>
                            <label class="text-sm font-semibold mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.target_table') }}</label>
                            <select class="form-control" wire:model.live="actionForm.target_table">
                                <option value="">{{ __('api_builder::api_builder.create.step3.modal.target_table_placeholder') }}</option>
                                @foreach($tableOptions as $table)
                                    <option value="{{ $table }}">{{ $table }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($actionForm['action_type'] === 'select')
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex justify-between items-center">
                                <div class="text-sm font-semibold text-slate-600">{{ __('api_builder::api_builder.create.step3.modal.join_desc') }}</div>
                                <button type="button" class="btn btn-default btn-sm" wire:click="addJoin">{{ __('api_builder::api_builder.create.step3.modal.add_join') }}</button>
                            </div>
                            <div class="space-y-3">
                                @forelse($actionForm['joins'] ?? [] as $index => $join)
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                        <div class="md:col-span-2">
                                            <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.headers.join_type') }}</label>
                                            <select class="form-control shadow-sm" wire:model.defer="actionForm.joins.{{ $index }}.type">
                                                <option value="left">{{ __('api_builder::api_builder.create.step3.modal.join_types.left') }}</option>
                                                <option value="inner">{{ __('api_builder::api_builder.create.step3.modal.join_types.inner') }}</option>
                                                <option value="right">{{ __('api_builder::api_builder.create.step3.modal.join_types.right') }}</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.headers.join_table') }}</label>
                                            <select class="form-control shadow-sm" wire:model.defer="actionForm.joins.{{ $index }}.target_table">
                                                <option value="">{{ __('api_builder::api_builder.create.step3.modal.target_table_placeholder') }}</option>
                                                @foreach($tableOptions as $table)
                                                    <option value="{{ $table }}">{{ $table }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.headers.join_alias') }}</label>
                                            <input class="form-control shadow-sm" wire:model.defer="actionForm.joins.{{ $index }}.alias" placeholder="e.g. t1">
                                        </div>
                                        <div class="md:col-span-4">
                                            <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.headers.join_on') }}</label>
                                            <div class="flex items-center gap-2">
                                                <input class="form-control shadow-sm" wire:model.defer="actionForm.joins.{{ $index }}.on_primary" placeholder="primary_key">
                                                <span class="text-slate-400">=</span>
                                                <input class="form-control shadow-sm" wire:model.defer="actionForm.joins.{{ $index }}.on_foreign" placeholder="foreign_key">
                                            </div>
                                        </div>
                                        <div class="md:col-span-1 text-right">
                                            <button
                                                type="button"
                                                class="btn btn-default btn-sm w-full border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300"
                                                wire:click="removeJoin({{ $index }})"
                                                title="{{ __('api_builder::api_builder.actions.delete') }}"
                                                aria-label="{{ __('api_builder::api_builder.actions.delete') }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 12h6l1-12M10 11v6M14 11v6" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-slate-400 text-sm border-2 border-dashed border-slate-100 rounded-xl">{{ __('api_builder::api_builder.create.step3.modal.no_join') }}</div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    @if(in_array($actionForm['action_type'], ['select','insert','update']))
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex justify-between items-center">
                                <div class="text-sm font-semibold text-slate-600">
                                    {{ $actionForm['action_type'] === 'select' ? __('api_builder::api_builder.create.step3.modal.columns_to_select') : __('api_builder::api_builder.create.step3.modal.mapping_desc') }}
                                </div>
                                <div class="flex gap-2 items-center">
                                    <button type="button" class="btn {{ ($actionForm['column_mappings_raw'] ?? false) ? 'btn-primary' : 'btn-default' }} btn-sm" wire:click="$toggle('actionForm.column_mappings_raw')">
                                        {{ __('api_builder::api_builder.create.step3.modal.raw_sql') }}
                                    </button>
                                    @if(!($actionForm['column_mappings_raw'] ?? false) && $actionForm['action_type'] === 'insert')
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="addColumnMapping">{{ __('api_builder::api_builder.actions.add_mapping') }}</button>
                                    @endif
                                </div>
                            </div>

                            @if($actionForm['column_mappings_raw'] ?? false)
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <textarea wire:model.defer="actionForm.column_mappings_raw_sql" rows="4" class="form-control font-mono text-sm" placeholder="e.g. id, name, email as user_email"></textarea>
                                    <div class="text-xs text-slate-500 mt-1 italic">{{ __('api_builder::api_builder.create.step3.modal.raw_sql_columns_hint') }}</div>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @forelse($actionForm['column_mappings'] as $index => $map)
                                        <div class="flex gap-3 items-end">
                                            <div class="flex-grow">
                                                @if(in_array($actionForm['action_type'], ['select','update']))
                                                    <input class="form-control" wire:model.defer="actionForm.column_mappings.{{ $index }}.column" readonly>
                                                @else
                                                    <input class="form-control" wire:model.defer="actionForm.column_mappings.{{ $index }}.column" placeholder="table.column_name">
                                                @endif
                                            </div>
                                            @if($actionForm['action_type'] === 'insert' || $actionForm['action_type'] === 'update')
                                                <div class="flex-grow">
                                                    <select class="form-control" wire:model.live="actionForm.column_mappings.{{ $index }}.source_ref">
                                                        <option value="">{{ __('api_builder::api_builder.create.step3.modal.source_ref_placeholder') }}</option>
                                                        @foreach($sourceReferenceOptions as $group => $options)
                                                            <optgroup label="{{ $group }}">
                                                                @foreach($options as $sourceReferenceOption)
                                                                    <option value="{{ $sourceReferenceOption['value'] }}">{{ $sourceReferenceOption['label'] }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                    @if(($actionForm['column_mappings'][$index]['source_ref'] ?? '') === '__manual__')
                                                        <div class="mt-2">
                                                            <input type="text" class="form-control" wire:model.defer="actionForm.column_mappings.{{ $index }}.manual_value" placeholder="Enter manual value...">
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            <button
                                                type="button"
                                                class="btn btn-default btn-sm border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 min-w-[38px]"
                                                wire:click="removeColumnMapping({{ $index }})"
                                                title="{{ __('api_builder::api_builder.actions.delete') }}"
                                                aria-label="{{ __('api_builder::api_builder.actions.delete') }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 12h6l1-12M10 11v6M14 11v6" />
                                                </svg>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="text-center py-6 text-slate-400 text-sm border-2 border-dashed border-slate-100 rounded-xl">{{ __('api_builder::api_builder.create.step3.modal.empty_mapping') }}</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(in_array($actionForm['action_type'], ['select','update','delete']))
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex justify-between items-center">
                                <div class="text-sm font-semibold text-slate-600">{{ __('api_builder::api_builder.create.step3.modal.condition_desc') }}</div>
                                <div class="flex gap-2 items-center">
                                    <button type="button" class="btn {{ ($actionForm['conditions_raw'] ?? false) ? 'btn-primary' : 'btn-default' }} btn-sm" wire:click="$toggle('actionForm.conditions_raw')">
                                        {{ __('api_builder::api_builder.create.step3.modal.raw_sql') }}
                                    </button>
                                    @if(!($actionForm['conditions_raw'] ?? false))
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="addCondition">{{ __('api_builder::api_builder.actions.add_condition') }}</button>
                                    @endif
                                </div>
                            </div>

                            @if($actionForm['conditions_raw'] ?? false)
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <textarea wire:model.defer="actionForm.conditions_raw_sql" rows="4" class="form-control font-mono text-sm" placeholder="e.g. status = 'active' AND (price > 100 OR category_id = 5)"></textarea>
                                    <div class="text-xs text-slate-500 mt-1 italic">{{ __('api_builder::api_builder.create.step3.modal.raw_sql_where_hint') }}</div>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @forelse($actionForm['conditions'] as $index => $condition)
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100" style="display:grid;grid-template-columns: 1.8fr 0.7fr 2fr auto;gap:12px;align-items:start;">
                                            <div>
                                                <select class="form-control" wire:model.defer="actionForm.conditions.{{ $index }}.field">
                                                    <option value="">{{ __('api_builder::api_builder.create.step3.modal.conditions.field_placeholder') }}</option>
                                                    @foreach($tableColumns as $column)
                                                        <option value="{{ $column }}">{{ $column }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <select class="form-control" wire:model.defer="actionForm.conditions.{{ $index }}.operator">
                                                    <option value="=">=</option>
                                                    <option value="!=">!=</option>
                                                    <option value=">">&gt;</option>
                                                    <option value="<">&lt;</option>
                                                    <option value=">=">&gt;=</option>
                                                    <option value="<=">&lt;=</option>
                                                    <option value="like">LIKE</option>
                                                </select>
                                            </div>
                                            <div>
                                                <select class="form-control" wire:model.live="actionForm.conditions.{{ $index }}.value_ref">
                                                    <option value="">{{ __('api_builder::api_builder.create.step3.modal.source_ref_placeholder') }}</option>
                                                    @foreach($sourceReferenceOptions as $group => $options)
                                                        <optgroup label="{{ $group }}">
                                                            @foreach($options as $sourceReferenceOption)
                                                                <option value="{{ $sourceReferenceOption['value'] }}">{{ $sourceReferenceOption['label'] }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                @if(($actionForm['conditions'][$index]['value_ref'] ?? '') === '__manual__')
                                                    <div class="mt-2">
                                                        <input type="text" class="form-control" wire:model.defer="actionForm.conditions.{{ $index }}.manual_value" placeholder="{{ __('api_builder::api_builder.create.step3.modal.manual_value_placeholder') }}">
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <button
                                                    type="button"
                                                    class="btn btn-default btn-sm border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 min-w-[38px]"
                                                    wire:click="removeCondition({{ $index }})"
                                                    title="{{ __('api_builder::api_builder.actions.delete') }}"
                                                    aria-label="{{ __('api_builder::api_builder.actions.delete') }}"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 12h6l1-12M10 11v6M14 11v6" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-6 text-slate-400 text-sm border-2 border-dashed border-slate-100 rounded-xl">{{ __('api_builder::api_builder.create.step3.modal.conditions.empty') }}</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($actionForm['action_type'] === 'call_api')
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.endpoint_url') }}</label>
                                    <input class="form-control" wire:model.defer="actionForm.http_url" placeholder="https://api.example.com/v1/user">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.method') }}</label>
                                    <select class="form-control" wire:model.defer="actionForm.http_method">
                                        <option>GET</option><option>POST</option><option>PUT</option><option>PATCH</option><option>DELETE</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.authorization_token') }}</label>
                                <input class="form-control" wire:model.defer="actionForm.http_auth_token" placeholder="payload.token">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500 mb-1 block">{{ __('api_builder::api_builder.create.step3.modal.headers_json') }}</label>
                                <textarea rows="3" class="form-control font-mono text-sm" wire:model.defer="actionForm.http_headers_json" placeholder='{"X-Header": "Value"}'></textarea>
                            </div>
                        </div>
                    @endif

                    @if($actionForm['action_type'] === 'condition')
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                             <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-4">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm font-semibold text-slate-600">Logic Configuration</div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs font-semibold text-slate-500">Match Mode</label>
                                        <select class="form-control !w-auto" wire:model.defer="actionForm.condition_logical_operator">
                                            <option value="and">ALL (AND)</option><option value="or">ANY (OR)</option>
                                        </select>
                                    </div>
                                </div>
                                @foreach($actionForm['condition_rules'] ?? [] as $index => $rule)
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end border border-slate-200 bg-white p-3 rounded-lg shadow-sm">
                                        <div class="md:col-span-4">
                                            <label class="text-xs font-semibold text-slate-500 mb-1 block">Subject</label>
                                            <select class="form-control" wire:model.live="actionForm.condition_rules.{{ $index }}.source_ref">
                                                <option value="">-- Select source --</option>
                                                @foreach($sourceReferenceOptions as $group => $options)
                                                    <optgroup label="{{ $group }}">
                                                        @foreach($options as $sourceReferenceOption)
                                                            <option value="{{ $sourceReferenceOption['value'] }}">{{ $sourceReferenceOption['label'] }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            @if(($actionForm['condition_rules'][$index]['source_ref'] ?? '') === '__manual__')
                                                <div class="mt-2"><input type="text" class="form-control" wire:model.defer="actionForm.condition_rules.{{ $index }}.manual_value" placeholder="Enter manual value..."></div>
                                            @endif
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="text-xs font-semibold text-slate-500 mb-1 block">Op</label>
                                            <select class="form-control" wire:model.live="actionForm.condition_rules.{{ $index }}.operator">
                                                <option value="=">=</option><option value="!=">!=</option><option value=">">&gt;</option><option value="<">&lt;</option><option value=">=">&gt;=</option><option value="<=">&lt;=</option>
                                                <option value="in">IN</option><option value="not_in">NOT IN</option><option value="empty">Is Empty</option><option value="not_empty">Not Empty</option>
                                            </select>
                                        </div>
                                        @if(!in_array($actionForm['condition_rules'][$index]['operator'] ?? '=', ['empty', 'not_empty']))
                                            <div class="md:col-span-4">
                                                <label class="text-xs font-semibold text-slate-500 mb-1 block">Expect Value</label>
                                                <input type="text" class="form-control" wire:model.defer="actionForm.condition_rules.{{ $index }}.value">
                                            </div>
                                        @endif
                                        <div class="md:col-span-1 text-right">
                                            @if(count($actionForm['condition_rules'] ?? []) > 1)
                                                <button type="button" class="btn btn-danger btn-sm w-full h-[32px]" wire:click="removeConditionRule({{ $index }})">&times;</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" class="btn btn-default btn-sm w-full border-dashed" wire:click="addConditionRule">+ Add Rule</button>
                             </div>
                        </div>
                    @endif

                    @if($actionForm['action_type'] === 'throw_error')
                        <div class="p-4 bg-red-50 border border-red-100 rounded-xl space-y-4">
                            <h4 class="font-semibold text-red-800 text-sm">Throw Error Response</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-3">
                                    <label class="text-xs font-semibold text-red-500 mb-1 block">Message</label>
                                    <input type="text" class="form-control" wire:model.defer="actionForm.error_message" placeholder="e.g. User not found">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-red-500 mb-1 block">Code</label>
                                    <input type="number" class="form-control" wire:model.defer="actionForm.error_status_code" placeholder="404">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-5 border-t bg-slate-50 flex justify-end gap-3 rounded-b-2xl">
                    @if($editingActionIndex !== null)
                        <button type="button" class="btn btn-danger" wire:click="deleteAction({{ $editingActionIndex }})">Delete</button>
                    @endif
                    <button type="button" class="btn btn-default" wire:click="closeActionModal">{{ __('api_builder::api_builder.actions.cancel') }}</button>
                    <button type="button" class="btn btn-primary px-8" wire:click="saveActionModal">{{ __('api_builder::api_builder.actions.save') }}</button>
                </div>
            </div>
        </div>
    @endif

    @if($showCheckUpModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="background: rgba(2, 6, 23, 0.72);">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="width:min(760px,92vw);max-height:calc(100vh - 40px);">
                <div class="p-5 border-b flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800">Health Check</h3>
                        <p class="text-slate-500 mt-1">Review potential issues in your API logic.</p>
                    </div>
                    <button type="button" class="p-2 hover:bg-slate-100 rounded-full transition-colors" wire:click="closeCheckUpModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-5 overflow-y-auto flex-grow bg-slate-50/30">
                    @if(empty($checkUpResults))
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h4 class="text-xl font-bold text-slate-800">All systems go!</h4>
                            <p class="text-slate-500 mt-2">No logical issues detected in your API configuration.</p>
                        </div>
                    @else
                        @php $hasDanger = collect($checkUpResults)->where('severity', 'danger')->count() > 0; @endphp
                        <div class="mb-8 p-5 rounded-2xl flex items-center gap-5 {{ $hasDanger ? 'bg-red-50 text-red-900 border border-red-100' : 'bg-amber-50 text-amber-900 border border-amber-100' }}">
                            <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center {{ $hasDanger ? 'bg-red-100' : 'bg-amber-100' }}">
                                @if($hasDanger)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">{{ $hasDanger ? 'Critical Issues Found' : 'Optimization Suggestions' }}</h4>
                                <p class="text-sm opacity-80">We've identified {{ count($checkUpResults) }} areas for improvement.</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @foreach($checkUpResults as $res)
                                <div class="p-5 rounded-2xl border bg-white shadow-sm flex items-start gap-4 {{ $res['severity'] === 'danger' ? 'border-red-100' : 'border-amber-100' }}">
                                    <div class="mt-1.5"><span class="flex h-2.5 w-2.5 rounded-full {{ $res['severity'] === 'danger' ? 'bg-red-500' : 'bg-amber-500' }}"></span></div>
                                    <div class="min-w-0 flex-grow">
                                        <div class="text-[10px] font-black uppercase tracking-widest {{ $res['severity'] === 'danger' ? 'text-red-500' : 'text-amber-600' }}">
                                            {{ $res['type'] }}
                                        </div>
                                        <p class="text-sm text-slate-700 mt-1 font-medium leading-relaxed">{{ $res['message'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="p-5 border-t bg-slate-50 flex justify-end gap-3">
                    <button type="button" class="btn btn-default" wire:click="closeCheckUpModal">Close</button>
                    <button type="button" class="btn btn-primary px-8" wire:click="runCheckUp">Check Again</button>
                </div>
            </div>
        </div>
    @endif
</div>
