<?php

namespace CrudBooster\Components\AlertMessage;

use CrudBooster\Components\AlertMessage\Livewire\AlertMessage;
use CrudBooster\Themes\CbThemeAssetRegistrar;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AlertMessageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/view', 'cb.alert');
        // Register asset
        CbThemeAssetRegistrar::addJs('vendor/crudbooster/themes/assets/js/anime.min.js');
        Livewire::component('alert-message', AlertMessage::class);
    }
}
