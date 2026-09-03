<?php

namespace CrudBooster\Components\Type\EmptyField;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\EmptyField\Function\EmptyField;
use Illuminate\Support\ServiceProvider;

class EmptyFieldServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-empty-field');
        CBTypeRegistrar::add('empty', 'cb-type-empty-field::form', 'cb-type-empty-field::view', EmptyField::class, false, false);
    }
}
