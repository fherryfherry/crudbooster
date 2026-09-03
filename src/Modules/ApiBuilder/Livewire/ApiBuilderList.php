<?php

namespace CrudBooster\Modules\ApiBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ApiBuilder\Http\ApiBuilderController;
use CrudBooster\Modules\ApiBuilder\Models\CbApiBuilder;
use CrudBooster\Modules\ApiBuilder\Models\CbApiRequestLog;
use CrudBooster\Modules\ApiBuilder\Models\CbApiToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class ApiBuilderList extends Component
{
    use WithAlertMessage;
    use WithConfirmMessage;
    use WithPagination;

    public string $activeTab = 'list';
    public string $sortBy = 'newest';
    public int $perPage = 10;
    public int $tokenPerPage = 4;
    public ?string $latestGeneratedToken = null;
    public bool $showTokenModal = false;
    public string $newTokenName = '';
    public string $newTokenStatus = 'active';
    public string $newTokenScope = '/v1/*';
    public bool $streamPaused = false;
    public int $logsLimit = 6;
    public bool $showSnippetModal = false;
    public ?string $selectedApiId = null;
    public ?string $snippetCurl = null;
    public ?string $snippetPython = null;
    public ?string $snippetPhp = null;
    public ?string $selectedApiToken = null;
    public bool $showNewApiModal = false;
    public string $newApiMode = 'quick';
    public ?string $quickModeTable = null;
    public array $availableTables = [];
    public bool $showTestModal = false;
    public ?string $testApiId = null;
    public ?string $testEndpoint = null;
    public ?string $testMethod = null;
    public array $testPayload = [];
    public ?string $testResponse = null;
    public bool $testLoading = false;
    public ?int $testStatusCode = null;
    public ?string $testStatusText = null;
    public ?string $testToken = null;

    public function setTab(string $tab): void

    {
        if (in_array($tab, ['list', 'credential', 'logs'], true)) {
            $this->activeTab = $tab;
            if ($tab === 'credential') {
                $this->dispatch('cb-credential-tab-opened');
            } elseif ($tab === 'logs') {
                $this->dispatch('cb-logs-tab-opened');
            }
            return;
        }

        $this->showAlertMessage(__('cb::api_builder.alerts.unknown_tab'), 'warning');
    }

    public function setSortBy(string $sort): void
    {
        $this->sortBy = $sort === 'oldest' ? 'oldest' : 'newest';
        $this->resetPage();
    }

    public function openGenerate(): void
    {
        $this->redirect(getCmsUrl('api-builder/create'), navigate: true);
    }

    public function openTestModal(string $id): void
    {
        $api = CbApiBuilder::query()->findOrFail($id);
        $this->testApiId = $id;
        $this->testMethod = strtoupper($api->method);
        $baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
        $this->testEndpoint = $baseUrl . '/api' . $api->endpoint_path;
        
        $payload = [];
        if ($api->payload_schema) {
            foreach ($api->payload_schema as $field) {
                $payload[$field['key']] = '';
            }
        }
        $this->testPayload = $payload;
        $this->testResponse = null;
        $this->testStatusCode = null;
        $this->testStatusText = null;
        
        $token = CbApiToken::query()->where('status', 'active')->first();
        $this->testToken = $token?->token_encrypted;
        
        $this->showTestModal = true;
    }

    public function closeTestModal(): void
    {
        $this->showTestModal = false;
        $this->testApiId = null;
        $this->testResponse = null;
        $this->testToken = null;
    }

    public function deleteApi(string $id): void
    {
        $api = CbApiBuilder::query()->findOrFail($id);
        $this->showConfirmMessage(
            __('cb::api_builder.alerts.confirm_delete_title'),
            __('cb::api_builder.alerts.confirm_delete_message', ['name' => $api->name]),
            ['deleteApiConfirmed', $id],
            __('cb::api_builder.actions.delete'),
            'danger'
        );
    }

    public function deleteApiConfirmed(string $id): void
    {
        ApiBuilderController::invalidateCache($id);
        CbApiBuilder::query()->where('id', $id)->delete();
        $this->confirmMessageClose();
        $this->showAlertMessage(__('cb::api_builder.alerts.api_deleted'), 'success');
        $this->resetPage();
    }

    public function editApi(string $id): void
    {
        $this->redirect(getCmsUrl('api-builder/edit?id=' . $id), navigate: true);
    }

    public function generateToken(): void
    {
        $this->openTokenModal();
    }

    public function openTokenModal(): void
    {
        $this->newTokenName = '';
        $this->newTokenStatus = 'active';
        $this->newTokenScope = '/v1/*';
        $this->showTokenModal = true;
    }

    public function closeTokenModal(): void
    {
        $this->showTokenModal = false;
    }

    public function submitGenerateToken(): void
    {
        Validator::make([
            'name' => $this->newTokenName,
            'status' => $this->newTokenStatus,
            'scope' => $this->newTokenScope,
        ], [
            'name' => 'required|string|min:3|max:100',
            'status' => 'required|in:active,expired,disabled',
            'scope' => ['required', 'string', 'min:2', 'max:255', 'regex:/^\/[A-Za-z0-9\-\_\.\*\/]*$/'],
        ])->validate();

        $rawToken = 'cb_' . Str::random(42);
        $prefix = Str::substr($rawToken, 0, 12);

        CbApiToken::query()->create([
            'name' => $this->newTokenName,
            'scope_endpoint' => $this->normalizeScopeEndpoint($this->newTokenScope),
            'auth_method' => 'api_key',
            'status' => $this->newTokenStatus,
            'token_prefix' => $prefix,
            'token_hash' => Hash::make($rawToken),
            'token_encrypted' => $rawToken,
            'failed_attempt_24h' => 0,
            'last_used_at' => null,
        ]);

        $this->latestGeneratedToken = $rawToken;
        $this->showTokenModal = false;
        $this->showAlertMessage(__('cb::api_builder.alerts.token_created'), 'success');
    }

    private function normalizeScopeEndpoint(string $scope): string
    {
        $scope = trim($scope);
        if ($scope === '') {
            return '/v1/*';
        }

        if (!str_starts_with($scope, '/')) {
            $scope = '/' . $scope;
        }

        return $scope;
    }

    public function deactivateToken(string $id): void
    {
        $token = CbApiToken::query()->findOrFail($id);
        $token->status = 'disabled';
        $token->save();
        $this->showAlertMessage(__('cb::api_builder.alerts.token_inactive'), 'success');
    }

    public function deleteToken(string $id): void
    {
        $token = CbApiToken::query()->findOrFail($id);
        $this->showConfirmMessage(
            __('cb::api_builder.alerts.confirm_delete_token_title'),
            __('cb::api_builder.alerts.confirm_delete_token_message', ['name' => $token->name]),
            ['deleteTokenConfirmed', $id],
            __('cb::api_builder.actions.delete'),
            'danger'
        );
    }

    public function deleteTokenConfirmed(string $id): void
    {
        CbApiToken::query()->where('id', $id)->delete();
        $this->confirmMessageClose();
        $this->showAlertMessage(__('cb::api_builder.alerts.token_deleted'), 'success');
    }

    public function copyApiKey(string $id): void
    {
        $token = CbApiToken::query()->findOrFail($id);
        $value = $token->token_encrypted;
        if (! $value) {
            $this->showAlertMessage(__('cb::api_builder.alerts.api_key_unavailable'), 'warning');
            return;
        }

        $this->dispatch('cb-copy-api-key', value: $value);
        $this->showAlertMessage(__('cb::api_builder.alerts.api_key_copied'), 'success');
    }

    public function openSnippetModal(string $id): void
    {
        $this->selectedApiId = $id;
        $api = CbApiBuilder::query()->findOrFail($id);
        $token = CbApiToken::query()->where('status', 'active')->first();

        $this->selectedApiToken = $token?->token_encrypted ?? 'YOUR_TOKEN_HERE';
        $baseUrl = rtrim(config('app.url', 'https://your-domain.com'), '/');
        
        $endpoint = ltrim($api->endpoint_path, '/');
        $fullUrl = $baseUrl . '/api/' . $endpoint;
        
        $method = strtoupper($api->method);

        $this->snippetCurl = "curl -X {$method} '{$fullUrl}' \\\n  -H 'Authorization: Bearer {$this->selectedApiToken}' \\\n  -H 'Content-Type: application/json' \\\n  -d '{\n    \"key\": \"value\"\n  }'";

        $this->snippetPython = "import requests\n\nurl = '{$fullUrl}'\nheaders = {{\n    'Authorization': 'Bearer {$this->selectedApiToken}',\n    'Content-Type': 'application/json'\n}}\ndata = {{\n    'key': 'value'\n}}\n\nresponse = requests.{$method}(url, headers=headers, json=data)\nprint(response.json())";

        $this->snippetPhp = sprintf("<?php

\$url = '%s';
\$token = '%s';

\$headers = [
    'Authorization: Bearer ' . \$token,
    'Content-Type: application/json',
];

\$data = [
    'key' => 'value'
];

\$ch = curl_init(\$url);
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, '%s');
curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);
curl_setopt(\$ch, CURLOPT_POSTFIELDS, json_encode(\$data));

\$response = curl_exec(\$ch);
curl_close(\$ch);

echo \$response;", $fullUrl, $this->selectedApiToken, $method);

        $this->showSnippetModal = true;
    }

    public function closeSnippetModal(): void
    {
        $this->showSnippetModal = false;
        $this->selectedApiId = null;
        $this->snippetCurl = null;
        $this->snippetPython = null;
        $this->snippetPhp = null;
        $this->selectedApiToken = null;
    }

    public function copySnippet(string $type): void
    {
        $snippet = match ($type) {
            'curl' => $this->snippetCurl,
            'python' => $this->snippetPython,
            'php' => $this->snippetPhp,
            default => null,
        };

        if ($snippet) {
            $this->dispatch('cb-copy-api-key', value: $snippet);
            $this->showAlertMessage(__('cb::api_builder.alerts.snippet_copied'), 'success');
        }
    }

    public function openNewApiModal(): void
    {
        $this->showNewApiModal = true;
        $this->newApiMode = 'quick';
        $this->quickModeTable = null;
        $this->availableTables = $this->getAvailableTables();
        $this->dispatch('cb-new-api-modal-opened');
    }

    public function closeNewApiModal(): void
    {
        $this->showNewApiModal = false;
        $this->newApiMode = 'quick';
        $this->quickModeTable = null;
    }

    public function proceedNewApi(): void
    {
        $generatedQuickMode = false;
        if ($this->newApiMode === 'quick') {
            $generatedQuickMode = $this->createQuickModeApi();
        } else {
            $this->redirect(getCmsUrl('api-builder/create'), navigate: true);
        }
        $this->closeNewApiModal();
        if ($generatedQuickMode) {
            $this->dispatch('cb-quick-api-generated');
        }
    }

    private function getAvailableTables(): array
    {
        try {
            $tables = Schema::getTables();
            $result = [];
            foreach ($tables as $table) {
                $tableName = is_array($table) ? ($table['name'] ?? null) : ($table->name ?? null);
                if ($tableName) {
                    $tableName = last(explode('.', $tableName));
                    if (!str_starts_with($tableName, 'cb_') && !str_starts_with($tableName, 'migrations')) {
                        $result[] = $tableName;
                    }
                }
            }
            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function createQuickModeApi(): bool
    {
        if (!$this->quickModeTable) {
            $this->showAlertMessage('Please select a table', 'warning');
            return false;
        }

        $tableName = $this->quickModeTable;
        $columns = $this->getTableColumns($tableName);
        $baseName = ucfirst(str_replace('_', ' ', $tableName));
        $baseSlug = Str::slug(str_replace('_', '-', $tableName));

        $payloadFields = [];
        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }
            $payloadFields[] = [
                'key' => $column,
                'type' => 'string',
                'required' => false,
                'description' => ucfirst($column) . ' field',
            ];
        }

        // 1. List API (GET /v1/table)
        CbApiBuilder::query()->create([
            'name' => 'List ' . $baseName,
            'endpoint_path' => '/v1/' . $baseSlug . '/list',
            'description' => 'List all records from ' . $tableName,
            'method' => 'GET',
            'status' => 'active',
            'rate_limit_enabled' => true,
            'rate_limit_rpm' => 60,
            'payload_schema' => [],
            'process_steps' => [
                [
                    'alias' => 'list_data',
                    'action_type' => 'select',
                    'target_table' => $tableName,
                    'column_mappings' => array_map(fn($col) => ['column' => $col], $columns),
                    'conditions' => [],
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
        ]);

        // 2. Detail API (GET /v1/table/detail)
        CbApiBuilder::query()->create([
            'name' => 'Detail ' . $baseName,
            'endpoint_path' => '/v1/' . $baseSlug . '/detail',
            'description' => 'Get single record detail from ' . $tableName,
            'method' => 'GET',
            'status' => 'active',
            'rate_limit_enabled' => true,
            'rate_limit_rpm' => 60,
            'payload_schema' => [['key' => 'id', 'type' => 'integer', 'required' => true, 'description' => 'ID of the record']],
            'process_steps' => [
                [
                    'alias' => 'detail_data',
                    'action_type' => 'select',
                    'target_table' => $tableName,
                    'column_mappings' => array_map(fn($col) => ['column' => $col], $columns),
                    'conditions' => [['field' => 'id', 'operator' => '=', 'value_ref' => 'payload.id']],
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
        ]);

        // 3. Create API (POST /v1/table/create)
        CbApiBuilder::query()->create([
            'name' => 'Create ' . $baseName,
            'endpoint_path' => '/v1/' . $baseSlug . '/create',
            'description' => 'Create new record in ' . $tableName,
            'method' => 'POST',
            'status' => 'active',
            'rate_limit_enabled' => true,
            'rate_limit_rpm' => 60,
            'payload_schema' => $payloadFields,
            'process_steps' => [
                [
                    'alias' => 'create_record',
                    'action_type' => 'insert',
                    'target_table' => $tableName,
                    'column_mappings' => array_map(fn($f) => ['column' => $f['key'], 'source_ref' => 'payload.' . $f['key']], $payloadFields),
                    'conditions' => [],
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
        ]);

        // 4. Update API (POST /v1/table/update)
        CbApiBuilder::query()->create([
            'name' => 'Update ' . $baseName,
            'endpoint_path' => '/v1/' . $baseSlug . '/update',
            'description' => 'Update existing record in ' . $tableName,
            'method' => 'POST',
            'status' => 'active',
            'rate_limit_enabled' => true,
            'rate_limit_rpm' => 60,
            'payload_schema' => array_merge([['key' => 'id', 'type' => 'integer', 'required' => true, 'description' => 'ID of the record']], $payloadFields),
            'process_steps' => [
                [
                    'alias' => 'update_record',
                    'action_type' => 'update',
                    'target_table' => $tableName,
                    'column_mappings' => array_map(fn($f) => ['column' => $f['key'], 'source_ref' => 'payload.' . $f['key']], $payloadFields),
                    'conditions' => [['field' => 'id', 'operator' => '=', 'value_ref' => 'payload.id']],
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
        ]);

        // 5. Delete API (POST /v1/table/delete)
        CbApiBuilder::query()->create([
            'name' => 'Delete ' . $baseName,
            'endpoint_path' => '/v1/' . $baseSlug . '/delete',
            'description' => 'Delete record from ' . $tableName,
            'method' => 'POST',
            'status' => 'active',
            'rate_limit_enabled' => true,
            'rate_limit_rpm' => 60,
            'payload_schema' => [['key' => 'id', 'type' => 'integer', 'required' => true, 'description' => 'ID of the record']],
            'process_steps' => [
                [
                    'alias' => 'delete_record',
                    'action_type' => 'delete',
                    'target_table' => $tableName,
                    'conditions' => [['field' => 'id', 'operator' => '=', 'value_ref' => 'payload.id']],
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
        ]);

        $this->showAlertMessage(__('cb::api_builder.alerts.api_draft_saved'), 'success');
        return true;
    }

    private function getTableColumns(string $table): array
    {
        try {
            return \Illuminate\Support\Facades\Schema::getColumnListing($table);
        } catch (\Throwable) {
            return [];
        }
    }

    public function render()
    {
        $query = CbApiBuilder::query();
        if ($this->sortBy === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $apis = $query->paginate($this->perPage);
        $totalApis = CbApiBuilder::query()->count();
        $activeEndpoints = CbApiBuilder::query()->where('status', 'active')->count();
        $avgResponse = (int) round(CbApiBuilder::query()->avg('avg_response_ms') ?? 0);
        $errorRate = (float) (CbApiBuilder::query()->avg('error_rate_percent') ?? 0);
        $tokenQuery = CbApiToken::query();
        $tokens = (clone $tokenQuery)->latest()->paginate($this->tokenPerPage, ['*'], 'tokenPage');
        $activeTokenCount = CbApiToken::query()->where('status', 'active')->count();
        $failed24h = (int) CbApiToken::query()->sum('failed_attempt_24h');
        $tokenStatusSummary = [
            'active' => CbApiToken::query()->where('status', 'active')->count(),
            'expired' => CbApiToken::query()->where('status', 'expired')->count(),
            'disabled' => CbApiToken::query()->where('status', 'disabled')->count(),
        ];
        $logs = CbApiRequestLog::query()
            ->latest('created_at')
            ->limit($this->logsLimit)
            ->get();
        $allLogs = CbApiRequestLog::query()->latest('created_at')->limit(1500)->get();
        $totalCalls = $allLogs->count();
        $errorCalls = $allLogs->where('is_error', true)->count();
        $errorRateLogs = $totalCalls > 0 ? ($errorCalls / $totalCalls) * 100 : 0;
        $avgLatencyLogs = (int) round($allLogs->avg('latency_ms') ?? 0);
        $successRate = $totalCalls > 0 ? (($totalCalls - $errorCalls) / $totalCalls) * 100 : 100;

        $errorDistribution = $allLogs
            ->where('is_error', true)
            ->groupBy('endpoint')
            ->map(fn(Collection $items, string $endpoint) => [
                'endpoint' => $endpoint,
                'count' => $items->count(),
            ])
            ->sortByDesc('count')
            ->take(4)
            ->values()
            ->all();

        $maxErrorCount = collect($errorDistribution)->max('count') ?: 1;
        $errorDistribution = collect($errorDistribution)->map(function ($item) use ($errorCalls, $maxErrorCount) {
            $item['percent'] = $errorCalls > 0 ? round(($item['count'] / $errorCalls) * 100, 1) : 0;
            $item['bar_percent'] = round(($item['count'] / $maxErrorCount) * 100, 1);
            return $item;
        })->values()->all();

        return view('cb.api-builder::list', [
            'apis' => $apis,
            'tokens' => $tokens,
            'logs' => $logs,
            'logsOverview' => [
                'totalCalls' => $totalCalls,
                'errorRate' => $errorRateLogs,
                'avgLatency' => $avgLatencyLogs,
                'successRate' => $successRate,
            ],
            'errorDistribution' => $errorDistribution,
            'hasMoreLogs' => CbApiRequestLog::query()->count() > $logs->count(),
            'securityInsights' => [
                'activeTokens' => $activeTokenCount,
                'failed24h' => $failed24h,
            ],
            'tokenStatusSummary' => $tokenStatusSummary,
            'stats' => [
                'totalApis' => $totalApis,
                'activeEndpoints' => $activeEndpoints,
                'avgResponse' => $avgResponse,
                'errorRate' => $errorRate,
            ],
        ])->layout('cb.themes::layout-app');
    }

    public function methodBadgeClass(string $method): string
    {
        return match (Str::upper($method)) {
            'GET' => 'cb-method-get',
            'POST' => 'cb-method-post',
            'PUT' => 'cb-method-put',
            'DELETE' => 'cb-method-delete',
            default => 'cb-method-any',
        };
    }

    public function statusDotClass(string $status): string
    {
        return match ($status) {
            'active' => 'cb-status-active',
            'testing' => 'cb-status-testing',
            'disabled' => 'cb-status-disabled',
            default => 'cb-status-disabled',
        };
    }

    public function authMethodLabel(string $method): string
    {
        return match ($method) {
            'bearer_token' => __('cb::api_builder.credential.auth_method.bearer_token'),
            'api_key' => __('cb::api_builder.credential.auth_method.api_key'),
            'oauth2' => __('cb::api_builder.credential.auth_method.oauth2'),
            default => __('cb::api_builder.credential.auth_method.unknown'),
        };
    }

    public function tokenStatusClass(string $status): string
    {
        return match ($status) {
            'active' => 'cb-token-status-active',
            'expired' => 'cb-token-status-expired',
            'disabled' => 'cb-token-status-disabled',
            default => 'cb-token-status-disabled',
        };
    }

    public function apiKeyMasked(CbApiToken $token): string
    {
        $value = $token->token_encrypted;
        if (! $value) {
            return ($token->token_prefix ?: __('cb::api_builder.alerts.api_key_unavailable')) . '...';
        }

        $head = Str::substr($value, 0, 10);
        $tail = Str::substr($value, -4);

        return $head . '...' . $tail;
    }

    public function formatLastUsed(?Carbon $lastUsedAt): string
    {
        if (! $lastUsedAt) {
            return __('cb::api_builder.credential.last_used_never');
        }

        return $lastUsedAt->diffForHumans();
    }

    public function toggleStream(): void
    {
        $this->streamPaused = ! $this->streamPaused;
    }

    public function loadMoreLogs(): void
    {
        $this->logsLimit += 6;
    }

    public function clearLogs(): void
    {
        $this->showConfirmMessage(
            __('cb::api_builder.alerts.confirm_clear_logs_title'),
            __('cb::api_builder.alerts.confirm_clear_logs_message'),
            'clearLogsConfirmed',
            __('cb::api_builder.actions.clear'),
            'danger'
        );
    }

    public function clearLogsConfirmed(): void
    {
        CbApiRequestLog::query()->truncate();
        $this->confirmMessageClose();
        $this->showAlertMessage(__('cb::api_builder.alerts.logs_cleared'), 'success');
    }

    public function exportLogsCsv()
    {
        $rows = CbApiRequestLog::query()->latest('created_at')->limit(1000)->get([
            'created_at',
            'method',
            'endpoint',
            'status_code',
            'status_text',
            'latency_ms',
            'is_error',
        ]);

        $lines = ['timestamp,method,endpoint,status_code,status_text,latency_ms,is_error'];
        foreach ($rows as $row) {
            $lines[] = sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"',
                $row->created_at?->format('Y-m-d H:i:s'),
                $row->method,
                $row->endpoint,
                $row->status_code,
                str_replace('"', '""', (string) $row->status_text),
                $row->latency_ms,
                $row->is_error ? '1' : '0'
            );
        }

        $content = implode("\n", $lines);
        $filename = 'api-logs-' . now()->format('Ymd-His') . '.csv';
        $this->dispatch('cb-download-csv', content: $content, filename: $filename);
        $this->showAlertMessage(__('cb::api_builder.alerts.csv_prepared'), 'success');
    }

    public function logStatusClass(int $statusCode): string
    {
        if ($statusCode >= 500) {
            return 'cb-log-status-error';
        }
        if ($statusCode >= 400) {
            return 'cb-log-status-warn';
        }
        return 'cb-log-status-ok';
    }

}
