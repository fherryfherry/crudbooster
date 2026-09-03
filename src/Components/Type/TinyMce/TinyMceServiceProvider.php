<?php

namespace CrudBooster\Components\Type\TinyMce;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\TinyMce\Function\TinyMce;
use CrudBooster\Themes\CbThemeAssetRegistrar;
use Illuminate\Support\ServiceProvider;

class TinyMceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-tinymce');
        $this->loadRoutesFrom(__DIR__.'/router.php');
        // Register asset
        CbThemeAssetRegistrar::addJs('vendor/crudbooster/themes/assets/js/tinymce/tinymce.min.js');
        // Register type component
        CBTypeRegistrar::addWysiwyg('tinymce', 'cb-type-tinymce::form', 'cb-type-tinymce::view', TinyMce::class, false, true);
    }
}
