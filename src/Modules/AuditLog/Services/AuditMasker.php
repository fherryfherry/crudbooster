<?php

namespace CrudBooster\Modules\AuditLog\Services;

class AuditMasker
{
    public function __construct(
        private readonly array $maskedFields = [],
        private readonly int $maxPayloadLength = 4000,
    ) {
    }

    public function sanitize(mixed $value, ?string $key = null): mixed
    {
        if ($key !== null && $this->isSensitiveKey($key)) {
            return '[MASKED]';
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->sanitize($v, is_string($k) ? $k : null);
            }
            return $result;
        }

        if (is_object($value)) {
            if (method_exists($value, 'toArray')) {
                /** @var array $converted */
                $converted = $value->toArray();
                return $this->sanitize($converted, $key);
            }

            return $this->truncateString((string) json_encode($value, JSON_UNESCAPED_SLASHES));
        }

        if (is_string($value)) {
            return $this->truncateString($value);
        }

        return $value;
    }

    private function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower($key);
        $maskedFields = $this->maskedFields ?: [
            'password',
            'token',
            'secret',
            'authorization',
            'remember_token',
            'api_key',
        ];

        foreach ($maskedFields as $sensitive) {
            $sensitive = strtolower((string) $sensitive);
            if ($sensitive !== '' && str_contains($normalized, $sensitive)) {
                return true;
            }
        }

        return false;
    }

    private function truncateString(string $value): string
    {
        if ($this->maxPayloadLength <= 0) {
            return $value;
        }

        if (mb_strlen($value) <= $this->maxPayloadLength) {
            return $value;
        }

        return mb_substr($value, 0, $this->maxPayloadLength) . '...(truncated)';
    }
}

