<?php

namespace CrudBooster\Modules\PageBuilder\Livewire;

use CrudBooster\Modules\PageBuilder\Models\CbPage;
use Livewire\Component;

class PageBuilderViewer extends Component
{
    public $data = [];
    public $name;
    public $id;
    public $grid = [];

    public function mount($id)
    {
        $this->id = $id;
        if($id) {
            $this->data = CbPage::where('id',$id)->first()?->toArray();
            $this->grid = $this->data['config'] ?? [];
            $this->name = $this->data['name'] ?? '';
        }
    }

    public function render()
    {
        return view("cb.page-builder::page_viewer")->layout("cb.themes::layout-app");
    }
}
