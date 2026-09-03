<?php

namespace CrudBooster\Modules\Dashboard;

use CrudBooster\Modules\Dashboard\Livewire\Dashboard;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class CbDashboardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'cb.modules.dashboard');
        $this->loadRoutesFrom(__DIR__ . '/router.php');
        // Register live wire components
        Livewire::component('dashboard', Dashboard::class);
        ModuleRegistrar::registerModule('dashboard', 'Dashboard', Dashboard::class, Dashboard::class, self::class, additional: [
            'permissionAvailable' => [
                RolePermission::READ,
            ],
        ]);
    }
}
