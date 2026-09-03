<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Heading;

use CrudBooster\Modules\PageBuilder\Models\CbPage;
use Livewire\Component;

class HeadingElement extends Component
{
    public $form = [];
    public $pageId;
    public $rowIndex;
    public $colIndex;
    public $config;
    public $id;

    public function mount($id = null, $rowIndex = null, $colIndex = null)
    {
        $this->id = $rowIndex.$colIndex;
        $this->pageId = $id;
        $this->rowIndex = $rowIndex;
        $this->colIndex = $colIndex;
        if($this->pageId) {
            $this->config = CbPage::where('id',$this->pageId)->first()?->config;
            $this->form = ($this->config) ? $this->config[$rowIndex][$colIndex]['content']['config']??[] : [];
        }
    }

    public function save()
    {
        $this->validate([
            'form.heading' => 'required'
        ]);

        // update to page
        $page = CbPage::where('id',$this->pageId)->first();
        $config = $page->config;
        $config[$this->rowIndex][$this->colIndex]['content']['config'] = $this->form;
        $page->config = $config;
        $page->save();
        $this->dispatch('saved', type: 'success', message: 'Element heading saved');
    }

    public function render()
    {
        return view('cb.element::Heading.views.config');
    }

}
