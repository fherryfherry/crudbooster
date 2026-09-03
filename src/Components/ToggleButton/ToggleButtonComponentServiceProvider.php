<?php

namespace CrudBooster\Components\ToggleButton;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ToggleButtonComponentServiceProvider extends ServiceProvider
{
    // boot
    public function boot()
    {
        Blade::component('toggle-button', ToggleButton::class);
    }

    // register
    public function register()
    {

    }

}
