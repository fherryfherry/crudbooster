<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\Menu\Livewire\Menu;
use CrudBooster\Modules\Menu\Livewire\MenuForm;

CBRoute::createRoute('menu', Menu::class, MenuForm::class);