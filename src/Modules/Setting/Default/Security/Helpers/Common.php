<?php

use CrudBooster\Modules\Setting\Default\Security\Helpers\SecurityProperty;

if(!function_exists('securitySetting')) {
    /**
     * @return SecurityProperty|\Illuminate\Foundation\Application|mixed
     */
    function securitySetting(): mixed
    {
        return app(SecurityProperty::class);
    }
}
