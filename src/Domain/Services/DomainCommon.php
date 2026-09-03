<?php

namespace CrudBooster\Domain\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DomainCommon
{
    public static $columns;

    public static function getColumns(Model $model)
    {
        $table = $model->getTable();
        if (!isset(self::$columns[$table])) {
            self::$columns[$table] = Schema::getColumnListing($table);
        }
        return self::$columns[$table];
    }

}