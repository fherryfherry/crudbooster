<?php

use CrudBooster\Components\Type\TinyMce\Function\ImageUploadController;
use Illuminate\Support\Facades\Route;

Route::post('/cb/components/type/tinymce/upload-image', [ImageUploadController::class, 'upload'])->name('tinymce.upload.image');
