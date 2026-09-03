<?php

namespace CrudBooster\Components\Type\Summernote;

use Illuminate\Support\ServiceProvider;
use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Themes\CbThemeAssetRegistrar;
use CrudBooster\Components\Type\Summernote\Function\Summernote;

class SummernoteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load views from stub directory
        $this->loadViewsFrom(__DIR__ . '/stub', 'cb-type-summernote');
        
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/router.php');

        // Publish assets to public/vendor/crudbooster/summernote
        $this->publishes([
            __DIR__ . '/assets' => public_path('vendor/crudbooster/summernote'),
        ], 'cb-summernote-assets');
        
        // Register assets in correct order
        // 1. jQuery first (required by Summernote)
        CbThemeAssetRegistrar::addJs('https://code.jquery.com/jquery-3.5.1.min.js');
        
        // 2. Scoped CSS (NOT global Bootstrap CSS)
        $scopedCssPath = public_path('vendor/crudbooster/summernote/summernote-scoped.css');
        if (file_exists($scopedCssPath)) {
            CbThemeAssetRegistrar::addCss(asset('vendor/crudbooster/summernote/summernote-scoped.css'));
        }
        
        // 3. Bootstrap JS only (without global CSS)
        CbThemeAssetRegistrar::addJs('https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js');
        
        // 4. Summernote assets
        CbThemeAssetRegistrar::addCss('https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css');
        CbThemeAssetRegistrar::addJs('https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js');
        
        // Register type component
        CBTypeRegistrar::addWysiwyg('summernote', 'cb-type-summernote::form', 'cb-type-summernote::view', Summernote::class, false, true);
    }
}
