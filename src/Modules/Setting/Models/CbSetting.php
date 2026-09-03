<?php

namespace CrudBooster\Modules\Setting\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CbSetting extends Model
{
    use HasUuids;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cb_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['*'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'general_setting' => 'array',
        'production_setting' => 'array',
        'staging_setting' => 'array',
        'development_setting' => 'array',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
