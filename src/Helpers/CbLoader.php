<?php

namespace CrudBooster\Helpers;

use Illuminate\Support\Facades\File;

class CbLoader
{
    /**
     * Load all service providers in the given directory
     *
     * @param string $dir
     * @param string $namespace
     * @param array $except
     */
    public static function loadServiceProviders(string $dir, string $namespace, array $except = []): void
    {
        if(File::exists($dir)) {
            foreach (File::allFiles($dir) as $file) {
                if (str_contains($file->getFilename(), 'ServiceProvider.php') && !in_array($file->getFilename(), $except)) {
                    $relativePath = str_replace([$dir, '/', '.php'], ['', '\\', ''], $file->getRelativePathname());
                    $class = $namespace . $relativePath;
                    if (class_exists($class)) {
                        app()->register($class);
                    }
                }
            }
        }
    }
}
