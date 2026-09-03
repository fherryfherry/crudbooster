<?php

namespace CrudBooster\Themes\Components;

use Illuminate\View\Component;

class Header extends Component
{
    public function __construct(public string $pageTitle)
    {

    }
    public function render()
    {
        return view('cb.themes::components.header');
    }
}
