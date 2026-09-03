<?php

namespace CrudBooster\Components;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Livewire\Livewire;

class LivewireComponentRegistrar
{
    public static function autoRegister(string $path): void
    {
        $fileSystem = new Filesystem();
        $components = [];
        $files = $fileSystem->allFiles($path);
        foreach ($files as $file) {
            $filePath = $file->getPathname();
            if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) == 'php') {
                require_once $filePath;
                $className = self::getClassNameFromFile($filePath);
                if ($className) {
                    $name = Str::snake(class_basename($className));
                    $components[$name] = $className;
                }
            }
        }

        foreach ($components as $name => $className) {
            Livewire::component($name, $className);
        }
    }

    private static function getClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if (preg_match('/namespace\s+(.+?);/', $content, $namespaceMatches) &&
            preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            $namespace = $namespaceMatches[1];
            $class = $classMatches[1];
            return $namespace . '\\' . $class;
        }
        return null;
    }
}
