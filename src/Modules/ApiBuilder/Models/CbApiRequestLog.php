<?php

namespace CrudBooster\Modules\ApiBuilder\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CbApiRequestLog extends Model
{
    use HasUuids;

    protected $table = 'cb_api_request_logs';

    protected $fillable = [
        'endpoint',
        'method',
        'status_code',
        'status_text',
        'latency_ms',
        'is_error',
        'created_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'latency_ms' => 'integer',
        'is_error' => 'boolean',
        'created_at' => 'datetime',
    ];
}
