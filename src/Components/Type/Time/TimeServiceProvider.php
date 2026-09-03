<?php

namespace CrudBooster\Components\Type\Time;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Time\Function\Time;
use Illuminate\Support\ServiceProvider;

class TimeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-time');
        CBTypeRegistrar::addDateTime([
            'type'=>'time',
            'form'=>'cb-type-time::form',
            'view'=>'cb-type-time::view',
            'clazz'=>Time::class,
            'settingSupport'=>false,
            'generalOption'=>true]);
    }
}
