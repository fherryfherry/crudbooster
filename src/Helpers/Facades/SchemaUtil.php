<?php

namespace CrudBooster\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getTableListing()
 */
class SchemaUtil extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SchemaUtil';
    }
}
