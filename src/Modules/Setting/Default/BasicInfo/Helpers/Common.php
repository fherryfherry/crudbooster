<?php

use CrudBooster\Modules\Setting\Default\BasicInfo\Helpers\BasicInfoProperty;

if(!function_exists('basicInfoSetting')) {

    /**
     * @return BasicInfoProperty|\Illuminate\Foundation\Application|mixed
     */
    function basicInfoSetting(): mixed
    {
        return app(BasicInfoProperty::class);
    }
}
