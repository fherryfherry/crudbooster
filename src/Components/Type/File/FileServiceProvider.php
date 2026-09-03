<?php

namespace CrudBooster\Components\Type\File;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\File\Function\File;
use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-file');
        CBTypeRegistrar::addUpload('file', 'cb-type-file::form', 'cb-type-file::view', File::class, false, true);
    }
}
