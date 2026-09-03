<?php

namespace CrudBooster\Modules\Setting\Default\BasicInfo;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Modules\Setting\CbSettingRegistrar;
use CrudBooster\Modules\Setting\Default\BasicInfo\Helpers\BasicInfoProperty;
use CrudBooster\Modules\Setting\Default\BasicInfo\Livewire\BasicInfoSetting;
use CrudBooster\Modules\Setting\Services\CbSettingService;
use Illuminate\Support\ServiceProvider;

class CbBasicInfoSettingServiceProvider extends ServiceProvider
{
    private $key = 'basic-info';
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.basic-info-setting');
        CbSettingRegistrar::add($this->key, [
            'label' => 'Basic Information',
            'icon' => Icon::BUILDING,
            'clazz' => BasicInfoSetting::class,
            'order' => 1
        ]);
    }

    public function register()
    {
        require_once __DIR__ . '/Helpers/Common.php';
        $this->app->singleton(BasicInfoProperty::class, function () {
            $settingCache = CbSettingService::get($this->key);
            return new BasicInfoProperty($settingCache);
        });
    }

}
