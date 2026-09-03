<?php

namespace CrudBooster\Livewire;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class CbLiveWireServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__, 'cb.livewire');
    }

    public function register()
    {
        $this->includeAllCommon();
    }

    private function includeAllCommon()
    {
        // include all Common.php
        $fileSystem = new Filesystem();
        $files = $fileSystem->allFiles(__DIR__);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (str_contains($filename, 'Common.php')) {
                require_once $file->getPathname();
            }
        }
    }
}