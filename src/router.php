<?php

use Illuminate\Support\Facades\Route;

Route::get(getCmsPath('/'), function () {
    return redirect(getCmsPath(config('cb.dashboard_path')));
});

Route::get(getCmsPath('export/pdf'), [\CrudBooster\Http\Controllers\ExportPdfController::class, 'export'])
    ->middleware(['web', 'auth'])
    ->name('crudbooster.export.pdf');

Route::get(getCmsPath('export/xls'), [\CrudBooster\Http\Controllers\ExportExcelController::class, 'export'])
    ->middleware(['web', 'auth'])
    ->name('crudbooster.export.xls');

Route::get(getCmsPath('export/csv'), [\CrudBooster\Http\Controllers\ExportCsvController::class, 'export'])
    ->middleware(['web', 'auth'])
    ->name('crudbooster.export.csv');