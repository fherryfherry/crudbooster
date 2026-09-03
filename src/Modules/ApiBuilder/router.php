<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderCreate;
use CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderList;

CBRoute::createRouteOne('api-builder', ApiBuilderList::class, ['web', 'auth']);
CBRoute::createRouteOne('api-builder/create', ApiBuilderCreate::class, ['web', 'auth']);
CBRoute::createRouteOne('api-builder/edit', ApiBuilderCreate::class, ['web', 'auth']);

Route::get(getCmsPath('api-docs'), [\CrudBooster\Modules\ApiBuilder\Http\ApiBuilderController::class, 'swagger'])
    ->middleware(['web', 'auth', 'cb.audit'])
    ->name('cb.api.swagger');

Route::get(getCmsPath('api-docs/openapi.json'), [\CrudBooster\Modules\ApiBuilder\Http\ApiBuilderController::class, 'openapiJson'])
    ->middleware(['web', 'auth', 'cb.audit'])
    ->name('cb.api.openapi.json');
