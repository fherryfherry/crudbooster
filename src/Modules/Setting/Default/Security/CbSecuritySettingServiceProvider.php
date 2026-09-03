<?php

namespace CrudBooster\Modules\Setting\Default\Security;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Modules\Setting\CbSettingRegistrar;
use CrudBooster\Modules\Setting\Default\BasicInfo\Helpers\BasicInfoProperty;
use CrudBooster\Modules\Setting\Default\Security\Helpers\SecurityProperty;
use CrudBooster\Modules\Setting\Default\Security\Livewire\SecuritySetting;
use CrudBooster\Modules\Setting\Services\CbSettingService;
use Illuminate\Support\ServiceProvider;

class CbSecuritySettingServiceProvider extends ServiceProvider
{
    private $key = 'security';
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.security-setting');
        CbSettingRegistrar::add($this->key, [
            'label' => 'Security',
            'icon' => Icon::KEY,
            'clazz' => SecuritySetting::class,
            'order'=> 3
        ]);
    }

    public function register()
    {
        require_once __DIR__ . '/Helpers/Common.php';
        $this->app->singleton(SecurityProperty::class, function () {
            $settingCache = CbSettingService::get($this->key);
            return new SecurityProperty($settingCache);
        });
    }

}
