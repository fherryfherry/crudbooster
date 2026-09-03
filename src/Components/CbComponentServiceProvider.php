<?php

namespace CrudBooster\Components;

use CrudBooster\Helpers\CbLoader;
use Illuminate\Support\ServiceProvider;

class CbComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__, 'cb.components');
    }

    public function register()
    {
        require_once __DIR__ . '/ConfirmMessage/Common.php';

        CbLoader::loadServiceProviders(__DIR__, 'CrudBooster\\Components\\', ['CbComponentServiceProvider.php']);
    }
}
