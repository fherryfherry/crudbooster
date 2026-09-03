<?php

namespace CrudBooster\Components\Type\SelectIcon;

use CrudBooster\Components\SelectIcon\SelectIcon;
use CrudBooster\Components\Type\CBTypeRegistrar;
use Illuminate\Support\ServiceProvider;

class SelectIconServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/stub', 'cb-type-select-icon');
        CBTypeRegistrar::addSelect([
            'type' => 'selectIcon',
            'form' => 'cb-type-select-icon::form',
            'view' => 'cb-type-select-icon::view',
            'clazz' => SelectIcon::class,
            'generalOption' => false,
            'settingSupport' => false,
            'settingFormConfig' => null,
        ]);
    }
}
