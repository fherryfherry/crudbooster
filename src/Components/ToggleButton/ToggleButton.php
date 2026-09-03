<?php

namespace CrudBooster\Components\ToggleButton;

use Illuminate\View\Component;

class ToggleButton extends Component
{
    public $id;
    public $model;
    public $type;
    public $value;

    public function __construct($id, $model, $type = 'checkbox', $value = null)
    {
        $this->id = $id;
        $this->model = $model;
        $this->type = $type;
        $this->value = $value;
    }

    public function render()
    {
        return view('cb.components::ToggleButton.views.togglebutton');
    }
}
