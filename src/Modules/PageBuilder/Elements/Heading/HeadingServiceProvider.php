<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Heading;

use CrudBooster\Modules\PageBuilder\Elements\PageBuilderElementRegistrar;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class HeadingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageBuilderElementRegistrar::add('heading', 'Heading', HeadingElement::class);
        Livewire::component('heading-viewer', HeadingElementViewer::class);
    }
}
