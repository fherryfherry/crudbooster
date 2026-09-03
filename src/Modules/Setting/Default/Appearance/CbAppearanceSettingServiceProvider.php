<?php

namespace CrudBooster\Modules\Setting\Default\Appearance;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Modules\Setting\CbSettingRegistrar;
use CrudBooster\Modules\Setting\Default\Appearance\Helpers\AppearanceCommon;
use CrudBooster\Modules\Setting\Default\Appearance\Livewire\AppearanceSetting;
use CrudBooster\Modules\Setting\Default\BasicInfo\Livewire\BasicInfoSetting;
use CrudBooster\Modules\Setting\Services\CbSettingService;
use Illuminate\Support\ServiceProvider;

class CbAppearanceSettingServiceProvider extends ServiceProvider
{
    private $key = 'appearance';
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.appearance-setting');
        CbSettingRegistrar::add($this->key, [
            'label' => 'Appearance',
            'icon' => Icon::IMAGE,
            'clazz' => AppearanceSetting::class,
            'order'=> 2
        ]);
    }

    public function register()
    {
        require_once __DIR__ . '/Helpers/Common.php';
        $this->app->singleton(AppearanceCommon::class, function () {
            $settingCache = CbSettingService::get($this->key);
            return new AppearanceCommon($settingCache);
        });
    }

}
