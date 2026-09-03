<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Paragraph;

use CrudBooster\Modules\PageBuilder\Elements\PageBuilderElementRegistrar;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ParagraphServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageBuilderElementRegistrar::add('paragraph', 'Paragraph', ParagraphElement::class);
        Livewire::component('paragraph-viewer', ParagraphElementViewer::class);
    }
}
