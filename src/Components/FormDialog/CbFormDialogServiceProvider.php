<?php

namespace CrudBooster\Components\FormDialog;

use CrudBooster\Components\FormDialog\Livewire\FormDialog;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class CbFormDialogServiceProvider extends ServiceProvider
{
    public static function boot()
    {
        Livewire::component('cb-form-dialog', FormDialog::class);
    }

}
