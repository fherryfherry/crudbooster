<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Image;

use CrudBooster\Modules\PageBuilder\Models\CbPage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageElement extends Component
{
    use WithFileUploads;

    public $form = [];
    public $pageId;
    public $rowIndex;
    public $colIndex;
    public $config;
    public $id;
    public $imageUpload;

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
            'imageUpload' => 'image'
        ]);

        $image = null;
        if (is_object($this->imageUpload) && !is_string($this->imageUpload)) {
            $image = $this->imageUpload->store('public');
            $this->form['image'] = $image;
        }

        // update to page
        $page = CbPage::where('id',$this->pageId)->first();
        $config = $page->config;
        $config[$this->rowIndex][$this->colIndex]['content']['config'] = $this->form;
        $page->config = $config;
        $page->save();
        $this->dispatch('saved', type: 'success', message: 'Element Image saved');
    }

    public function render()
    {
        return view('cb.element::Image.views.config');
    }

}
