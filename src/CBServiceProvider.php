<?php

namespace CrudBooster;

use CrudBooster\Commands\GenerateCrudCommand;
use CrudBooster\Commands\GenerateSettingCommand;
use CrudBooster\Commands\InstallCommand;
use CrudBooster\Commands\GenerateCustomCssCommand;
use CrudBooster\Commands\PruneAuditLogCommand;
use CrudBooster\Components\ActionButton\ActionButtonOptions;
use CrudBooster\Components\CbComponentServiceProvider;
use CrudBooster\Helpers\SchemaUtil;
use CrudBooster\Livewire\CbLiveWireServiceProvider;
use CrudBooster\Modules\CbModuleServiceProvider;
use CrudBooster\Themes\CbThemeServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class CBServiceProvider extends ServiceProvider
{
    protected $commands = [
        InstallCommand::class,
        GenerateCrudCommand::class,
        GenerateSettingCommand::class,
        GenerateCustomCssCommand::class,
        PruneAuditLogCommand::class,
    ];

    public function boot()
    {
        AboutCommand::add('CRUDBooster', fn() => ['Version' => 'v7.x']);
        // Load default route
        $this->loadRoutesFrom(__DIR__ . '/router.php');

        // Register middleware
        $this->app['router']->aliasMiddleware('cb.audit', \CrudBooster\Middleware\CbAuditLogRequestMiddleware::class);

        if ($this->app->runningInConsole()) {
            // Register commands
            $this->commands($this->commands);
            // Publish config
            $this->publishes([
                __DIR__ . '/Configs/cb.php' => config_path('cb.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Configs/cb.php', 'cb');

        $this->app->register(CbComponentServiceProvider::class);
        $this->app->register(CbLiveWireServiceProvider::class);
        $this->app->register(CbThemeServiceProvider::class);
        $this->app->register(CbModuleServiceProvider::class);

        // Register singleton
        $this->app->singleton(InstallCommand::class);
        $this->app->singleton(GenerateCrudCommand::class);
        $this->app->singleton(GenerateCustomCssCommand::class);
        $this->app->singleton(PruneAuditLogCommand::class);

        // Register facade
        $this->app->singleton('SchemaUtil', function () {
            return new SchemaUtil();
        });

        // Register helpers common
        require_once __DIR__ . '/Helpers/Common.php';
    }
}
