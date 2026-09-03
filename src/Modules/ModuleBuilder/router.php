<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\ModuleBuilder\Livewire\Module;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormActionButton;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormBasicInfo;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormBrowseDesign;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormBulkAction;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormFormDesign;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormFormHook;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormHookQuery;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormRelationship;
use CrudBooster\Modules\ModuleBuilder\Livewire\ModuleFormTableSchema;

// Module Builder routes
CBRoute::createRouteOne('module-builder', Module::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/create', ModuleFormBasicInfo::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/info', ModuleFormBasicInfo::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/table-schema', ModuleFormTableSchema::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/relationship', ModuleFormRelationship::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/hook-query', ModuleFormHookQuery::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/browse-design', ModuleFormBrowseDesign::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/bulk-action', ModuleFormBulkAction::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/action-button', ModuleFormActionButton::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/form-design', ModuleFormFormDesign::class, ['web', 'auth']);
CBRoute::createRouteOne('module-builder/{uuid}/form-hook', ModuleFormFormHook::class, ['web', 'auth']);
