<?php

namespace CrudBooster\Modules\PageBuilder\Elements\GoogleMap;

use CrudBooster\Modules\PageBuilder\Models\CbPage;
use Livewire\Component;

class GoogleMapElement extends Component
{
    public $form = [];
    public $pageId;
    public $rowIndex;
    public $colIndex;
    public $config;
    public $mapUrl;
    public $id;

    public function mount($id = null, $rowIndex = null, $colIndex = null)
    {
        $this->id = $rowIndex . $colIndex;
        $this->pageId = $id;
        $this->rowIndex = $rowIndex;
        $this->colIndex = $colIndex;
        if ($this->pageId) {
            $this->config = CbPage::where('id', $this->pageId)->first()?->config;
            $this->form = ($this->config) ? $this->config[$rowIndex][$colIndex]['content']['config'] ?? [] : [];
            $this->form['mapUrl'] = $this->form['mapUrl'] ?? $this->generateMapUrl();
        }
        $this->form['zoom'] = $this->form['zoom'] ?? 15;
    }

    public function updated()
    {
        if ($this->form['place'] ?? false) {
            $this->form['mapUrl'] = $this->generateMapUrl();
        }
    }
    public function generateMapUrl()
    {
        // check if apiKey, place, zoom is setted
        if (!isset($this->form['apiKey']) || !isset($this->form['place']) || !isset($this->form['zoom'])) {
            return;
        }
        return "https://www.google.com/maps/embed/v1/search?key=" . $this->form['apiKey'] . "&q=" . $this->form['place'] . "&zoom=" . $this->form['zoom'];
    }

    public function save()
    {
        $this->validate([
            'form.title' => 'required',
            'form.place' => 'required',
            'form.zoom' => 'required',
        ]);

        // update to page
        $page = CbPage::where('id', $this->pageId)->first();
        $config = $page->config;
        $config[$this->rowIndex][$this->colIndex]['content']['config'] = $this->form;
        $page->config = $config;
        $page->save();
        $this->dispatch('saved', type: 'success', message: 'Element box counter saved');
    }

    public function render()
    {
        return view('cb.element::GoogleMap.views.config');
    }
}
