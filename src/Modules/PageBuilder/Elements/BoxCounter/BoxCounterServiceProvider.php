<?php

namespace CrudBooster\Modules\PageBuilder\Elements\BoxCounter;

use CrudBooster\Modules\PageBuilder\Elements\PageBuilderElementRegistrar;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class BoxCounterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageBuilderElementRegistrar::add('box-counter', 'Box Counter', BoxCounterElement::class);
        Livewire::component('box-counter-viewer', BoxCounterElementViewer::class);
    }
}
