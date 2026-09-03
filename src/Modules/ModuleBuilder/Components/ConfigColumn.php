<?php

namespace CrudBooster\Modules\ModuleBuilder\Components;

use Illuminate\View\Component;

class ConfigColumn extends Component
{
    public $key;
    public $column;

    public function __construct($key, $column)
    {
        $this->key = $key;
        $this->column = $column;
    }

    public function render()
    {
        return view('cb.module-builder::components.configcolumn');
    }
}
