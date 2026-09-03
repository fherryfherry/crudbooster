<?php

namespace CrudBooster\Modules\Setting\Default\BasicInfo\Livewire;

use CrudBooster\Modules\Setting\CbBaseSetting;

class BasicInfoSetting extends CbBaseSetting
{
    public $key = 'basic-info';
    public function render()
    {
        return view('cb.basic-info-setting::basic_info');
    }
}
