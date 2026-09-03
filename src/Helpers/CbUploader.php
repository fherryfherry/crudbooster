<?php

namespace CrudBooster\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CbUploader
{
    protected static $prefix = 'public/{date}';
    public static function uploadFromFile(File $file, ?string $disk = null)
    {
        $disk = $disk ?? (config('cb.storage_disk') ?? config('filesystems.default'));
        $path = str_replace('{date}', date('Y-m-d'), self::$prefix);
        $newFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME) . '.' . $file->getExtension();
        if(Storage::disk($disk)->exists($path.'/'.$newFilename)) {
            $newFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME) . '-' . time() . '.' . $file->getExtension();
        }
        return Storage::disk($disk)->putFileAs($path, $file, $newFilename);
    }

    public static function uploadFromLivewire(TemporaryUploadedFile $file, ?string $disk = null): false|string
    {
        $disk = $disk ?? (config('cb.storage_disk') ?? config('filesystems.default'));
        $path = str_replace('{date}', date('Y-m-d'), self::$prefix);
        $newFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->getClientOriginalExtension();
        if(Storage::disk($disk)->exists($path.'/'.$newFilename)) {
            $newFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . time() . '.' . $file->getClientOriginalExtension();
        }
        return $file->storeAs($path, $newFilename, ['disk' => $disk]);
    }

    // from UploadedFile
    public static function uploadFromUploadedFile(\Illuminate\Http\UploadedFile $file, ?string $disk = null): false|string
    {
        $disk = $disk ?? (config('cb.storage_disk') ?? config('filesystems.default'));
        $path = str_replace('{date}', date('Y-m-d'), self::$prefix);
        $newFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->getClientOriginalExtension();
        if(Storage::disk($disk)->exists($path.'/'.$newFilename)) {
            $newFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . time() . '.' . $file->getClientOriginalExtension();
        }
        return $file->storeAs($path, $newFilename, ['disk' => $disk]);
    }

}
