<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\Role\Livewire\Role;
use CrudBooster\Modules\Role\Livewire\RoleForm;

CBRoute::createRoute('role', Role::class, RoleForm::class);