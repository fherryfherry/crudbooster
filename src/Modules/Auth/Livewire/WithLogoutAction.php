<?php

namespace CrudBooster\Modules\Auth\Livewire;

use CrudBooster\Modules\Auth\Events\LogoutSuccess;
use Illuminate\Support\Facades\Event;

trait WithLogoutAction
{
    public function logout(): void
    {
        if (auth()->check()) {
            Event::dispatch(new LogoutSuccess(auth()->user()));
        }
        auth()->logout();
        $this->redirectRoute('login',  navigate: true);
    }

}
