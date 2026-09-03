<?php

namespace CrudBooster\Modules\AuditLog\Services;

use CrudBooster\Events\EventDataDeleted;
use CrudBooster\Events\EventDataDeleting;
use CrudBooster\Events\EventFormSaved;
use CrudBooster\Events\EventFormSaving;
use CrudBooster\Modules\AuditLog\Models\CbAuditLog;
use CrudBooster\Modules\Auth\Events\LoginAttemptFailed;
use CrudBooster\Modules\Auth\Events\LoginAttemptSuccess;
use CrudBooster\Modules\Auth\Events\LogoutSuccess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class AuditLogService
{
    private static array $pendingForm = [];
    private static array $pendingDelete = [];
    private static ?bool $tableExists = null;

    private AuditMasker $masker;

    public function __construct()
    {
        $this->masker = new AuditMasker(
            maskedFields: (array) config('cb.audit_log.masked_fields', []),
            maxPayloadLength: (int) config('cb.audit_log.max_payload_length', 4000),
        );
    }

    public function ensureRequestId(?Request $request = null): string
    {
        $request = $request ?: request();
        if (! $request instanceof Request) {
            return (string) Str::uuid();
        }

        $existing = (string) $request->attributes->get('cb_audit_request_id', '');
        if ($existing !== '') {
            return $existing;
        }

        $requestId = (string) Str::uuid();
        $request->attributes->set('cb_audit_request_id', $requestId);

        return $requestId;
    }

    public function captureRequest(Request $request, mixed $response): void
    {
        if (! $this->enabled()) {
            return;
        }

        $path = '/' . ltrim($request->path(), '/');
        if ($this->shouldSkipPath($path)) {
            return;
        }

        $statusCode = $response instanceof Response || method_exists($response, 'getStatusCode')
            ? (int) $response->getStatusCode()
            : 200;

        $requestPayload = $request->isMethod('GET')
            ? $request->query()
            : $request->except(['_token']);

        // Avoid noisy request-only logs for plain page GET without any query/body payload.
        if ($request->isMethod('GET') && empty($requestPayload)) {
            return;
        }

        $meta = $this->resolveRequestMeta($request);
        $actor = $this->resolveActorMeta();

        $this->record([
            'user_id' => $actor['user_id'],
            'user_email' => $actor['user_email'],
            'user_name' => $actor['user_name'],
            'module_key' => $this->resolveModuleKeyFromPath($meta['path']),
            'entity_type' => null,
            'entity_id' => null,
            'action' => 'request',
            'http_method' => $meta['http_method'],
            'path' => $meta['path'],
            'ip_address' => $meta['ip_address'],
            'user_agent' => $meta['user_agent'],
            'request_id' => $meta['request_id'],
            'before_data' => null,
            'after_data' => null,
            'changed_fields' => null,
            'request_payload' => $this->sanitizeArray($requestPayload),
            'outcome' => $this->resolveOutcome($statusCode),
            'created_at' => now(),
        ]);
    }

    public function captureRequestException(Request $request, \Throwable $exception): void
    {
        if (! $this->enabled()) {
            return;
        }

        $path = '/' . ltrim($request->path(), '/');
        if ($this->shouldSkipPath($path)) {
            return;
        }

        $statusCode = $exception instanceof HttpExceptionInterface
            ? (int) $exception->getStatusCode()
            : 500;

        $requestPayload = $request->isMethod('GET')
            ? $request->query()
            : $request->except(['_token']);

        $meta = $this->resolveRequestMeta($request);
        $actor = $this->resolveActorMeta();

        $this->record([
            'user_id' => $actor['user_id'],
            'user_email' => $actor['user_email'],
            'user_name' => $actor['user_name'],
            'module_key' => $this->resolveModuleKeyFromPath($meta['path']),
            'entity_type' => null,
            'entity_id' => null,
            'action' => 'request',
            'http_method' => $meta['http_method'],
            'path' => $meta['path'],
            'ip_address' => $meta['ip_address'],
            'user_agent' => $meta['user_agent'],
            'request_id' => $meta['request_id'],
            'before_data' => null,
            'after_data' => null,
            'changed_fields' => null,
            'request_payload' => $this->sanitizeArray($requestPayload),
            'outcome' => $this->resolveOutcome($statusCode),
            'created_at' => now(),
        ]);
    }

    public function onFormSaving(EventFormSaving $event): void
    {
        if (! $this->enabled()) {
            return;
        }

        $modelClass = (string) ($event->model ?? '');
        if ($modelClass === '' || $modelClass === CbAuditLog::class) {
            return;
        }

        $request = request();
        $requestId = $this->ensureRequestId($request instanceof Request ? $request : null);
        $beforeRaw = $event->id ? $this->resolveModelSnapshot($modelClass, $event->id) : [];
        $payloadRaw = $this->normalizeData($event->data ?? []);
        $action = $event->id ? 'update' : 'create';

        self::$pendingForm[$requestId][] = [
            'model' => $modelClass,
            'id' => $event->id,
            'action' => $action,
            'before_raw' => $beforeRaw,
            'payload_raw' => $payloadRaw,
        ];
    }

    public function onFormSaved(EventFormSaved $event): void
    {
        if (! $this->enabled()) {
            return;
        }

        $modelClass = (string) ($event->model ?? '');
        if ($modelClass === '' || $modelClass === CbAuditLog::class) {
            return;
        }

        $request = request();
        $requestId = $this->ensureRequestId($request instanceof Request ? $request : null);
        $context = $this->pullPendingForm($requestId, $modelClass, $event->id);

        $beforeRaw = $this->normalizeData($context['before_raw'] ?? []);
        $afterRaw = $this->normalizeData($this->resolveModelSnapshot($modelClass, $event->id));

        [$beforeChangedRaw, $afterChangedRaw, $changedKeys] = AuditDiff::changed(
            $beforeRaw,
            $afterRaw,
            ['updated_at']
        );

        $action = (string) ($context['action'] ?? ($context['id'] ? 'update' : 'create'));
        if ($action === 'update' && empty($changedKeys)) {
            // Prevent duplicate/no-op update logs from repeated Livewire lifecycle calls.
            return;
        }

        $beforeChanged = $this->sanitizeArray($beforeChangedRaw);
        $afterChanged = $this->sanitizeArray($afterChangedRaw);

        $meta = $this->resolveRequestMeta($request instanceof Request ? $request : null);
        $actor = $this->resolveActorMeta();

        $this->record([
            'user_id' => $actor['user_id'],
            'user_email' => $actor['user_email'],
            'user_name' => $actor['user_name'],
            'module_key' => $this->resolveModuleKeyFromPath($meta['path']),
            'entity_type' => $modelClass,
            'entity_id' => (string) $event->id,
            'action' => $action,
            'http_method' => $meta['http_method'],
            'path' => $meta['path'],
            'ip_address' => $meta['ip_address'],
            'user_agent' => $meta['user_agent'],
            'request_id' => $meta['request_id'],
            'before_data' => $beforeChanged,
            'after_data' => $afterChanged,
            'changed_fields' => $changedKeys,
            'request_payload' => $this->sanitizeArray($context['payload_raw'] ?? []),
            'outcome' => 'success',
            'created_at' => now(),
        ]);
    }

    public function onDataDeleting(EventDataDeleting $event): void
    {
        if (! $this->enabled()) {
            return;
        }

        $modelClass = (string) ($event->model ?? '');
        if ($modelClass === '' || $modelClass === CbAuditLog::class) {
            return;
        }

        $request = request();
        $requestId = $this->ensureRequestId($request instanceof Request ? $request : null);
        $beforeRaw = $this->normalizeData($event->data ?? []);
        $pendingKey = $this->deletePendingKey($requestId, $modelClass, (string) $event->id);

        self::$pendingDelete[$pendingKey] = [
            'model' => $modelClass,
            'id' => (string) $event->id,
            'before_raw' => $beforeRaw,
        ];
    }

    public function onDataDeleted(EventDataDeleted $event): void
    {
        if (! $this->enabled()) {
            return;
        }

        $modelClass = (string) ($event->model ?? '');
        if ($modelClass === '' || $modelClass === CbAuditLog::class) {
            return;
        }

        $request = request();
        $requestId = $this->ensureRequestId($request instanceof Request ? $request : null);
        $pendingKey = $this->deletePendingKey($requestId, $modelClass, (string) $event->id);
        $context = self::$pendingDelete[$pendingKey] ?? [
            'before_raw' => $this->normalizeData($event->data ?? []),
        ];
        unset(self::$pendingDelete[$pendingKey]);

        $beforeSanitized = $this->sanitizeArray($context['before_raw'] ?? []);
        $changedKeys = AuditDiff::keys($beforeSanitized);
        $meta = $this->resolveRequestMeta($request instanceof Request ? $request : null);
        $actor = $this->resolveActorMeta();

        $this->record([
            'user_id' => $actor['user_id'],
            'user_email' => $actor['user_email'],
            'user_name' => $actor['user_name'],
            'module_key' => $this->resolveModuleKeyFromPath($meta['path']),
            'entity_type' => $modelClass,
            'entity_id' => (string) $event->id,
            'action' => 'delete',
            'http_method' => $meta['http_method'],
            'path' => $meta['path'],
            'ip_address' => $meta['ip_address'],
            'user_agent' => $meta['user_agent'],
            'request_id' => $meta['request_id'],
            'before_data' => $beforeSanitized,
            'after_data' => [],
            'changed_fields' => $changedKeys,
            'request_payload' => null,
            'outcome' => 'success',
            'created_at' => now(),
        ]);
    }

    public function onLoginSuccess(LoginAttemptSuccess $event): void
    {
        if (! $this->enabled()) {
            return;
        }

        $request = request();
        $meta = $this->resolveRequestMeta($request instanceof Request ? $request : null);
        $actor = $this->resolveActorMeta($event->user ?? null);

        $this->record([
            'user_id' => $actor['user_id'],
            'user_email' => $actor['user_email'],
            'user_name' => $actor['user_name'],
            'module_key' => 'auth',
            'entity_type' => $actor['entity_type'],
            'entity_id' => $actor['entity_id'],
            'action' => 'login',
            'http_method' => $meta['http_method'],
            'path' => $meta['path'],
            'ip_address' => $meta['ip_address'],
            'user_agent' => $meta['user_agent'],
            'request_id' => $meta['request_id'],
            'before_data' => null,
            'after_data' => null,
            'changed_fields' => null,
            'request_payload' => $this->sanitizeArray([
                'email' => $actor['user_email'],
            ]),
            'outcome' => 'success',
            'created_at' => now(),
        ]);
    }

    public function onLoginFailed(LoginAttemptFailed $event): void
    {
        if (! $this->enabled()) {
            return;
        }

        $request = request();
        $meta = $this->resolveRequestMeta($request instanceof Request ? $request : null);

        $this->record([
            'user_id' => null,
            'user_email' => is_string($event->email ?? null) ? $event->email : null,
            'user_name' => null,
            'module_key' => 'auth',
            'entity_type' => null,
            'entity_id' => null,
            'action' => 'login',
            'http_method' => $meta['http_method'],
            'path' => $meta['path'],
            'ip_address' => $meta['ip_address'],
            'user_agent' => $meta['user_agent'],
            'request_id' => $meta['request_id'],
            'before_data' => null,
            'after_data' => null,
            'changed_fields' => null,
            'request_payload' => $this->sanitizeArray([
                'email' => is_string($event->email ?? null) ? $event->email : null,
            ]),
            'outcome' => 'failed',
            'created_at' => now(),
        ]);
    }

    public function onLogoutSuccess(LogoutSuccess $event): void
    {
        if (! $this->enabled()) {
            return;
        }

        $request = request();
        $meta = $this->resolveRequestMeta($request instanceof Request ? $request : null);
        $actor = $this->resolveActorMeta($event->user ?? null);

        $this->record([
            'user_id' => $actor['user_id'],
            'user_email' => $actor['user_email'],
            'user_name' => $actor['user_name'],
            'module_key' => 'auth',
            'entity_type' => $actor['entity_type'],
            'entity_id' => $actor['entity_id'],
            'action' => 'logout',
            'http_method' => $meta['http_method'],
            'path' => $meta['path'],
            'ip_address' => $meta['ip_address'],
            'user_agent' => $meta['user_agent'],
            'request_id' => $meta['request_id'],
            'before_data' => null,
            'after_data' => null,
            'changed_fields' => null,
            'request_payload' => null,
            'outcome' => 'success',
            'created_at' => now(),
        ]);
    }

    public function prune(int $days): int
    {
        if (! $this->enabled()) {
            return 0;
        }

        return CbAuditLog::query()
            ->where('created_at', '<', now()->subDays(max(1, $days)))
            ->delete();
    }

    private function pullPendingForm(string $requestId, string $modelClass, mixed $savedId): array
    {
        $items = self::$pendingForm[$requestId] ?? [];
        $savedId = $savedId !== null ? (string) $savedId : null;

        foreach ($items as $index => $item) {
            if (($item['model'] ?? '') !== $modelClass) {
                continue;
            }

            $itemId = isset($item['id']) ? (string) $item['id'] : null;
            if ($savedId !== null && $itemId !== null && $itemId === $savedId) {
                unset($items[$index]);
                self::$pendingForm[$requestId] = array_values($items);
                return $item;
            }
        }

        foreach ($items as $index => $item) {
            if (($item['model'] ?? '') !== $modelClass) {
                continue;
            }

            if (($item['action'] ?? '') === 'create') {
                unset($items[$index]);
                self::$pendingForm[$requestId] = array_values($items);
                return $item;
            }
        }

        return [
            'model' => $modelClass,
            'id' => $savedId,
            'action' => $savedId ? 'update' : 'create',
            'before_raw' => [],
            'payload_raw' => [],
        ];
    }

    private function deletePendingKey(string $requestId, string $modelClass, string $id): string
    {
        return $requestId . '|' . $modelClass . '|' . $id;
    }

    private function resolveModelSnapshot(string $modelClass, mixed $id): array
    {
        try {
            if (!class_exists($modelClass) || $id === null) {
                return [];
            }

            $data = $modelClass::query()->find($id);
            if (! $data) {
                return [];
            }

            return $data->toArray();
        } catch (\Throwable) {
            return [];
        }
    }

    private function normalizeData(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            /** @var array $arrayValue */
            $arrayValue = $value->toArray();
            return $arrayValue;
        }

        return [];
    }

    private function sanitizeArray(mixed $value): array
    {
        $normalized = $this->normalizeData($value);
        $sanitized = $this->masker->sanitize($normalized);
        return is_array($sanitized) ? $sanitized : [];
    }

    private function resolveRequestMeta(?Request $request): array
    {
        if (! $request) {
            return [
                'http_method' => null,
                'path' => null,
                'ip_address' => null,
                'user_agent' => null,
                'request_id' => (string) Str::uuid(),
            ];
        }

        $effectivePath = $this->resolveEffectivePath($request);

        return [
            'http_method' => strtoupper((string) $request->method()),
            'path' => $effectivePath,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'request_id' => $this->ensureRequestId($request),
        ];
    }

    private function resolveEffectivePath(Request $request): string
    {
        $rawPath = '/' . ltrim($request->path(), '/');
        if (! in_array($rawPath, ['/livewire/update'], true) && ! str_starts_with($rawPath, '/livewire/message/')) {
            return $rawPath;
        }

        $snapshotPath = $this->resolveLivewirePathFromSnapshot($request);
        if ($snapshotPath) {
            return $snapshotPath;
        }

        $referer = (string) $request->headers->get('referer', '');
        $refererPath = is_string($referer) ? parse_url($referer, PHP_URL_PATH) : null;
        if (is_string($refererPath) && trim($refererPath) !== '') {
            return '/' . ltrim($refererPath, '/');
        }

        return $rawPath;
    }

    private function resolveLivewirePathFromSnapshot(Request $request): ?string
    {
        $components = $request->input('components');
        if (! is_array($components)) {
            return null;
        }

        $snapshot = $components[0]['snapshot'] ?? null;
        if (! is_string($snapshot) || trim($snapshot) === '') {
            return null;
        }

        $decoded = json_decode($snapshot, true);
        if (! is_array($decoded)) {
            return null;
        }

        $memoPath = $decoded['memo']['path'] ?? null;
        if (! is_string($memoPath) || trim($memoPath) === '') {
            return null;
        }

        return '/' . ltrim($memoPath, '/');
    }

    private function resolveActorMeta(mixed $user = null): array
    {
        if (! $user) {
            $user = Auth::user();
        }

        if (! $user) {
            return [
                'user_id' => null,
                'user_email' => null,
                'user_name' => null,
                'entity_type' => null,
                'entity_id' => null,
            ];
        }

        return [
            'user_id' => isset($user->id) ? (string) $user->id : null,
            'user_email' => $user->email ?? null,
            'user_name' => $user->name ?? null,
            'entity_type' => get_class($user),
            'entity_id' => isset($user->id) ? (string) $user->id : null,
        ];
    }

    private function resolveOutcome(int $statusCode): string
    {
        if (in_array($statusCode, [401, 403], true)) {
            return 'blocked';
        }

        if ($statusCode >= 400) {
            return 'failed';
        }

        return 'success';
    }

    private function shouldSkipPath(string $path): bool
    {
        $normalizedPath = '/' . ltrim($path, '/');
        $adminPath = trim((string) config('cb.admin_path', 'cms'), '/');
        $auditPathPattern = '/' . $adminPath . '/audit-log*';
        if (Str::is($auditPathPattern, $normalizedPath)) {
            return true;
        }

        if ($normalizedPath === '/favicon.ico') {
            return true;
        }

        foreach ((array) config('cb.audit_log.skip_paths', []) as $pattern) {
            $pattern = '/' . ltrim((string) $pattern, '/');
            if (Str::is($pattern, $normalizedPath)) {
                return true;
            }
        }

        return false;
    }

    private function resolveModuleKeyFromPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $clean = trim($path, '/');
        if ($clean === '') {
            return null;
        }

        $adminPath = trim((string) config('cb.admin_path', 'cms'), '/');
        if ($clean === $adminPath) {
            return (string) config('cb.dashboard_path', 'dashboard');
        }

        if (str_starts_with($clean, $adminPath . '/')) {
            $rest = substr($clean, strlen($adminPath) + 1);
            if ($rest === false || $rest === '') {
                return (string) config('cb.dashboard_path', 'dashboard');
            }

            $segments = explode('/', $rest);
            return $segments[0] ?? null;
        }

        if (str_starts_with($clean, 'api/')) {
            return 'api';
        }

        $segments = explode('/', $clean);
        return $segments[0] ?? null;
    }

    private function enabled(): bool
    {
        return (bool) config('cb.audit_log.enabled', true) && $this->hasAuditTable();
    }

    private function hasAuditTable(): bool
    {
        if (self::$tableExists === true) {
            return true;
        }

        try {
            $exists = Schema::hasTable('cb_audit_logs');
            self::$tableExists = $exists ? true : null;
            return $exists;
        } catch (\Throwable $e) {
            Log::warning('Audit log table check failed: ' . $e->getMessage());
            self::$tableExists = null;
            return false;
        }
    }

    private function record(array $payload): void
    {
        if (! $this->hasAuditTable()) {
            return;
        }

        try {
            CbAuditLog::query()->create($payload);
        } catch (\Throwable $e) {
            Log::warning('Failed writing audit log: ' . $e->getMessage());
        }
    }
}
