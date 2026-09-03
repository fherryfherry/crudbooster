<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\PageBuilder\Livewire\PageBuilder;
use CrudBooster\Modules\PageBuilder\Livewire\PageBuilderStudio;
use CrudBooster\Modules\PageBuilder\Livewire\PageBuilderViewer;

// Page Builder routes
CBRoute::createRouteOne('page-builder', PageBuilder::class, ['web', 'auth']);
CBRoute::createRouteOne('page-builder/create', PageBuilderStudio::class, ['web', 'auth']);
CBRoute::createRouteOne('page-builder/{id}/studio', PageBuilderStudio::class, ['web', 'auth']);
CBRoute::createRouteOne('p/{id}', PageBuilderViewer::class, ['web', 'auth']);
