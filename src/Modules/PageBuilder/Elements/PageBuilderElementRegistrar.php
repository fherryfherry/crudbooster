<?php

namespace CrudBooster\Modules\PageBuilder\Elements;

use Livewire\Livewire;

class PageBuilderElementRegistrar
{
    private static $elements = [];

    /**
     * Add a new element to the page builder
     * @param $key
     * @param $name
     * @param $class
     * @return void
     */
    public static function add($key, $name, $class)
    {
        self::$elements[$key] = [
            'key' => $key,
            'name' => $name,
            'class' => $class
        ];
        // Register Livewire component
        Livewire::component($key, $class);
    }

    public static function __getElements()
    {
        return self::$elements;
    }
}
