<?php

use CrudBooster\Modules\Setting\Services\CbSettingService;

if (!function_exists('setting')) {
    function setting($name, $key, $default = null)
    {
        $setting = CbSettingService::get($name);
        return $setting && isset($setting[$key]) ? $setting[$key] : $default;
    }
}

if(!function_exists('cbSettingRegistrarList')) {
    function cbSettingRegistrarList(): array
    {
        return \CrudBooster\Modules\Setting\CbSettingRegistrar::__getAll();
    }
}
