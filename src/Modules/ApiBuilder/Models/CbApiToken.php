<?php

namespace CrudBooster\Modules\ApiBuilder\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CbApiToken extends Model
{
    use HasUuids;

    protected $table = 'cb_api_tokens';

    protected $fillable = [
        'name',
        'scope_endpoint',
        'auth_method',
        'status',
        'token_prefix',
        'token_hash',
        'token_encrypted',
        'failed_attempt_24h',
        'last_used_at',
    ];

    protected $casts = [
        'failed_attempt_24h' => 'integer',
        'last_used_at' => 'datetime',
    ];

    public function setTokenEncryptedAttribute(?string $value): void
    {
        $this->attributes['token_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getTokenEncryptedAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
