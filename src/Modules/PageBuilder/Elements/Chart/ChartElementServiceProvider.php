<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Chart;

use CrudBooster\Modules\PageBuilder\Elements\PageBuilderElementRegistrar;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ChartElementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageBuilderElementRegistrar::add('chart', 'Chart', ChartElement::class);
        Livewire::component('chart-viewer', ChartElementViewer::class);
    }
}
