<?php

namespace CrudBooster\Components\Type\Radio;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;
use Illuminate\Support\ServiceProvider;

class RadioServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-radio');
        CBTypeRegistrar::addSelect([
            'type'=>'radio',
            'form'=>'cb-type-radio::form',
            'view'=>'cb-type-radio::view',
            'clazz'=>Checkbox::class,
            'generalOption'=>false,
            'settingSupport'=>true,
            'settingFormConfig'=>__DIR__.'/Function/SettingFormConfig.php',
        ]);
    }
}
