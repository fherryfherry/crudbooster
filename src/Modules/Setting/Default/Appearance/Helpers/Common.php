<?php

use CrudBooster\Modules\Setting\Default\Appearance\Helpers\AppearanceCommon;

if(!function_exists('appearanceSetting')) {
    /**
     * @return CrudBooster\Modules\Setting\Default\Appearance\Helpers\AppearanceCommon|\Illuminate\Foundation\Application|mixed
     */
    function appearanceSetting(): mixed
    {
        return app(AppearanceCommon::class);
    }
}
