<?php

namespace CrudBooster\Modules\AuditLog\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CbAuditLog extends Model
{
    use HasUuids;

    protected $table = 'cb_audit_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_email',
        'user_name',
        'module_key',
        'entity_type',
        'entity_id',
        'action',
        'http_method',
        'path',
        'ip_address',
        'user_agent',
        'request_id',
        'before_data',
        'after_data',
        'changed_fields',
        'request_payload',
        'outcome',
        'created_at',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
        'changed_fields' => 'array',
        'request_payload' => 'array',
        'created_at' => 'datetime',
    ];
}

