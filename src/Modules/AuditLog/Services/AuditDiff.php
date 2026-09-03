<?php

namespace CrudBooster\Modules\AuditLog\Services;

class AuditDiff
{
    public static function changed(array $before, array $after, array $ignoreKeys = []): array
    {
        $flatBefore = self::flatten($before);
        $flatAfter = self::flatten($after);

        $allKeys = array_unique(array_merge(array_keys($flatBefore), array_keys($flatAfter)));
        sort($allKeys);

        $beforeChanged = [];
        $afterChanged = [];
        $changedKeys = [];

        foreach ($allKeys as $key) {
            if (self::shouldIgnore($key, $ignoreKeys)) {
                continue;
            }

            $beforeValue = $flatBefore[$key] ?? null;
            $afterValue = $flatAfter[$key] ?? null;

            if ($beforeValue !== $afterValue) {
                $beforeChanged[$key] = $beforeValue;
                $afterChanged[$key] = $afterValue;
                $changedKeys[] = $key;
            }
        }

        return [$beforeChanged, $afterChanged, $changedKeys];
    }

    public static function flatten(array $data, string $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $dotKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $result += self::flatten($value, $dotKey);
                continue;
            }

            $result[$dotKey] = $value;
        }

        return $result;
    }

    public static function keys(array $data): array
    {
        return array_keys(self::flatten($data));
    }

    private static function shouldIgnore(string $dotKey, array $ignoreKeys): bool
    {
        if (in_array($dotKey, $ignoreKeys, true)) {
            return true;
        }

        $segments = explode('.', $dotKey);
        $baseKey = (string) end($segments);
        return in_array($baseKey, $ignoreKeys, true);
    }
}
