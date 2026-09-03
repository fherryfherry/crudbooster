<?php

namespace CrudBooster\Modules\QueryBuilder;

use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\QueryBuilder\Livewire\QueryBuilder;
use CrudBooster\Modules\QueryBuilder\Livewire\QueryBuilderForm;
use CrudBooster\Modules\Role\Enum\RolePermission;
use Illuminate\Support\ServiceProvider;

class CbQueryBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.query-builder');
        $this->loadRoutesFrom(__DIR__ . '/router.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        // Override the strict setting
        config(['database.connections.mysql.strict' => false]);

        ModuleRegistrar::registerModule(
            key: 'query-builder',
            name: 'Query Builder',
            browseModuleClass: QueryBuilder::class,
            formModuleClass: QueryBuilderForm::class,
            serviceProvider: self::class,
            additional: [
                'permissionAvailable' => RolePermission::all(),
            ]
        );
    }
}
