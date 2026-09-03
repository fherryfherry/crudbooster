<?php

namespace CrudBooster\Tests\Unit;

use CrudBooster\Modules\AuditLog\Services\AuditMasker;
use PHPUnit\Framework\TestCase;

class AuditMaskerTest extends TestCase
{
    public function test_masks_sensitive_keys_with_default_policy(): void
    {
        $masker = new AuditMasker();

        $sanitized = $masker->sanitize([
            'email' => 'user@example.com',
            'password' => 'super-secret',
            'meta' => [
                'authorization' => 'Bearer abc',
                'refresh_token' => 'refresh-123',
                'note' => 'safe',
            ],
            'api_key_value' => 'key-123',
        ]);

        $this->assertSame('user@example.com', $sanitized['email']);
        $this->assertSame('[MASKED]', $sanitized['password']);
        $this->assertSame('[MASKED]', $sanitized['meta']['authorization']);
        $this->assertSame('[MASKED]', $sanitized['meta']['refresh_token']);
        $this->assertSame('safe', $sanitized['meta']['note']);
        $this->assertSame('[MASKED]', $sanitized['api_key_value']);
    }

    public function test_masks_using_custom_sensitive_fields(): void
    {
        $masker = new AuditMasker(maskedFields: ['pin_code']);

        $sanitized = $masker->sanitize([
            'pin_code' => '1234',
            'password' => 'not-masked-with-custom-only',
        ]);

        $this->assertSame('[MASKED]', $sanitized['pin_code']);
        $this->assertSame('not-masked-with-custom-only', $sanitized['password']);
    }

    public function test_truncates_long_strings_by_max_payload_length(): void
    {
        $masker = new AuditMasker(maxPayloadLength: 5);

        $sanitized = $masker->sanitize([
            'note' => 'abcdefghij',
        ]);

        $this->assertSame('abcde...(truncated)', $sanitized['note']);
    }

    public function test_sanitizes_object_with_to_array(): void
    {
        $masker = new AuditMasker();
        $payload = new class {
            public function toArray(): array
            {
                return [
                    'name' => 'Tester',
                    'client_secret' => 's3cr3t',
                ];
            }
        };

        $sanitized = $masker->sanitize($payload);

        $this->assertSame('Tester', $sanitized['name']);
        $this->assertSame('[MASKED]', $sanitized['client_secret']);
    }
}
