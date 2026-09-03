<?php

namespace CrudBooster\Components\SelectIcon;


use CrudBooster\Components\Icon\Icon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class SelectIcon extends Component
{
    public $keyword;

    #[Modelable]
    public $selected;

    #[Computed]
    public function getIcons()
    {
        $icons = Icon::getIcons();
        if($this->keyword) {
            $icons = array_filter($icons, function($icon) {
                return str_contains(strtolower($icon['label']), strtolower($this->keyword));
            });
        }
        return $icons;
    }
    public function unselectIcon()
    {
        $this->selected = null;
    }

    public function selectIcon($iconKey)
    {
        $this->selected = $iconKey;
    }
    public function render()
    {
        return view('cb.components::SelectIcon.views.template');
    }
}
