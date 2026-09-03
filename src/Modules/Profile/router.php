<?php

use CrudBooster\Helpers\CBPathUtil;
use CrudBooster\Modules\Profile\Livewire\Profile;
use Illuminate\Support\Facades\Route;

Route::prefix(CBPathUtil::getCmsPath('profile'))->group(function () {
    Route::get("/", config('cb.profile_component') ?? Profile::class)->middleware(['web','auth'])->name('profile');
});