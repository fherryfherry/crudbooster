<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('summernote')->group(function () {
    Route::post('upload/image', '\\CrudBooster\\Components\\Type\\Summernote\\Function\\ImageUploadController@upload')->name('summernote.upload.image');
}); 