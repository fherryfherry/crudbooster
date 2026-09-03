<?php

namespace CrudBooster\Modules\Setting;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Helpers\CBRoute;
use Livewire\Livewire;

class CbSettingRegistrar
{
    private static $data = [];

    // add
    public static function add($key, array $option)
    {
        $add = [];
        $add['name'] = $key;
        $add['order'] = $option['order'] ?? count(self::$data);
        $add['icon'] = $option['icon'] ?? Icon::COG;
        $add['label'] = $option['label'];
        $add['clazz'] = $option['clazz'];
        self::$data[$key] = $add;
        Livewire::component('cb.setting::' . $key, $option['clazz']);
        CBRoute::createRouteOne('setting/'.$key, $option['clazz']);
    }

    // get
    public static function __getAll()
    {
        $data = self::$data ? array_values(self::$data) : [];
        usort($data, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        return $data;
    }

}
