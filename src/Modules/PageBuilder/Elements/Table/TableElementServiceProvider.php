<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Table;

use CrudBooster\Modules\PageBuilder\Elements\PageBuilderElementRegistrar;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TableElementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageBuilderElementRegistrar::add('table', 'Table', TableElement::class);
        Livewire::component('table-viewer', TableElementViewer::class);
    }
}
