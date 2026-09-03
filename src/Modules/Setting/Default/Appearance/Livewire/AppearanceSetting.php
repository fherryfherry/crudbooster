<?php

namespace CrudBooster\Modules\Setting\Default\Appearance\Livewire;

use CrudBooster\Modules\Setting\CbBaseSetting;
use Livewire\WithFileUploads;

class AppearanceSetting extends CbBaseSetting
{
    use WithFileUploads;
    
    public $key = 'appearance';
    public function render()
    {
        return view('cb.appearance-setting::appearance');
    }
}
