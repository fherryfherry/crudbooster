<?php

namespace CrudBooster\Components\TopBar\Profile;

use CrudBooster\Components\TopBar\Profile\Livewire\TopBarProfile;
use CrudBooster\Components\TopBar\TopBarRegistrar;
use Illuminate\Support\ServiceProvider;

class TopBarProfileServiceProvider extends ServiceProvider
{
    // boot
    public function boot()
    {
        TopBarRegistrar::add([
            'name'=> 'top-bar-profile',
            'clazz'=> TopBarProfile::class,
            'order'=> 99 // it means last position
        ]);
    }
}
