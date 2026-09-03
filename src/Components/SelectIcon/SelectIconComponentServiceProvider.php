<?php

namespace CrudBooster\Components\SelectIcon;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SelectIconComponentServiceProvider extends ServiceProvider
{
    // boot
    public function boot()
    {
        Livewire::component('select-icon', SelectIcon::class);
    }

    // register
    public function register()
    {

    }

}
