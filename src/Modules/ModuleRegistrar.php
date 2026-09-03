<?php

namespace CrudBooster\Modules;

use CrudBooster\Components\LivewireComponentRegistrar;
use Livewire\Livewire;

class ModuleRegistrar
{
    private static $modules = [];

    /**
     * This method is used to register a module
     * @param string $key
     * @param string $name
     * @param string $browseModuleClass
     * @param string $formModuleClass
     * @param $serviceProvider
     * @param array $additional
     * @return void
     */
    public static function registerModule(string $key, string $name, string $browseModuleClass, string $formModuleClass, $serviceProvider, array $additional = [])
    {
        if(isset(self::$modules[$key])) {
            return;
        }
        self::$modules[$key] = [
            'key'=> $key,
            'name'=> $name,
            'mainPath' => $key,
            'browseModuleClass' => $browseModuleClass,
            'formModuleClass' => $formModuleClass,
            'serviceProvider' => $serviceProvider,
            'additional' => $additional
        ];

        // Register browse module
        Livewire::component($key . '-browse', $browseModuleClass);
        // Register form module
        Livewire::component($key . '-form', $formModuleClass);

        // Register the component
        $directory = dirname((new \ReflectionClass($serviceProvider))->getFileName());
        $livewireDirectory = $directory . '/Livewire';
        if(is_dir($livewireDirectory)) {
            LivewireComponentRegistrar::autoRegister($livewireDirectory);
        }
    }

    public static function getModules($key = null)
    {
        if($key) {
            return self::$modules[$key] ?? null;
        }
        return self::$modules;
    }
}
