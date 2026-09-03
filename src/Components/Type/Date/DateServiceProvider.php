<?php

namespace CrudBooster\Components\Type\Date;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Date\Function\Date;
use Illuminate\Support\ServiceProvider;

class DateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-date');
        CBTypeRegistrar::addDateTime([
            'type'=>'date',
            'form'=>'cb-type-date::form',
            'view'=>'cb-type-date::view',
            'clazz'=>Date::class,
            'settingSupport'=>false,
            'generalOption'=>true,
            'optionList'=> __DIR__.'/Function/OptionList.php'
        ]);
    }
}
