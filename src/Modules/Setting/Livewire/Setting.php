<?php

namespace CrudBooster\Modules\Setting\Livewire;

use Livewire\Component;

class Setting extends Component
{
    public $currentSetting;
    public function mount()
    {

    }
    public function openSetting($name)
    {
        $this->currentSetting = $name;
    }
    public function render()
    {
        return view('cb.setting::setting')->layout('cb.themes::layout-app');
    }
}
