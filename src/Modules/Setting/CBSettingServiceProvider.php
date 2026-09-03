<?php

namespace CrudBooster\Modules\Setting;

use CrudBooster\Helpers\CbLoader;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use CrudBooster\Modules\Setting\Livewire\Setting;
use Illuminate\Support\ServiceProvider;

class CBSettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'cb.setting');
        $this->loadRoutesFrom(__DIR__.'/router.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        ModuleRegistrar::registerModule('setting','Setting Management', Setting::class, Setting::class, self::class, additional: [
            'permissionAvailable' => [
                RolePermission::READ,
                RolePermission::UPDATE,
            ],
        ]);
    }

    public function register()
    {
        require_once __DIR__.'/Helpers/Common.php';

        CbLoader::loadServiceProviders(__DIR__.'/Default', 'CrudBooster\\Modules\\Setting\\Default\\');
        
        // Load user setting service providers from app/Cb/Settings
        $this->loadUserSettingServiceProviders();
    }
    
    /**
     * Load all setting service providers in App/Cb/Settings
     */
    private function loadUserSettingServiceProviders()
    {
        $settingsPath = app_path('Cb/Settings');
        $namespace = 'App\\Cb\\Settings\\';

        CbLoader::loadServiceProviders($settingsPath, $namespace);
    }
}
