<?php

namespace CrudBooster\Components\Type\Image;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Image\Function\Image;
use Illuminate\Support\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-image');
        CBTypeRegistrar::addUpload('image', 'cb-type-image::form', 'cb-type-image::view', Image::class, false, true);
    }
}
