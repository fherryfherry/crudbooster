<?php

namespace CrudBooster\Components\Type\Password;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\Password\Function\Password;
use Illuminate\Support\ServiceProvider;

class PasswordServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/stub', 'cb-type-password');
        CBTypeRegistrar::add('password', 'cb-type-password::form', 'cb-type-password::view', Password::class, false, true);
    }
}
