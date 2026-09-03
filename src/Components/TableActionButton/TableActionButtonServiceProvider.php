<?php

namespace CrudBooster\Components\TableActionButton;

use Illuminate\Support\ServiceProvider;

class TableActionButtonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TableActionButtonOptions::class, function () {
            return new TableActionButtonOptions();
        });
    }
}
