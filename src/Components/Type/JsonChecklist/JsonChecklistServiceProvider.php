<?php

namespace CrudBooster\Components\Type\JsonChecklist;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\JsonChecklist\Function\JsonChecklist;
use Illuminate\Support\ServiceProvider;

class JsonChecklistServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-jsonChecklist');
        CBTypeRegistrar::addJson('jsonChecklist', 'cb-type-jsonChecklist::form', 'cb-type-jsonChecklist::view', JsonChecklist::class);
    }
}
