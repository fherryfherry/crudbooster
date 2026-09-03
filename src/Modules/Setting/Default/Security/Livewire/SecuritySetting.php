<?php

namespace CrudBooster\Modules\Setting\Default\Security\Livewire;

use CrudBooster\Modules\Setting\CbBaseSetting;

class SecuritySetting extends CbBaseSetting
{
    public $key = 'security';
    public function render()
    {
        return view('cb.security-setting::security');
    }
}
