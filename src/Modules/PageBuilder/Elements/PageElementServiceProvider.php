<?php

namespace CrudBooster\Modules\PageBuilder\Elements;

use CrudBooster\Helpers\CbLoader;
use Illuminate\Support\ServiceProvider;

class PageElementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__, 'cb.element');
    }

    public function register()
    {
        // Register all service providers in this directory
        CbLoader::loadServiceProviders(__DIR__, 'CrudBooster\\Modules\\PageBuilder\\Elements\\', ['PageElementServiceProvider.php']);
    }

}
