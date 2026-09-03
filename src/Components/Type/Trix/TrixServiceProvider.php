<?php

namespace CrudBooster\Components\Type\Trix;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Trix\Function\Trix;
use CrudBooster\Themes\CbThemeAssetRegistrar;
use Illuminate\Support\ServiceProvider;

class TrixServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-trix');
        $this->loadRoutesFrom(__DIR__.'/router.php');
        // Register assets
        CbThemeAssetRegistrar::addJs('https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js');
        CbThemeAssetRegistrar::addCss('https://unpkg.com/trix@2.0.8/dist/trix.css');
        // Register type component
        CBTypeRegistrar::addWysiwyg('trix', 'cb-type-trix::form', 'cb-type-trix::view', Trix::class, false, true);
    }
}
