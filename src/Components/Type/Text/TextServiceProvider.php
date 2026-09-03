<?php

namespace CrudBooster\Components\Type\Text;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Text\Function\Text;
use Illuminate\Support\ServiceProvider;

class TextServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-text');
        CBTypeRegistrar::add('text', 'cb-type-text::form', 'cb-type-text::view', Text::class, true, true);
    }
}
