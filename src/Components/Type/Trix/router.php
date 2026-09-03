<?php

use CrudBooster\Components\Type\Trix\Function\ImageUploadController;    

Route::post('/cb/components/type/trix/upload-image', [ImageUploadController::class, 'upload'])->name('trix.upload.image');