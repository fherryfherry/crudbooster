<?php

namespace CrudBooster\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchemaUtil
{
    public function getTableListing()
    {
        return collect(Schema::getTableListing())->map(fn($table) => str_replace(DB::getDatabaseName().'.', '', $table))->toArray();
    }
}
