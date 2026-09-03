<?php

use CrudBooster\Helpers\CBPathUtil;
use CrudBooster\Modules\Dashboard\Livewire\Dashboard;
use CrudBooster\Modules\Profile\Livewire\Profile;
use Illuminate\Support\Facades\Route;

Route::prefix(CBPathUtil::getCmsPath(config('cb.dashboard_path') ?? 'dashboard'))->group(function () {
    Route::get("/", Dashboard::class)->middleware(['web','auth'])->name('dashboard');
});