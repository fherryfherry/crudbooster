<?php

namespace CrudBooster\Components\Type\Email;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Email\function\Email;
use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-email');
        CBTypeRegistrar::add('email', 'cb-type-email::form', 'cb-type-email::view', Email::class, true, true);
    }
}
