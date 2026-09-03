<?php

namespace CrudBooster\Modules\PageBuilder;

use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\PageBuilder\Livewire\PageBuilder;
use CrudBooster\Modules\PageBuilder\Livewire\PageBuilderStudio;
use CrudBooster\Modules\Role\Enum\RolePermission;
use CrudBooster\Themes\CbThemeAssetRegistrar;
use Illuminate\Support\ServiceProvider;

class PageBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(Elements\PageElementServiceProvider::class);
    }
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'cb.page-builder');
        $this->loadRoutesFrom(__DIR__.'/router.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        // Register assets
        CbThemeAssetRegistrar::addJs('vendor/crudbooster/themes/assets/js/chart.min.js');

        ModuleRegistrar::registerModule(
            key: 'page-builder',
            name: 'Page Builder',
            browseModuleClass: PageBuilder::class,
            formModuleClass: PageBuilderStudio::class,
            serviceProvider: self::class,
            additional: [
            'permissionAvailable' => RolePermission::all(),
        ]);
    }
}
