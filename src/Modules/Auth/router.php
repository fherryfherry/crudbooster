<?php

use CrudBooster\Helpers\CBPathUtil;
use CrudBooster\Modules\Auth\Events\LogoutSuccess;
use CrudBooster\Modules\Auth\Livewire\Forgot;
use CrudBooster\Modules\Auth\Livewire\Login;
use CrudBooster\Modules\Auth\Livewire\Reset;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;

Route::prefix(CBPathUtil::getCmsPath('auth'))->group(function () {
    Route::get("login", Login::class)->middleware(['web', 'cb.audit'])->name('login');
    Route::get("logout", function () {
        if (auth()->check()) {
            Event::dispatch(new LogoutSuccess(auth()->user()));
        }
        auth()->logout();
        return redirect()->route('login');
    })->middleware(['web', 'cb.audit'])->name('logout');
    Route::get("forgot", Forgot::class)->middleware(['web', 'cb.audit'])->name('forgot');
    Route::get("password-reset/{token}", Reset::class)->middleware(['web', 'cb.audit'])->name('reset');
});
