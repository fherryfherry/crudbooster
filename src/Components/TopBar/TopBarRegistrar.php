<?php

namespace CrudBooster\Components\TopBar;

use Livewire\Livewire;

class TopBarRegistrar
{
    private static $data = [];

    public static function add($data)
    {
        self::$data[$data['name']] = $data;
        // throw invalid argument
        if (!isset($data['name']) || !isset($data['clazz'])) {
            throw new \InvalidArgumentException('Invalid argument');
        }
        Livewire::component($data['name'], $data['clazz']);
    }

    public static function __getData()
    {
        return collect(self::$data)->values()->sort(function ($a, $b) {
            return $a['order'] <=> $b['order'];
        })->toArray();
    }
}
