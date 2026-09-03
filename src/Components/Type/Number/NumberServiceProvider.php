<?php

namespace CrudBooster\Components\Type\Number;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Number\Function\Number;
use Illuminate\Support\ServiceProvider;

class NumberServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-number');
        CBTypeRegistrar::addNumeric('number', 'cb-type-number::form', 'cb-type-number::view', Number::class, false, true);
    }
}
