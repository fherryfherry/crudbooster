<?php

namespace CrudBooster\Modules\Auth;

use CrudBooster\Commands\CbInstallRegistrar;
use CrudBooster\Helpers\CbUploader;
use CrudBooster\Modules\Auth\Livewire\Forgot;
use CrudBooster\Modules\Auth\Livewire\Login;
use CrudBooster\Modules\Auth\Livewire\Reset;
use CrudBooster\Modules\Setting\Services\CbSettingService;
use Illuminate\Http\File;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class CbAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.auth');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Lang' => resource_path('lang'),
            ], 'cb-lang');

            // Add install callback
            CbInstallRegistrar::add('auth', function () {
                // insert default splash image
                $splash = CbUploader::uploadFromFile(new File(public_path('vendor/crudbooster/themes/assets/images/login-splash.jpeg')));
                $logo = CbUploader::uploadFromFile(new File(public_path('vendor/crudbooster/themes/assets/images/logo-cb-color.png')));
                $sidebarLogo = CbUploader::uploadFromFile(new File(public_path('vendor/crudbooster/themes/assets/images/logo-cb-white.png')));
                CbSettingService::createOrUpdate('appearance', [
                    'login_logo' => $logo,
                    'login_splash' => $splash,
                    'sidebar_logo' => $sidebarLogo
                ]);
            });
        }
        $this->loadRoutesFrom(__DIR__ . '/router.php');
        // Register livewire components
        Livewire::component('login', Login::class);
        Livewire::component('forgot', Forgot::class);
        Livewire::component('reset', Reset::class);
    }
}
