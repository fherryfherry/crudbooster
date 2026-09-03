<?php

namespace CrudBooster\Modules\Menu;

use CrudBooster\Modules\Menu\Livewire\Menu;
use CrudBooster\Modules\Menu\Livewire\MenuForm;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use Illuminate\Support\ServiceProvider;

class CbMenuServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/router.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        ModuleRegistrar::registerModule(key: 'menu',
            name: 'Menu Management',
            browseModuleClass: Menu::class,
            formModuleClass: MenuForm::class,
            serviceProvider: self::class,additional: [
                'permissionAvailable' => RolePermission::all(),
            ]);

        require_once __DIR__.'/Common.php';
    }
}
