<?php

namespace CrudBooster\Tests\Unit;

use CrudBooster\Modules\AuditLog\Services\AuditDiff;
use PHPUnit\Framework\TestCase;

class AuditDiffTest extends TestCase
{
    public function test_changed_returns_only_modified_fields(): void
    {
        [$beforeChanged, $afterChanged, $changedKeys] = AuditDiff::changed(
            [
                'name' => 'Alpha',
                'email' => 'alpha@example.com',
                'meta' => ['role' => 'user'],
            ],
            [
                'name' => 'Beta',
                'email' => 'alpha@example.com',
                'meta' => ['role' => 'admin'],
            ]
        );

        $this->assertSame(['meta.role' => 'user', 'name' => 'Alpha'], $beforeChanged);
        $this->assertSame(['meta.role' => 'admin', 'name' => 'Beta'], $afterChanged);
        $this->assertSame(['meta.role', 'name'], $changedKeys);
    }

    public function test_changed_ignores_configured_keys(): void
    {
        [$beforeChanged, $afterChanged, $changedKeys] = AuditDiff::changed(
            [
                'updated_at' => '2026-03-26 10:00:00',
                'meta' => ['updated_at' => '2026-03-26 10:00:00'],
                'name' => 'Alpha',
            ],
            [
                'updated_at' => '2026-03-26 10:05:00',
                'meta' => ['updated_at' => '2026-03-26 10:05:00'],
                'name' => 'Beta',
            ],
            ['updated_at']
        );

        $this->assertSame(['name' => 'Alpha'], $beforeChanged);
        $this->assertSame(['name' => 'Beta'], $afterChanged);
        $this->assertSame(['name'], $changedKeys);
    }

    public function test_flatten_converts_nested_array_to_dot_notation(): void
    {
        $flattened = AuditDiff::flatten([
            'profile' => [
                'name' => 'Tester',
                'settings' => [
                    'timezone' => 'Asia/Jakarta',
                ],
            ],
            'email' => 'tester@example.com',
        ]);

        $this->assertSame([
            'profile.name' => 'Tester',
            'profile.settings.timezone' => 'Asia/Jakarta',
            'email' => 'tester@example.com',
        ], $flattened);
    }

    public function test_keys_returns_all_flattened_keys(): void
    {
        $keys = AuditDiff::keys([
            'id' => 1,
            'meta' => [
                'role' => 'admin',
                'active' => true,
            ],
        ]);

        $this->assertSame(['id', 'meta.role', 'meta.active'], $keys);
    }
}
