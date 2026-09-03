<?php

namespace CrudBooster\Modules;

use CrudBooster\Helpers\CbLoader;

trait WithUserModuleLoader
{
    /**
     * Register all module service providers in App/Cb/Modules
     */
    protected function registerUserModuleServiceProviders()
    {
        $modulePath = app_path('Cb/Modules');
        $namespace = 'App\\Cb\\Modules\\';

        CbLoader::loadServiceProviders($modulePath, $namespace);
    }
}
