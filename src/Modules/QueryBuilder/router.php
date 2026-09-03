<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\QueryBuilder\Livewire\QueryBuilder;
use CrudBooster\Modules\QueryBuilder\Livewire\QueryBuilderForm;

// Query Builder routes
CBRoute::createRouteOne('query-builder', QueryBuilder::class, ['web', 'auth']);
CBRoute::createRouteOne('query-builder/create', QueryBuilderForm::class, ['web', 'auth']);
CBRoute::createRouteOne('query-builder/{id}/form', QueryBuilderForm::class, ['web', 'auth']);
