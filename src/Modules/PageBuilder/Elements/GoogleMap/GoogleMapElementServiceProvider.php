<?php

namespace CrudBooster\Modules\PageBuilder\Elements\GoogleMap;

use CrudBooster\Modules\PageBuilder\Elements\PageBuilderElementRegistrar;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class GoogleMapElementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageBuilderElementRegistrar::add('google-map', 'Google Map', GoogleMapElement::class);
        Livewire::component('google-map-viewer', GoogleMapElementViewer::class);
    }
}
