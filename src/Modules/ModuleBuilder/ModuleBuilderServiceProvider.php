<?php

namespace CrudBooster\Modules\ModuleBuilder;

use CrudBooster\Modules\ModuleBuilder\Builder\ModuleBuilder;
use CrudBooster\Modules\ModuleBuilder\Components\ConfigColumn;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ModuleBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'cb.module-builder');
        $this->loadRoutesFrom(__DIR__.'/router.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        ModuleRegistrar::registerModule(
            key: 'module-builder',
            name: 'Module Builder',
            browseModuleClass: ModuleBuilder::class,
            formModuleClass: ModuleBuilder::class,
            serviceProvider: self::class, additional: [
            'permissionAvailable' => RolePermission::all(),
        ]);

        // Register blade components
        Blade::component('config-column', ConfigColumn::class);
    }

    public function register()
    {
        require_once __DIR__.'/Helpers/Common.php';
    }
}
