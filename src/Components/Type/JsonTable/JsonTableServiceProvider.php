<?php

namespace CrudBooster\Components\Type\JsonTable;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\JsonTable\Function\JsonTable;
use Illuminate\Support\ServiceProvider;

class JsonTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-jsonTable');
        CBTypeRegistrar::addJson('jsonTable', 'cb-type-jsonTable::form', 'cb-type-jsonTable::view', JsonTable::class);
    }
}
