<?php

namespace CrudBooster\Modules\Profile;

use CrudBooster\Modules\Profile\Livewire\Profile;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class CBProfileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.profile');
        $this->loadRoutesFrom(__DIR__ . '/router.php');
        // Register live wire components
        Livewire::component('profile', Profile::class);
    }
}
