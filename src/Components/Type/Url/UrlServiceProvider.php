<?php

namespace CrudBooster\Components\Type\Url;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Url\Function\Url;
use Illuminate\Support\ServiceProvider;

class UrlServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-url');
        CBTypeRegistrar::add('url', 'cb-type-url::form', 'cb-type-url::view', Url::class, false, true);
    }
}
