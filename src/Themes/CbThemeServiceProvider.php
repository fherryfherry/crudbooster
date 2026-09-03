<?php

namespace CrudBooster\Themes;

use CrudBooster\Themes\Components\Header;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CbThemeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(config('cb.theme_path') ?? __DIR__ . '/views', 'cb.themes');
        $this->registerCbAssetDirective();
        $this->cbFormDirective();
        $this->cbFormTitleDirective();
        $this->cbModalImportDirective();
        $this->cbModalBulkConfirmationDirective();
        $this->cbDetailContent();

        if($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/assets' => public_path('vendor/crudbooster/themes/assets'),
            ], 'cb-themes');
        }

        // Register theme components
        Blade::component('header', Header::class);
    }

    private function registerCbAssetDirective()
    {
        Blade::directive('cbAssets', function ($expression) {
            $cssList = CbThemeAssetRegistrar::__getDataCss();
            $jsList = CbThemeAssetRegistrar::__getDataJs();
            $html = '';
            foreach ($cssList as $css) {
                $url = str_starts_with($css, 'http') || str_starts_with($css, '//') ? $css : asset($css);
                $html .= '<link rel="stylesheet" href="'.$url.'">';
            }

            foreach ($jsList as $js) {
                $url = str_starts_with($js, 'http') || str_starts_with($js, '//') ? $js : asset($js);
                $html .= '<script src="' . $url . '"></script>';
            }
            return $html;
        });
    }

    private function cbFormDirective()
    {
        Blade::directive('cbForm', function ($expression) {
            return "<?php echo view('cb.themes::components.form-content', $expression); ?>";
        });
    }

    private function cbFormTitleDirective()
    {
        Blade::directive('cbFormTitle', function ($expression) {
            return "<?php echo view('cb.themes::components.form-title', $expression); ?>";
        });
    }

    private function cbModalImportDirective()
    {
        Blade::directive('cbModalImport', function ($expression) {
            return "<?php echo view('cb.themes::components.modal-import', $expression); ?>";
        });
    }
    private function cbModalBulkConfirmationDirective()
    {
        Blade::directive('cbModalBulkConfirmation', function ($expression) {
            return "<?php echo view('cb.themes::components.modal-bulk-confirmation', $expression); ?>";
        });
    }

    private function cbDetailContent()
    {
        Blade::directive('cbDetailContent', function ($expression) {
            return "<?php echo view('cb.themes::components.detail-content', $expression); ?>";
        });
    }
}
