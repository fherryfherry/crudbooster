<?php

namespace CrudBooster\Modules\ApiBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Modules\ApiBuilder\Http\ApiBuilderController;
use CrudBooster\Modules\ApiBuilder\Models\CbApiBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

class ApiBuilderCreate extends Component
{
    use WithAlertMessage;

    public int $step = 1;
    public string $name = '';
    public string $endpointPath = '';
    public string $description = '';
    public bool $rateLimitEnabled = true;
    public int $rateLimitRpm = 1000;
    public string $method = 'POST';

    public array $payloadFields = [];
    public array $processActions = [];
    public array $responseMappings = [];
    public string $responseMode = 'custom';
    public bool $cacheResponseEnabled = false;

    public bool $showActionModal = false;
    public ?int $editingActionIndex = null;
    public ?int $editingParentIndex = null;
    public bool $showCheckUpModal = false;
    public array $checkUpResults = [];
    public ?string $editingApiId = null;
    public array $actionForm = [];

    public array $tableOptions = [];
    public array $tableColumns = [];

    public function mount(): void
    {
        $this->tableOptions = $this->getTableOptions();
        $this->payloadFields = [
            ['key' => 'email', 'type' => 'string', 'required' => true, 'description' => __('api_builder::api_builder.create.step2.sample_email_input')],
        ];
        $this->actionForm = $this->defaultActionForm();

        $apiId = request()->query('id');
        if ($apiId) {
            $api = CbApiBuilder::query()->find($apiId);
            if ($api) {
                $this->editingApiId = $api->id;
                $this->name = (string) $api->name;
                $this->endpointPath = ltrim((string) $api->endpoint_path, '/');
                $this->description = (string) ($api->description ?? '');
                $this->method = (string) $api->method;
                $this->rateLimitEnabled = (bool) $api->rate_limit_enabled;
                $this->rateLimitRpm = (int) ($api->rate_limit_rpm ?? 1000);
                $this->payloadFields = is_array($api->payload_schema) && count($api->payload_schema) > 0 ? $api->payload_schema : $this->payloadFields;
                $this->processActions = is_array($api->process_steps) ? $api->process_steps : [];
                $this->responseMode = (string) data_get($api->response_mapper, 'mode', 'custom');
                $this->responseMappings = is_array(data_get($api->response_mapper, 'mapping')) ? data_get($api->response_mapper, 'mapping') : [];
                $this->cacheResponseEnabled = (bool) ($api->cache_response_enabled ?? false);
            }
        }
    }

    public function updatedActionFormTargetTable($value): void
    {
        if (! $value) {
            $this->actionForm['column_mappings'] = [];
            return;
        }

        if (in_array(($this->actionForm['action_type'] ?? ''), ['select', 'insert', 'update'], true)) {
            $this->actionForm['column_mappings'] = $this->buildAutoColumnMappings($value);
            if (($this->actionForm['action_type'] ?? '') === 'update' && empty($this->actionForm['conditions'])) {
                $this->actionForm['conditions'] = [$this->defaultConditionRow()];
            }
            return;
        }

        $this->actionForm['column_mappings'] = [];
    }

