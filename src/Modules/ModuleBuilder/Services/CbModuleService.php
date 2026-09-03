<?php

namespace CrudBooster\Modules\ModuleBuilder\Services;

use CrudBooster\Domain\Services\BaseService;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use CrudBooster\Modules\Role\Models\CBRole;

class CbModuleService extends BaseService
{
    protected static string $model = CbModule::class;
}
