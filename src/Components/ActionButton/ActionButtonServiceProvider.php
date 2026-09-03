<?php

namespace CrudBooster\Components\ActionButton;

use Illuminate\Support\ServiceProvider;

class ActionButtonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ActionButtonOptions::class, function ($app) {
            return new ActionButtonOptions();
        });
    }
}