    public function updatedActionFormActionType($value): void
    {
        if (in_array($value, ['update', 'delete'], true)) {
            $this->actionForm['column_mappings'] = [];
            if (empty($this->actionForm['conditions'])) {
                $this->actionForm['conditions'] = [$this->defaultConditionRow()];
            }
            if ($value === 'update' && !empty($this->actionForm['target_table'])) {
                $this->actionForm['column_mappings'] = $this->buildAutoColumnMappings($this->actionForm['target_table']);
            }
            return;
        }

        $this->actionForm['conditions'] = [];
        if (in_array($value, ['select', 'insert'], true) && !empty($this->actionForm['target_table'])) {
            $this->actionForm['column_mappings'] = $this->buildAutoColumnMappings($this->actionForm['target_table']);
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validateStep1();
        }
        if ($this->step === 2) {
            $this->validatePayload();
        }
        if ($this->step === 3) {
            $this->validateProcess();
        }

        $this->step = min(4, $this->step + 1);
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function addPayloadField(): void
    {
        $this->payloadFields[] = ['key' => '', 'type' => 'string', 'required' => false, 'description' => ''];
    }

    public function removePayloadField(int $index): void
    {
        unset($this->payloadFields[$index]);
        $this->payloadFields = array_values($this->payloadFields);
    }

    public function moveActionUp(int $index, ?int $parentIndex = null): void
    {
        if ($index <= 0) {
            return;
        }
        if ($parentIndex !== null) {
            $temp = $this->processActions[$parentIndex]['true_actions'][$index];
            $this->processActions[$parentIndex]['true_actions'][$index] = $this->processActions[$parentIndex]['true_actions'][$index - 1];
            $this->processActions[$parentIndex]['true_actions'][$index - 1] = $temp;
        } else {
            $temp = $this->processActions[$index];
            $this->processActions[$index] = $this->processActions[$index - 1];
            $this->processActions[$index - 1] = $temp;
        }
    }

    public function moveActionDown(int $index, ?int $parentIndex = null): void
    {
        if ($parentIndex !== null) {
            if ($index >= count($this->processActions[$parentIndex]['true_actions']) - 1) {
                return;
            }
            $temp = $this->processActions[$parentIndex]['true_actions'][$index];
            $this->processActions[$parentIndex]['true_actions'][$index] = $this->processActions[$parentIndex]['true_actions'][$index + 1];
            $this->processActions[$parentIndex]['true_actions'][$index + 1] = $temp;
        } else {
            if ($index >= count($this->processActions) - 1) {
                return;
            }
            $temp = $this->processActions[$index];
            $this->processActions[$index] = $this->processActions[$index + 1];
            $this->processActions[$index + 1] = $temp;
        }
    }

    public function openAddActionModal(?int $parentIndex = null): void
    {
        $this->editingActionIndex = null;
        $this->editingParentIndex = $parentIndex;
        $this->actionForm = $this->defaultActionForm();
        $this->tableColumns = [];
        $this->showActionModal = true;
    }

    public function openEditActionModal(int $index, ?int $parentIndex = null): void
    {
        $this->editingActionIndex = $index;
        $this->editingParentIndex = $parentIndex;

        if ($parentIndex !== null) {
            $action = $this->processActions[$parentIndex]['true_actions'][$index] ?? null;
        } else {
            $action = $this->processActions[$index] ?? null;
        }
        
        if (! $action) {
            return;
        }
        $this->actionForm = $action;
        if (in_array(($this->actionForm['action_type'] ?? ''), ['update', 'delete'], true) && empty($this->actionForm['conditions'])) {
            $this->actionForm['conditions'] = [$this->defaultConditionRow()];
        }
        if (in_array(($this->actionForm['action_type'] ?? ''), ['select', 'insert', 'update'], true) && !empty($this->actionForm['target_table'])) {
            $this->actionForm['column_mappings'] = $this->buildAutoColumnMappings($this->actionForm['target_table']);
        }
        $this->showActionModal = true;
    }

    public function closeActionModal(): void
    {
        $this->showActionModal = false;
    }

    public function closeCheckUpModal(): void
    {
        $this->showCheckUpModal = false;
    }

    public function openCheckUpModal(): void
    {
        $this->runCheckUp();
    }

    public function runCheckUp(): void
    {
        $issues = [];
        $payloadKeys = collect($this->payloadFields)->pluck('key')->filter()->all();
        $definedAliases = [];

        // Recursive helper to scan aliases and check rules
        $scanActions = function($actions, $isNested = false) use (&$scanActions, &$issues, $payloadKeys, &$definedAliases) {
            foreach ($actions as $index => $action) {
                $alias = $action['alias'] ?? null;
                $type = $action['action_type'] ?? 'unknown';
                
                if (!$alias) {
                    $issues[] = ['type' => 'logic_error', 'message' => "Action #".($index+1)." is missing an alias.", 'severity' => 'danger'];
                } elseif (in_array($alias, $definedAliases)) {
                    $issues[] = ['type' => 'logic_error', 'message' => "Duplicate alias found: '$alias'. Each action must have a unique alias.", 'severity' => 'danger'];
                } else {
                    $definedAliases[] = $alias;
                }

                // Check Table existence
                if (in_array($type, ['select', 'insert', 'update', 'delete'])) {
                    $table = $action['target_table'] ?? null;
                    if (!$table) {
                        $issues[] = ['type' => 'missing_ref', 'message' => "Action '$alias' ($type) has no target table selected.", 'severity' => 'danger'];
                    } elseif (!Schema::hasTable($table)) {
                        $issues[] = ['type' => 'schema_mismatch', 'message' => "Table '$table' in action '$alias' does not exist in database.", 'severity' => 'danger'];
                    }
                }

                // Check Conditions/Mappings
                $checkRef = function($ref, $actionAlias) use (&$issues, $payloadKeys, &$definedAliases) {
                    if (!$ref) return;
                    if (str_starts_with($ref, 'payload.')) {
                        $key = substr($ref, 8);
                        if (!in_array($key, $payloadKeys)) {
                            $issues[] = ['type' => 'missing_ref', 'message' => "Action '$actionAlias' references unknown payload field '$key'.", 'severity' => 'warning'];
                        }
                    } elseif (str_starts_with($ref, 'action_alias.')) {
                        $parts = explode('.', substr($ref, 13));
                        $targetAlias = $parts[0] ?? null;
                        if (!in_array($targetAlias, $definedAliases)) {
                            $issues[] = ['type' => 'missing_ref', 'message' => "Action '$actionAlias' references alias '$targetAlias' which is not yet defined or missing.", 'severity' => 'danger'];
                        }
                    }
                };

                // Check conditions
                foreach ($action['conditions'] ?? [] as $cond) {
                    $checkRef($cond['value_ref'] ?? null, $alias);
                }

                // Check column mappings
                foreach ($action['column_mappings'] ?? [] as $map) {
                    $checkRef($map['source_ref'] ?? null, $alias);
                }

                // Check condition rules (Multiple)
                if ($type === 'condition') {
                    foreach ($action['condition_rules'] ?? [] as $rule) {
                        $checkRef($rule['source_ref'] ?? null, $alias);
                    }
                    // Recursive scan for true_actions
                    $scanActions($action['true_actions'] ?? [], true);
                }
            }
        };

        $scanActions($this->processActions);

        // Check Response Mapper
        if ($this->responseMode === 'custom') {
            foreach ($this->responseMappings as $map) {
                $ref = $map['source_ref'] ?? null;
                if (!$ref) continue;
                if (str_starts_with($ref, 'payload.')) {
                    $key = substr($ref, 8);
                    if (!in_array($key, $payloadKeys)) {
                        $issues[] = ['type' => 'missing_ref', 'message' => "Response mapper references unknown payload field '$key'.", 'severity' => 'warning'];
                    }
                } elseif (str_starts_with($ref, 'action_alias.')) {
                    $parts = explode('.', substr($ref, 13));
                    $targetAlias = $parts[0] ?? null;
                    if (!in_array($targetAlias, $definedAliases)) {
                        $issues[] = ['type' => 'missing_ref', 'message' => "Response mapper references undefined alias '$targetAlias'.", 'severity' => 'danger'];
                    }
                }
            }
        }

        $this->checkUpResults = $issues;
        $this->showCheckUpModal = true;
    }

    public function saveActionModal(): void
    {
        $rules = [
            'action_type' => 'required|in:select,insert,update,delete,call_api,condition,throw_error',
            'alias' => 'required|string|max:60',
        ];

        if (in_array($this->actionForm['action_type'], ['select', 'insert', 'update', 'delete'], true)) {
            $rules['target_table'] = 'required|string';
        }
        if (in_array($this->actionForm['action_type'], ['update', 'delete'], true)) {
            $rules['conditions'] = 'required|array|min:1';
            $rules['conditions.*.field'] = 'required|string';
            $rules['conditions.*.operator'] = 'required|in:=,!=,>,<,>=,<=,like';
            $rules['conditions.*.value_ref'] = 'required|string';
        }
        if ($this->actionForm['action_type'] === 'call_api') {
            $rules['http_url'] = 'required|url';
            $rules['http_method'] = 'required|in:GET,POST,PUT,PATCH,DELETE';
        }
        if ($this->actionForm['action_type'] === 'condition') {
            $rules['condition_logical_operator'] = 'required|in:and,or';
            $rules['condition_rules'] = 'required|array|min:1';
            $rules['condition_rules.*.source_ref'] = 'required|string';
            $rules['condition_rules.*.operator'] = 'required|string';
            if (!isset($this->actionForm['true_actions'])) {
                $this->actionForm['true_actions'] = [];
            }
        }
        if ($this->actionForm['action_type'] === 'throw_error') {
            $rules['error_message'] = 'required|string';
            $rules['error_status_code'] = 'required|numeric|min:400|max:599';
        }

        Validator::make($this->actionForm, $rules)->validate();

        if ($this->editingActionIndex === null) {
            if ($this->editingParentIndex !== null) {
                $this->processActions[$this->editingParentIndex]['true_actions'][] = $this->actionForm;
            } else {
                $this->processActions[] = $this->actionForm;
            }
        } else {
            if ($this->editingParentIndex !== null) {
                $this->processActions[$this->editingParentIndex]['true_actions'][$this->editingActionIndex] = $this->actionForm;
            } else {
                $this->processActions[$this->editingActionIndex] = $this->actionForm;
            }
        }

        $this->showActionModal = false;
    }

    public function deleteAction(int $index, ?int $parentIndex = null): void
    {
        if ($parentIndex !== null) {
            unset($this->processActions[$parentIndex]['true_actions'][$index]);
            $this->processActions[$parentIndex]['true_actions'] = array_values($this->processActions[$parentIndex]['true_actions']);
        } else {
            unset($this->processActions[$index]);
            $this->processActions = array_values($this->processActions);
        }
    }

    public function addColumnMapping(): void
    {
        if (in_array(($this->actionForm['action_type'] ?? ''), ['select', 'update'], true)) {
            return;
        }

        if (!isset($this->actionForm['column_mappings'])) {
            $this->actionForm['column_mappings'] = [];
        }

        $this->actionForm['column_mappings'][] = [
            'column' => '',
            'source_ref' => '',
        ];
    }

    public function removeColumnMapping(int $index): void
    {
        unset($this->actionForm['column_mappings'][$index]);
        $this->actionForm['column_mappings'] = array_values($this->actionForm['column_mappings']);
    }

    public function addCondition(): void
    {
        $this->actionForm['conditions'][] = $this->defaultConditionRow();
    }

    public function removeCondition(int $index): void
    {
        unset($this->actionForm['conditions'][$index]);
        $this->actionForm['conditions'] = array_values($this->actionForm['conditions']);

        if (empty($this->actionForm['conditions'])) {
            $this->actionForm['conditions'] = [$this->defaultConditionRow()];
        }
    }

    public function addResponseMapping(): void
    {
        $this->responseMappings[] = ['output_key' => '', 'source_ref' => ''];
    }

    public function removeResponseMapping(int $index): void
    {
        unset($this->responseMappings[$index]);
        $this->responseMappings = array_values($this->responseMappings);
    }

    public function saveDraft(): void
    {
        $this->validateStep1();
        $this->validatePayload();
        $this->validateProcess();

        $endpointPath = '/' . ltrim($this->endpointPath, '/');
        $payload = [
            'name' => $this->name,
            'endpoint_path' => $endpointPath,
            'description' => $this->description,
            'method' => $this->method,
            'status' => 'testing',
            'rate_limit_enabled' => $this->rateLimitEnabled,
            'rate_limit_rpm' => $this->rateLimitRpm,
            'payload_schema' => $this->payloadFields,
            'process_steps' => $this->processActions,
            'response_mapper' => [
                'mode' => $this->responseMode,
                'mapping' => $this->responseMappings,
            ],
            'cache_response_enabled' => $this->cacheResponseEnabled,
            'avg_response_ms' => null,
            'error_rate_percent' => 0,
        ];

        if ($this->editingApiId) {
            CbApiBuilder::query()->where('id', $this->editingApiId)->update($payload);
            ApiBuilderController::invalidateCache($this->editingApiId);
        } else {
            CbApiBuilder::query()->create($payload);
        }

        $this->showAlertMessage(__('api_builder::api_builder.alerts.api_draft_saved'), 'success');
        $this->redirect(getCmsUrl('api-builder'), navigate: true);
    }

    public function publishApi(): void
    {
        $this->validateStep1();
        $this->validatePayload();

        $endpointPath = '/' . ltrim($this->endpointPath, '/');
        $payload = [
            'name' => $this->name,
            'endpoint_path' => $endpointPath,
            'description' => $this->description,
            'method' => $this->method,
            'status' => 'active',
            'rate_limit_enabled' => $this->rateLimitEnabled,
            'rate_limit_rpm' => $this->rateLimitRpm,
            'payload_schema' => $this->payloadFields,
            'process_steps' => $this->processActions,
            'response_mapper' => [
                'mode' => $this->responseMode,
                'mapping' => $this->responseMappings,
            ],
            'cache_response_enabled' => $this->cacheResponseEnabled,
            'avg_response_ms' => null,
            'error_rate_percent' => 0,
        ];

        if ($this->editingApiId) {
            CbApiBuilder::query()->where('id', $this->editingApiId)->update($payload);
            ApiBuilderController::invalidateCache($this->editingApiId);
        } else {
            CbApiBuilder::query()->create($payload);
        }

        $this->showAlertMessage(__('api_builder::api_builder.alerts.api_published'), 'success');
        $this->redirect(getCmsUrl('api-builder'), navigate: true);
    }

    private function validateStep1(): void
    {
        Validator::make([
            'name' => $this->name,
            'endpoint_path' => $this->endpointPath,
            'rate_limit_rpm' => $this->rateLimitRpm,
            'method' => $this->method,
        ], [
            'name' => 'required|string|min:3|max:120',
            'endpoint_path' => 'required|string|min:2|max:255',
            'rate_limit_rpm' => 'required|integer|min:1|max:100000',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE',
        ])->validate();
    }

    private function validatePayload(): void
    {
        foreach ($this->payloadFields as $field) {
            if (blank($field['key'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payload' => __('api_builder::api_builder.create.step2.payload_key_required'),
                ]);
            }
        }
    }

    private function validateProcess(): void
    {
        // Step 3 can be empty by design, placeholder is allowed.
    }

    private function defaultActionForm(): array
    {
        return [
            'alias' => 'action_' . Str::random(4),
            'action_type' => 'select',
            'target_table' => '',
            'column_mappings' => [],
            'column_mappings_raw' => false,
            'column_mappings_raw_sql' => '',
            'conditions' => [],
            'conditions_raw' => false,
            'conditions_raw_sql' => '',
            'joins' => [],
            'http_url' => '',
            'http_method' => 'POST',
            'http_auth_token' => '',
            'http_headers_json' => '{}',
            'condition_logical_operator' => 'and',
            'condition_rules' => [
                ['source_ref' => '', 'operator' => '=', 'value' => '']
            ],
            'error_message' => 'Execution stopped manually.',
            'error_status_code' => 400,
        ];
    }

    public function addConditionRule(): void
    {
        $this->actionForm['condition_rules'][] = ['source_ref' => '', 'operator' => '=', 'value' => ''];
    }

    public function removeConditionRule(int $index): void
    {
        unset($this->actionForm['condition_rules'][$index]);
        $this->actionForm['condition_rules'] = array_values($this->actionForm['condition_rules']);
        if (empty($this->actionForm['condition_rules'])) {
            $this->actionForm['condition_rules'] = [['source_ref' => '', 'operator' => '=', 'value' => '']];
        }
    }

    public function addJoin(): void
    {
        $this->actionForm['joins'][] = [
            'target_table' => '',
            'alias' => '',
            'type' => 'left',
            'on_primary' => '',
            'on_foreign' => '',
        ];
    }

    public function removeJoin(int $index): void
    {
        unset($this->actionForm['joins'][$index]);
        $this->actionForm['joins'] = array_values($this->actionForm['joins']);
    }

    private function getTableOptions(): array
    {
        $skip = [
            'migrations',
            'failed_jobs',
            'jobs',
            'job_batches',
            'sessions',
            'cache',
            'cache_locks',
            'password_reset_tokens',
            'personal_access_tokens',
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
            'horizon_jobs',
            'horizon_job_batches',
            'horizon_failed_jobs',
            'horizon_metrics',
            'horizon_monitored_tags',
            'horizon_notifications',
            'horizon_processes',
            'horizon_supervisors',
            'horizon_supervisor_processes',
            'pulse_aggregates',
            'pulse_entries',
            'pulse_values',
            'queue_monitor',
            'queue_monitor_statuses',
            'sqlite_sequence',
        ];
        return collect(Schema::getTableListing())
            ->map(function ($table) {
                return last(explode('.', (string) $table));
            })
            ->reject(function ($table) use ($skip) {
                $table = (string) $table;

                if (in_array($table, $skip, true)) {
                    return true;
                }

                if (in_array($table, ['cb_dummy_orders', 'cb_dummy_order_logs'], true)) {
                    return false;
                }

                if (str_starts_with($table, 'cb_')) {
                    return true;
                }

                return false;
            })
            ->values()
            ->all();
    }

    private function defaultConditionRow(): array
    {
        return [
            'field' => '',
            'operator' => '=',
            'value_ref' => '',
        ];
    }

    private function buildAutoColumnMappings(string $table): array
    {
        $columns = Schema::getColumnListing($table);
        $primaryColumns = $this->getPrimaryColumns($table);
        $existing = collect($this->actionForm['column_mappings'] ?? [])->keyBy('column');

        return collect($columns)
            ->reject(fn(string $column) => in_array($column, $primaryColumns, true))
            ->map(function (string $column) use ($existing) {
                $prev = $existing->get($column);
                return [
                    'column' => $column,
                    'source_ref' => Arr::get($prev, 'source_ref', ''),
                ];
            })
            ->values()
            ->all();
    }

    private function getPrimaryColumns(string $table): array
    {
        try {
            $indexes = Schema::getIndexes($table);
            foreach ($indexes as $index) {
                if (!empty($index['primary']) || (($index['name'] ?? null) === 'primary')) {
                    return array_values($index['columns'] ?? []);
                }
            }
        } catch (\Throwable) {
            // Fallback for drivers not supporting indexes metadata.
        }

        return ['id'];
    }

    private function syncTableColumns(): void
    {
        $this->tableColumns = [];
        if (!empty($this->actionForm['target_table'])) {
            $table = $this->actionForm['target_table'];
            try {
                $cols = Schema::getColumnListing($table);
                foreach ($cols as $col) {
                    $this->tableColumns[] = $table . '.' . $col;
                }
            } catch (\Throwable $e) {
                // Ignore if table doesn't exist
            }
        }

        foreach ($this->actionForm['joins'] ?? [] as $join) {
            if (!empty($join['target_table']) && !empty($join['alias'])) {
                $joinTable = $join['target_table'];
                $joinAlias = $join['alias'];
                try {
                    $cols = Schema::getColumnListing($joinTable);
                    foreach ($cols as $col) {
                        $this->tableColumns[] = $joinAlias . '.' . $col;
                    }
                } catch (\Throwable $e) {
                    // Ignore if table doesn't exist
                }
            }
        }
        
        $this->tableColumns = array_values(array_unique($this->tableColumns));
    }

    public function render()
    {
        $this->syncTableColumns();

        $processOutputAliases = collect($this->processActions)
            ->pluck('alias')
            ->filter()
            ->values()
            ->all();

        $sourceReferenceOptions = [];

        foreach ($this->payloadFields as $field) {
            $key = trim((string) ($field['key'] ?? ''));
            if ($key !== '') {
                $sourceReferenceOptions['Payload'][] = [
                    'value' => 'payload.' . $key,
                    'label' => 'payload.' . $key,
                ];
            }
        }

        $upstreamActions = $this->processActions;
        if ($this->editingActionIndex !== null) {
            $upstreamActions = array_slice($this->processActions, 0, $this->editingActionIndex);
        }

        foreach ($upstreamActions as $action) {
            $alias = trim((string) ($action['alias'] ?? ''));
            if ($alias !== '') {
                $columnMappings = $action['column_mappings'] ?? [];
                $hasColumns = false;
                
                foreach ($columnMappings as $mapping) {
                    $column = trim((string) ($mapping['column'] ?? ''));
                    if ($column !== '') {
                        $hasColumns = true;
                        $sourceReferenceOptions['Action'][] = [
                            'value' => 'action_alias.' . $alias . '.' . $column,
                            'label' => 'action_alias.' . $alias . '.' . $column,
                        ];
                    }
                }
                
                if (!$hasColumns) {
                    $sourceReferenceOptions['Action'][] = [
                        'value' => 'action_alias.' . $alias,
                        'label' => 'action_alias.' . $alias,
                    ];
                }
            }
        }

        if ($this->showActionModal) {
            foreach ($this->tableColumns as $column) {
                $sourceReferenceOptions['Table'][] = [
                    'value' => $column,
                    'label' => $column,
                ];
            }
        }

        $sourceReferenceOptions['Manual'][] = [
            'value' => '__manual__',
            'label' => __('api_builder::api_builder.create.step3.modal.other_manual'),
        ];

        foreach ($sourceReferenceOptions as $group => $options) {
            $sourceReferenceOptions[$group] = collect($options)
                ->unique('value')
                ->values()
                ->all();
        }

        return view('cb.api-builder::create', [
            'tableOptions' => $this->tableOptions,
            'tableColumns' => $this->tableColumns,
            'processOutputAliases' => $processOutputAliases,
            'sourceReferenceOptions' => $sourceReferenceOptions,
        ])->layout('cb.themes::layout-app');
    }
}
