<?php

namespace CrudBooster\Modules\ApiBuilder\Http;

use CrudBooster\Modules\ApiBuilder\Models\CbApiBuilder;
use CrudBooster\Modules\ApiBuilder\Models\CbApiRequestLog;
use CrudBooster\Modules\ApiBuilder\Models\CbApiToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApiBuilderController
{
    private const HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    private const CONFIG_CACHE_TTL = 300;
    private const RESPONSE_CACHE_TTL = 60;
    private const CONFIG_CACHE_PREFIX = 'cb_api_config:';
    private const RESPONSE_CACHE_PREFIX = 'cb_api_response:';
    private const RESPONSE_CACHE_INDEX_PREFIX = 'cb_api_response_index:';

    public function handle(Request $request, string $path): JsonResponse
    {
        $startTime = microtime(true);
        $requestMethod = strtoupper($request->method());
        $api = $this->getApiWithCache($path, $requestMethod);

        if (!$api) {
            $availableMethods = $this->getAvailableMethodsForPath($path);
            if (!empty($availableMethods)) {
                $this->logRequest($request, null, 405, 'Method Not Allowed', $startTime);
                return Response::json([
                    'success' => false,
                    'error' => 'Method Not Allowed',
                    'allowed_methods' => $availableMethods,
                ], 405);
            }
            $this->logRequest($request, null, 404, 'Not Found', $startTime);
            return Response::json(['error' => 'API endpoint not found'], 404);
        }

        return $this->handleRequest($request, $api, $startTime);
    }

    private function getApiWithCache(string $path, string $method): ?CbApiBuilder
    {
        $normalizedMethod = strtoupper($method);
        $cacheKey = $this->getConfigCacheKey($path, $normalizedMethod);
        
        try {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        } catch (\Throwable $e) {
        }
        
        $api = CbApiBuilder::query()
            ->where('endpoint_path', '/' . $path)
            ->where('method', $normalizedMethod)
            ->whereIn('status', ['active', 'testing'])
            ->first();

        if (!$api) {
            $api = CbApiBuilder::query()
                ->where('endpoint_path', $path)
                ->where('method', $normalizedMethod)
                ->whereIn('status', ['active', 'testing'])
                ->first();
        }
        
        if ($api) {
            try {
                Cache::put($cacheKey, $api, self::CONFIG_CACHE_TTL);
            } catch (\Throwable $e) {
            }
        }
        
        return $api;
    }

    private function getAvailableMethodsForPath(string $path): array
    {
        $methods = CbApiBuilder::query()
            ->whereIn('endpoint_path', ['/' . $path, $path])
            ->whereIn('status', ['active', 'testing'])
            ->pluck('method')
            ->map(fn($method) => strtoupper((string) $method))
            ->filter(fn($method) => in_array($method, self::HTTP_METHODS, true))
            ->unique()
            ->values()
            ->all();

        return $methods;
    }

    private function getConfigCacheKey(string $path, string $method): string
    {
        return self::CONFIG_CACHE_PREFIX . strtoupper($method) . ':' . ltrim($path, '/');
    }

    private function handleRequest(Request $request, CbApiBuilder $api, float $startTime): JsonResponse
    {
        $authResult = $this->authenticateRequestToken($request, $api);
        if ($authResult instanceof JsonResponse) {
            $this->logRequest($request, $api, 401, 'Unauthorized', $startTime);
            return $authResult;
        }

        if ($api->rate_limit_enabled) {
            $rateLimitResult = $this->checkRateLimit($api, $request);
            if ($rateLimitResult === false) {
                $this->logRequest($request, $api, 429, 'Too Many Requests', $startTime);
                return Response::json([
                    'success' => false,
                    'error' => 'Rate limit exceeded. Please try again later.'
                ], 429);
            }
        }

        if ($api->cache_response_enabled) {
            return $this->handleWithResponseCache($request, $api, $startTime);
        }

        return $this->handleWithoutResponseCache($request, $api, $startTime);
    }

    private function authenticateRequestToken(Request $request, CbApiBuilder $api): ?JsonResponse
    {
        $activeTokens = CbApiToken::query()
            ->where('status', 'active')
            ->get();

        // Strict mode: runtime endpoint always requires valid active token.
        if ($activeTokens->isEmpty()) {
            return Response::json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        $authHeader = (string) $request->header('Authorization', '');
        if (!preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return Response::json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        $incomingToken = trim((string) ($matches[1] ?? ''));
        if ($incomingToken === '') {
            return Response::json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        $requestPath = '/' . ltrim((string) $api->endpoint_path, '/');
        foreach ($activeTokens as $token) {
            if (! $this->tokenScopeMatches((string) $token->scope_endpoint, $requestPath)) {
                continue;
            }

            $storedHash = (string) ($token->token_hash ?? '');
            if ($storedHash === '') {
                continue;
            }

            $verified = hash_equals($storedHash, hash('sha256', $incomingToken));
            if (! $verified) {
                try {
                    $verified = Hash::check($incomingToken, $storedHash);
                } catch (\Throwable) {
                    $verified = false;
                }
            }

            if (! $verified) {
                continue;
            }

            $token->forceFill([
                'last_used_at' => now(),
            ])->save();

            return null;
        }

        return Response::json([
            'success' => false,
            'error' => 'Unauthorized',
        ], 401);
    }

    private function tokenScopeMatches(string $scope, string $requestPath): bool
    {
        $scope = trim($scope) !== '' ? trim($scope) : '/v1/*';
        $normalizedRequestPath = '/' . ltrim($requestPath, '/');

        return Str::is(ltrim($scope, '/'), ltrim($normalizedRequestPath, '/'))
            || Str::is($scope, $normalizedRequestPath);
    }

    private function checkRateLimit(CbApiBuilder $api, Request $request): bool
    {
        $rpm = $api->rate_limit_rpm ?? 60;
        $key = 'cb_api_rate:' . $api->id . ':' . $request->ip();
        
        try {
            $current = Cache::get($key, 0);
            
            if ($current >= $rpm) {
                return false;
            }
            
            Cache::put($key, $current + 1, 60);
            return true;
        } catch (\Throwable $e) {
            return true;
        }
    }

    private function handleWithoutResponseCache(Request $request, CbApiBuilder $api, float $startTime): JsonResponse
    {
        try {
            $result = $this->executeProcessSteps($api, $request);
            $finalOutput = $this->prepareOutput($api, $result, $request->all());
            
            $this->logRequest($request, $api, 200, 'OK', $startTime);
            
            return Response::json([
                'success' => true,
                'data' => $finalOutput
            ]);
        } catch (\Throwable $e) {
            $statusCode = $e->getCode();
            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }
            $statusText = $statusCode === 500 ? 'Internal Server Error' : 'Error';
            $this->logRequest($request, $api, $statusCode, $statusText, $startTime);
            return Response::json([
                'success' => false,
                'error' => $this->resolveErrorMessage($e, $statusCode)
            ], $statusCode);
        }
    }

    private function handleWithResponseCache(Request $request, CbApiBuilder $api, float $startTime): JsonResponse
    {
        $cacheKey = $this->getResponseCacheKey($api, $request);
        
        try {
            $cached = Cache::get($cacheKey);
            
            if ($cached !== null) {
                $this->logRequest($request, $api, 200, 'OK', $startTime);
                return Response::json([
                    'success' => true,
                    'data' => $cached,
                    'cached' => true
                ]);
            }
        } catch (\Throwable $e) {
        }
        
        try {
            $result = $this->executeProcessSteps($api, $request);
            $finalOutput = $this->prepareOutput($api, $result, $request->all());
            
            try {
                Cache::put($cacheKey, $finalOutput, self::RESPONSE_CACHE_TTL);
                $this->rememberResponseCacheKey($api, $cacheKey);
            } catch (\Throwable $e) {
            }
            
            $this->logRequest($request, $api, 200, 'OK', $startTime);
            
            return Response::json([
                'success' => true,
                'data' => $finalOutput,
                'cached' => false
            ]);
        } catch (\Throwable $e) {
            $statusCode = $e->getCode();
            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }
            $statusText = $statusCode === 500 ? 'Internal Server Error' : 'Error';
            $this->logRequest($request, $api, $statusCode, $statusText, $startTime);
            return Response::json([
                'success' => false,
                'error' => $this->resolveErrorMessage($e, $statusCode)
            ], $statusCode);
        }
    }

    private function resolveErrorMessage(\Throwable $e, int $statusCode): string
    {
        $debug = (bool) config('app.debug', false);

        if ($statusCode >= 500 && !$debug) {
            return 'Internal server error';
        }

        $message = trim((string) $e->getMessage());
        if ($message === '') {
            return $statusCode >= 500 ? 'Internal server error' : 'Request failed';
        }

        return $message;
    }

    private function getResponseCacheKey(CbApiBuilder $api, Request $request): string
    {
        $endpoint = ltrim($api->endpoint_path, '/');
        $payloadHash = md5((string) json_encode($request->all()));
        $method = strtoupper($request->method());
        return self::RESPONSE_CACHE_PREFIX . $method . ':' . $endpoint . ':' . $payloadHash;
    }

    private function getResponseCacheIndexKey(CbApiBuilder $api): string
    {
        return self::RESPONSE_CACHE_INDEX_PREFIX . ltrim((string) $api->endpoint_path, '/');
    }

    private function rememberResponseCacheKey(CbApiBuilder $api, string $cacheKey): void
    {
        $indexKey = $this->getResponseCacheIndexKey($api);
        $existing = Cache::get($indexKey, []);
        if (!is_array($existing)) {
            $existing = [];
        }

        if (!in_array($cacheKey, $existing, true)) {
            $existing[] = $cacheKey;
        }

        Cache::put($indexKey, $existing, 3600);
    }

    public static function invalidateCache(string $apiId): void
    {
        try {
            $api = CbApiBuilder::query()->find($apiId);
            if ($api) {
                $endpoint = ltrim($api->endpoint_path, '/');
                foreach (self::HTTP_METHODS as $method) {
                    Cache::forget(self::CONFIG_CACHE_PREFIX . $method . ':' . $endpoint);
                    Cache::forget(self::CONFIG_CACHE_PREFIX . $method . ':' . '/' . $endpoint);
                }

                $indexKey = self::RESPONSE_CACHE_INDEX_PREFIX . $endpoint;
                $keys = Cache::get($indexKey, []);
                if (is_array($keys)) {
                    foreach ($keys as $key) {
                        Cache::forget((string) $key);
                    }
                }
                Cache::forget($indexKey);
            }
        } catch (\Throwable $e) {
        }
    }

    public static function invalidateAllApiCache(): void
    {
        try {
            CbApiBuilder::query()->select('id')->each(function ($item) {
                self::invalidateCache((string) $item->id);
            });
        } catch (\Throwable $e) {
        }
    }

    private function prepareOutput(CbApiBuilder $api, array $results, array $payload = []): mixed
    {
        $responseMapper = $api->response_mapper ?? [];
        $mode = $responseMapper['mode'] ?? 'last_action';
        $mappings = $responseMapper['mapping'] ?? [];
        
        if ($mode === 'custom' && !empty($mappings)) {
            $output = [];
            foreach ($mappings as $mapping) {
                $outputKey = $mapping['output_key'] ?? null;
                $sourceRef = $mapping['source_ref'] ?? null;

                if ($outputKey && $sourceRef) {
                    $output[$outputKey] = $this->resolveValueRefExtended($mapping, 'source_ref', $payload, $results);
                }
            }            return $output;
        }
        
        if (!empty($results)) {
            return end($results);
        }
        
        return null;
    }

    private function executeProcessSteps(CbApiBuilder $api, Request $request): array
    {
        $processSteps = $api->process_steps ?? [];
        $payload = $request->all();
        $results = [];
        
        $this->runSteps($processSteps, $payload, $results);
        
        return $results;
    }

    private function runSteps(array $steps, array &$payload, array &$results): void
    {
        foreach ($steps as $step) {
            $actionType = $step['action_type'] ?? null;
            $alias = $step['alias'] ?? 'action_' . uniqid();
            
            switch ($actionType) {
                case 'select':
                    $results[$alias] = $this->executeSelect($step, $payload, $results);
                    break;
                case 'insert':
                    $results[$alias] = $this->executeInsert($step, $payload, $results);
                    break;
                case 'update':
                    $results[$alias] = $this->executeUpdate($step, $payload, $results);
                    break;
                case 'delete':
                    $results[$alias] = $this->executeDelete($step, $payload, $results);
                    break;
                case 'call_api':
                    $results[$alias] = $this->executeCallApi($step, $payload, $results);
                    break;
                case 'throw_error':
                    $message = $step['error_message'] ?? 'Execution stopped manually.';
                    $code = (int) ($step['error_status_code'] ?? 400);
                    if ($code < 400 || $code > 599) $code = 400;
                    throw new \Exception($message, $code);
                case 'condition':
                    $conditionMet = $this->executeCondition($step, $payload, $results);
                    $results[$alias] = $conditionMet;
                    
                    if ($conditionMet) {
                        $this->runSteps($step['true_actions'] ?? [], $payload, $results);
                    }
                    break;
            }
        }
    }

    private function executeCondition(array $step, array $payload, array $previousResults): bool
    {
        $logicalOperator = strtolower($step['condition_logical_operator'] ?? 'and');
        $rules = $step['condition_rules'] ?? [];
        
        // Backward compatibility for old single condition format
        if (empty($rules) && isset($step['condition_source_ref'])) {
            $rules = [
                [
                    'source_ref' => $step['condition_source_ref'],
                    'operator' => $step['condition_operator'] ?? '=',
                    'value' => $step['condition_value'] ?? null,
                ]
            ];
        }

        if (empty($rules)) {
            return false;
        }

        $overallResult = $logicalOperator === 'and' ? true : false;

        foreach ($rules as $rule) {
            $sourceRef = $rule['source_ref'] ?? null;
            $operator = strtolower($rule['operator'] ?? '=');
            $compareValue = $rule['value'] ?? null;
            
            $actualValue = null;
            if ($sourceRef) {
                $actualValue = $this->resolveValueRefExtended($rule, 'source_ref', $payload, $previousResults);
            }

            $conditionMet = false;

            switch ($operator) {
                case '=':
                    $conditionMet = $actualValue == $compareValue;
                    break;
                case '!=':
                    $conditionMet = $actualValue != $compareValue;
                    break;
                case '>':
                    $conditionMet = $actualValue > $compareValue;
                    break;
                case '<':
                    $conditionMet = $actualValue < $compareValue;
                    break;
                case '>=':
                    $conditionMet = $actualValue >= $compareValue;
                    break;
                case '<=':
                    $conditionMet = $actualValue <= $compareValue;
                    break;
                case 'empty':
                    $conditionMet = empty($actualValue);
                    break;
                case 'not_empty':
                    $conditionMet = !empty($actualValue);
                    break;
                case 'in':
                    $arrayValues = array_map('trim', explode(',', (string) $compareValue));
                    $conditionMet = in_array((string) $actualValue, $arrayValues, false);
                    break;
                case 'not_in':
                    $arrayValues = array_map('trim', explode(',', (string) $compareValue));
                    $conditionMet = !in_array((string) $actualValue, $arrayValues, false);
                    break;
            }

            if ($logicalOperator === 'and') {
                $overallResult = $overallResult && $conditionMet;
                if (!$overallResult) break; // Short-circuit AND
            } else {
                $overallResult = $overallResult || $conditionMet;
                if ($overallResult) break; // Short-circuit OR
            }
        }

        return $overallResult;
    }

    private function executeSelect(array $step, array $payload, array $previousResults): mixed
    {
        $table = $step['target_table'] ?? null;
        if (!$table) {
            return null;
        }

        $query = DB::table($table);

        // Apply Joins
        $joins = $step['joins'] ?? [];
        foreach ($joins as $join) {
            $joinTable = $join['target_table'] ?? null;
            $joinType = $join['type'] ?? 'left';
            $onPrimary = $join['on_primary'] ?? null;
            $onForeign = $join['on_foreign'] ?? null;

            if ($joinTable && $onPrimary && $onForeign) {
                switch ($joinType) {
                    case 'inner':
                        $query->join($joinTable, $onPrimary, '=', $onForeign);
                        break;
                    case 'right':
                        $query->rightJoin($joinTable, $onPrimary, '=', $onForeign);
                        break;
                    default:
                        $query->leftJoin($joinTable, $onPrimary, '=', $onForeign);
                        break;
                }
            }
        }

        if ($step['column_mappings_raw'] ?? false) {
            $rawSql = $step['column_mappings_raw_sql'] ?? '';
            if ($rawSql) {
                $resolvedSql = $this->resolveStringTemplate($rawSql, $payload, $previousResults);
                $this->guardRawSql($resolvedSql);
                $query->selectRaw($resolvedSql);
            }
        } else {
            $columnMappings = $step['column_mappings'] ?? [];
            $columns = array_filter(array_column($columnMappings, 'column'));
            if (!empty($columns)) {
                $query->select($columns);
            }
        }
        
        $this->applyConditions($query, $step, $payload, $previousResults);
        
        return $query->get();
    }

    private function applyConditions($query, array $step, array $payload, array $previousResults): void
    {
        if ($step['conditions_raw'] ?? false) {
            $rawSql = $step['conditions_raw_sql'] ?? '';
            if ($rawSql) {
                $resolvedSql = $this->resolveStringTemplate($rawSql, $payload, $previousResults);
                $this->guardRawSql($resolvedSql);
                $query->whereRaw($resolvedSql);
            }
            return;
        }

        $conditions = $step['conditions'] ?? [];
        $targetTable = $step['target_table'] ?? null;
        $joins = $step['joins'] ?? [];
        $allowedPrefixes = [];
        if ($targetTable) {
            $allowedPrefixes[] = $targetTable;
        }
        foreach ($joins as $join) {
            if (!empty($join['alias'])) {
                $allowedPrefixes[] = $join['alias'];
            }
        }

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $valueRef = $condition['value_ref'] ?? null;

            if ($field && $valueRef) {
                $value = $this->resolveValueRefExtended($condition, 'value_ref', $payload, $previousResults);

                $isColumnRef = false;
                if ($value === $valueRef && is_string($value) && str_contains($value, '.')) {
                    $parts = explode('.', $value);
                    if (count($parts) >= 2 && in_array($parts[0], $allowedPrefixes)) {
                        $isColumnRef = true;
                    }
                }

                if ($isColumnRef) {
                    $query->whereColumn($field, $operator, $value);
                } else {
                    $query->where($field, $operator, $value);
                }
            }
        }
    }

    private function executeInsert(array $step, array $payload, array $previousResults): mixed
    {
        $table = $step['target_table'] ?? null;
        if (!$table) {
            return null;
        }

        $columnMappings = $step['column_mappings'] ?? [];
        $data = [];
        
        foreach ($columnMappings as $mapping) {
            $column = $mapping['column'] ?? null;
            $sourceRef = $mapping['source_ref'] ?? null;
            
            if ($column && $sourceRef) {
                $data[$column] = $this->resolveValueRefExtended($mapping, 'source_ref', $payload, $previousResults);
            }
        }
        
        if (empty($data)) {
            $data = $payload;
        }
        
        return DB::table($table)->insert($data);
    }

    private function executeUpdate(array $step, array $payload, array $previousResults): mixed
    {
        $table = $step['target_table'] ?? null;
        if (!$table) {
            return null;
        }

        $columnMappings = $step['column_mappings'] ?? [];
        $data = [];
        
        foreach ($columnMappings as $mapping) {
            $column = $mapping['column'] ?? null;
            $sourceRef = $mapping['source_ref'] ?? null;
            
            if ($column && $sourceRef) {
                $data[$column] = $this->resolveValueRefExtended($mapping, 'source_ref', $payload, $previousResults);
            }
        }
        
        $query = DB::table($table);
        $this->applyConditions($query, $step, $payload, $previousResults);
        
        return $query->update($data);
    }

    private function executeDelete(array $step, array $payload, array $previousResults): mixed
    {
        $table = $step['target_table'] ?? null;
        if (!$table) {
            return null;
        }

        $query = DB::table($table);
        $this->applyConditions($query, $step, $payload, $previousResults);
        
        return $query->delete();
    }

    private function executeCallApi(array $step, array $payload, array $previousResults): mixed
    {
        $url = $step['http_url'] ?? null;
        $method = strtolower($step['http_method'] ?? 'get');
        $headers = json_decode($step['http_headers_json'] ?? '{}', true) ?: [];
        $token = $step['http_auth_token'] ?? null;
        
        if (!$url) {
            return null;
        }
        
        if ($token) {
            $resolvedToken = $this->resolveValueRefExtended(['token' => $token, 'manual_value' => $token], 'token', $payload, $previousResults);
            $headers['Authorization'] = 'Bearer ' . $resolvedToken;
        }
        
        $resolvedUrl = $this->resolveStringTemplate($url, $payload, $previousResults);
        
        $http = Http::withHeaders($headers);
        
        $body = [];
        $columnMappings = $step['column_mappings'] ?? [];
        foreach ($columnMappings as $mapping) {
            $column = $mapping['column'] ?? null;
            $sourceRef = $mapping['source_ref'] ?? null;
            if ($column && $sourceRef) {
                $body[$column] = $this->resolveValueRefExtended($mapping, 'source_ref', $payload, $previousResults);
            }
        }
        
        $response = match ($method) {
            'post' => $http->post($resolvedUrl, $body),
            'put' => $http->put($resolvedUrl, $body),
            'patch' => $http->patch($resolvedUrl, $body),
            'delete' => $http->delete($resolvedUrl, $body),
            default => $http->get($resolvedUrl, $body),
        };
        
        return $response->json();
    }

    private function resolveValueRefExtended(array $item, string $refKey, array $payload, array $previousResults): mixed
    {
        $ref = $item[$refKey] ?? '';
        if ($ref === '__manual__') {
            return $item['manual_value'] ?? null;
        }
        return $this->resolveValueRef((string)$ref, $payload, $previousResults);
    }

    private function resolveValueRef(string $ref, array $payload, array $previousResults): mixed
    {
        if (str_starts_with($ref, 'payload.')) {
            $key = substr($ref, 8);
            return data_get($payload, $key);
        }
        
        if (str_starts_with($ref, 'action_alias.')) {
            $parts = explode('.', substr($ref, 13), 2);
            $alias = $parts[0] ?? null;
            $field = $parts[1] ?? null;
            
            if ($alias && isset($previousResults[$alias])) {
                $result = $previousResults[$alias];
                
                // If no specific field is requested, return the whole result
                if (!$field) {
                    return $result;
                }

                // If result is a collection, try to get the first item
                if ($result instanceof \Illuminate\Support\Collection) {
                    $firstItem = $result->first();
                    if ($firstItem) {
                        return data_get($firstItem, $field);
                    }
                    return null;
                }

                // If result is an array of objects/arrays (e.g. from get())
                if (is_array($result) && isset($result[0]) && (is_object($result[0]) || is_array($result[0]))) {
                     return data_get($result[0], $field);
                }
                
                return data_get($result, $field);
            }
            return null;
        }
        
        return $ref;
    }

    private function resolveStringTemplate(string $template, array $payload, array $previousResults): string
    {
        return preg_replace_callback('/\{([^}]+)\}/', function ($matches) use ($payload, $previousResults) {
            return $this->resolveValueRef($matches[1], $payload, $previousResults) ?? $matches[0];
        }, $template);
    }

    private function guardRawSql(string $sql): void
    {
        $sql = trim($sql);
        if ($sql === '') {
            throw new \Exception('Raw SQL must not be empty.', 422);
        }

        if (str_contains($sql, ';')) {
            throw new \Exception('Raw SQL with semicolon is not allowed.', 422);
        }

        $dangerousPattern = '/(--|\/\*|\*\/|\bunion\b|\bdrop\b|\btruncate\b|\balter\b|\bcreate\b|\bgrant\b|\brevoke\b|\binsert\b|\bupdate\b|\bdelete\b|\bbenchmark\b|\bsleep\b)/i';
        if (preg_match($dangerousPattern, $sql) === 1) {
            throw new \Exception('Raw SQL contains restricted keyword/pattern.', 422);
        }
    }

    private function logRequest(Request $request, ?CbApiBuilder $api, int $statusCode, string $statusText, float $startTime): void
    {
        $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

        CbApiRequestLog::query()->create([
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'status_code' => $statusCode,
            'status_text' => $statusText,
            'latency_ms' => $latencyMs,
            'is_error' => $statusCode >= 400,
        ]);

        if ($api) {
            // Update aggregate stats for this API
            // Note: In production we might use a background job, 
            // but for builder accuracy we do it here or limit the window.
            $windowLogs = CbApiRequestLog::query()
                ->where('endpoint', $request->path())
                ->where('method', $request->method())
                ->latest('created_at')
                ->limit(100)
                ->get();

            if ($windowLogs->isNotEmpty()) {
                $avgLatency = (int) round($windowLogs->avg('latency_ms') ?? 0);
                $errorCount = $windowLogs->where('is_error', true)->count();
                $errorRate = ($errorCount / $windowLogs->count()) * 100;

                $api->update([
                    'avg_response_ms' => $avgLatency,
                    'error_rate_percent' => $errorRate,
                ]);
            }
        }
    }

    public function swagger(): \Illuminate\View\View
    {
        $openapiUrl = route('cb.api.openapi.json');
        return view()->make('cb.api-builder::swagger', compact('openapiUrl'));
    }

    public function openapiJson(): JsonResponse
    {
        $apis = CbApiBuilder::query()->where('status', 'active')->get();
        $baseUrl = config('app.url', 'http://localhost:8000');
        
        $spec = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => config('app.name') . ' API Documentation',
                'version' => '1.0.0',
                'description' => 'Dynamic API documentation generated by CrudBooster API Builder',
            ],
            'servers' => [
                ['url' => $baseUrl . '/api', 'description' => 'Main API Server']
            ],
            'paths' => [],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                    ]
                ]
            ],
            'security' => [
                ['bearerAuth' => []]
            ]
        ];

        foreach ($apis as $api) {
            $path = '/' . ltrim($api->endpoint_path, '/');
            $method = strtolower($api->method);
            
            if (!isset($spec['paths'][$path])) {
                $spec['paths'][$path] = [];
            }

            $operation = [
                'summary' => $api->name,
                'description' => $api->description,
                'tags' => ['API Builder'],
                'responses' => [
                    '200' => [
                        'description' => 'Successful operation',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'success' => ['type' => 'boolean'],
                                        'data' => ['type' => 'object']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => ['description' => 'Unauthorized'],
                    '429' => ['description' => 'Too Many Requests']
                ]
            ];

            if ($method === 'get') {
                $parameters = [];
                foreach ($api->payload_schema as $field) {
                    $parameters[] = [
                        'name' => $field['key'],
                        'in' => 'query',
                        'required' => $field['required'] ?? false,
                        'description' => $field['description'] ?? '',
                        'schema' => [
                            'type' => $this->mapTypeToSwagger($field['type'] ?? 'string')
                        ]
                    ];
                }
                $operation['parameters'] = $parameters;
            } else {
                $properties = [];
                $required = [];
                foreach ($api->payload_schema as $field) {
                    $properties[$field['key']] = [
                        'type' => $this->mapTypeToSwagger($field['type'] ?? 'string'),
                        'description' => $field['description'] ?? ''
                    ];
                    if ($field['required'] ?? false) {
                        $required[] = $field['key'];
                    }
                }

                $operation['requestBody'] = [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => $properties,
                                'required' => $required
                            ]
                        ]
                    ]
                ];
            }

            $spec['paths'][$path][$method] = $operation;
        }

        return Response::json($spec);
    }

    private function mapTypeToSwagger(string $type): string
    {
        return match ($type) {
            'integer', 'int' => 'integer',
            'number', 'float', 'double' => 'number',
            'boolean', 'bool' => 'boolean',
            'array' => 'array',
            'object' => 'object',
            default => 'string',
        };
    }

    public static function registerDynamicRoutes(): void
    {
        $middleware = ['api'];
        if (config('cb.audit_log.enabled', true)) {
            $middleware[] = 'cb.audit';
        }

        Route::middleware($middleware)
            ->prefix('api')
            ->group(function () {
                Route::any('/{path}', [ApiBuilderController::class, 'handle'])
                    ->where('path', '.*')
                    ->name('cb.api.dynamic');
            });
    }
}
