<?php

namespace CrudBooster\Components\Type\Select;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Select\Function\Select;
use CrudBooster\Components\Type\Select\Function\SelectComponent;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SelectServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-select');
        CBTypeRegistrar::addSelect([
            'type'=>'select',
            'form'=>'cb-type-select::form',
            'view'=>'cb-type-select::view',
            'clazz'=>Select::class,
            'generalOption'=>false,
            'settingSupport'=>true,
            'settingFormConfig'=>__DIR__.'/Function/SettingFormConfig.php',
        ]);
        Livewire::component('select-component', SelectComponent::class);
    }
}
