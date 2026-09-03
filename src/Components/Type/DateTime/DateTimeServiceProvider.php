<?php

namespace CrudBooster\Components\Type\DateTime;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\DateTime\Function\DateTime;
use Illuminate\Support\ServiceProvider;

class DateTimeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-datetime');
        CBTypeRegistrar::addDateTime([
            'type'=>'datetime',
            'form'=>'cb-type-datetime::form',
            'view'=>'cb-type-datetime::view',
            'clazz'=>DateTime::class,
            'settingSupport'=>false,
            'generalOption'=>true]);
    }
}
