<?php

namespace CrudBooster\Components\Type\TextArea;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\TextArea\Function\TextArea;
use Illuminate\Support\ServiceProvider;

class TextAreaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-textarea');
        CBTypeRegistrar::add('textarea', 'cb-type-textarea::form', 'cb-type-textarea::view', TextArea::class, true, true);
    }
}
