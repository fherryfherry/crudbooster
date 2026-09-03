<?php

namespace CrudBooster\Modules\Role\Services;

use CrudBooster\Domain\Services\BaseService;
use CrudBooster\Modules\Role\Models\CBRole;

class CbRoleService extends BaseService
{
    protected static string $model = CBRole::class;
}