<?php

namespace CrudBooster\Modules\ApiBuilder\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CbApiBuilder extends Model
{
    use HasUuids;

    protected $table = 'cb_api_builders';

    protected $fillable = [
        'name',
        'endpoint_path',
        'description',
        'method',
        'status',
        'rate_limit_enabled',
        'rate_limit_rpm',
        'payload_schema',
        'process_steps',
        'response_mapper',
        'cache_response_enabled',
        'avg_response_ms',
        'error_rate_percent',
    ];

    protected $casts = [
        'rate_limit_enabled' => 'boolean',
        'rate_limit_rpm' => 'integer',
        'payload_schema' => 'array',
        'process_steps' => 'array',
        'response_mapper' => 'array',
        'cache_response_enabled' => 'boolean',
        'avg_response_ms' => 'integer',
        'error_rate_percent' => 'float',
    ];
}
