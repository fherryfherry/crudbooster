<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Image;

use CrudBooster\Modules\PageBuilder\Elements\PageBuilderElementRegistrar;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ImageElementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageBuilderElementRegistrar::add('image', 'Image', ImageElement::class);
        Livewire::component('image-viewer', ImageElementViewer::class);
    }
}
