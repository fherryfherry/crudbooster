<?php

namespace CrudBooster\Modules\PageBuilder\Elements\BoxCounter;

use CrudBooster\Modules\PageBuilder\Models\CbPage;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BoxCounterElement extends Component
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

    #[Computed]
    public function queryBuilderList()
    {
        return CbQueryBuilderService::getDataWithOutputTypeCount();
    }

    public function save()
    {
        $this->validate([
            'form.title' => 'required',
            'form.icon' => 'required',
            'form.queryBuilder' => 'required',
        ]);

        // update to page
        $page = CbPage::where('id',$this->pageId)->first();
        $config = $page->config;
        $config[$this->rowIndex][$this->colIndex]['content']['config'] = $this->form;
        $page->config = $config;
        $page->save();
        $this->dispatch('saved', type: 'success', message: 'Element box counter saved');
    }

    public function render()
    {
        return view('cb.element::BoxCounter.views.config');
    }

}
