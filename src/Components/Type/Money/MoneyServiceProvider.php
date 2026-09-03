<?php

namespace CrudBooster\Components\Type\Money;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Money\Function\Money;
use Illuminate\Support\ServiceProvider;

class MoneyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-money');
        CBTypeRegistrar::addNumeric('money', 'cb-type-money::form', 'cb-type-money::view', Money::class, false, true);
    }
}
