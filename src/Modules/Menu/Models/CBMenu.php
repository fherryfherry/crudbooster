<?php

namespace CrudBooster\Modules\Menu\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CBMenu extends Model
{
    use HasUuids;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cb_menus';

    protected $fillable = ['icon', 'menu_type', 'menu_value', 'name', 'is_dashboard', 'tag', 'menu_order', 'parent_id'];

    // Hide for id and timestamps
    protected $hidden = ['id', 'created_at', 'updated_at'];
}
