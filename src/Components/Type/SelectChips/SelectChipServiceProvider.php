<?php

namespace CrudBooster\Components\Type\SelectChips;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\SelectChips\Function\SelectChips;
use Illuminate\Support\ServiceProvider;

class SelectChipServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/stub', 'cb-type-selectChips');
        CBTypeRegistrar::addSelect([
            'type' => 'selectChips',
            'form' => 'cb-type-selectChips::form',
            'view' => 'cb-type-selectChips::view',
            'clazz' => SelectChips::class,
            'settingSupport'=> false,
            'generalOption' => false
        ]);
    }
}
