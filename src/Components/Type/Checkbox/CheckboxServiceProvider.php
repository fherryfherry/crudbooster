<?php

namespace CrudBooster\Components\Type\Checkbox;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;
use Illuminate\Support\ServiceProvider;

class CheckboxServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-checkbox');
        CBTypeRegistrar::addSelect([
            'type'=>'checkbox',
            'form'=>'cb-type-checkbox::form',
            'view'=>'cb-type-checkbox::view',
            'clazz'=>Checkbox::class,
            'generalOption'=>false,
            'settingSupport'=>true,
            'settingFormConfig'=>__DIR__.'/Function/SettingFormConfig.php',
        ]);
    }
}
