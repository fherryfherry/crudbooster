<?php

namespace CrudBooster\Modules;

use CrudBooster\Modules\ApiBuilder\CbApiBuilderServiceProvider;
use CrudBooster\Modules\AuditLog\CbAuditLogServiceProvider;
use CrudBooster\Modules\Auth\CbAuthServiceProvider;
use CrudBooster\Modules\Dashboard\CbDashboardServiceProvider;
use CrudBooster\Modules\Menu\CbMenuServiceProvider;
use CrudBooster\Modules\ModuleBuilder\ModuleBuilderServiceProvider;
use CrudBooster\Modules\PageBuilder\PageBuilderServiceProvider;
use CrudBooster\Modules\Profile\CBProfileServiceProvider;
use CrudBooster\Modules\QueryBuilder\CbQueryBuilderServiceProvider;
use CrudBooster\Modules\Role\CBRoleServiceProvider;
use CrudBooster\Modules\Setting\CBSettingServiceProvider;
use Illuminate\Support\ServiceProvider;

class CbModuleServiceProvider extends ServiceProvider
{
    use WithUserModuleLoader;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__, 'cb.modules');
    }

    public function register()
    {
        $this->app->register(CbApiBuilderServiceProvider::class);
        $this->app->register(CbAuditLogServiceProvider::class);
        $this->app->register(CbAuthServiceProvider::class);
        $this->app->register(CBProfileServiceProvider::class);
        $this->app->register(CBRoleServiceProvider::class);
        $this->app->register(CbMenuServiceProvider::class);
        $this->app->register(CBSettingServiceProvider::class);
        $this->app->register(CbDashboardServiceProvider::class);
        $this->app->register(ModuleBuilderServiceProvider::class);
        $this->app->register(PageBuilderServiceProvider::class);
        $this->app->register(CbQueryBuilderServiceProvider::class);

        // Register user module service providers
        $this->registerUserModuleServiceProviders();
    }


}
