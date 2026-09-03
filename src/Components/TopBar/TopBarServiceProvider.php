<?php

namespace CrudBooster\Components\TopBar;

use CrudBooster\Helpers\CbLoader;
use Illuminate\Support\ServiceProvider;

class TopBarServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__, 'cb.topBar');
    }
    public function register()
    {
        require_once __DIR__ . '/Common.php';

        CbLoader::loadServiceProviders(__DIR__, 'CrudBooster\\Components\\TopBar\\', ['TopBarServiceProvider.php']);
    }

}
