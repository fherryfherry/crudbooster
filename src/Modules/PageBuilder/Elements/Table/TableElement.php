<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Table;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Modules\PageBuilder\Models\CbPage;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class TableElement extends Component
{
    use WithFileUploads;

    public $form = [];
    public $pageId;
    public $rowIndex;
    public $colIndex;
    public $config;
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
        }
    }

    #[Computed]
    public function queryBuilderList()
    {
        return CbQueryBuilderService::getDataWithOutputTypeArray();
    }

    #[Computed]
    public function getIcons()
    {
        return Icon::getIcons();
    }

    public function save()
    {
        $this->validate([
            'form.query' => 'required',
            'form.title' => 'required',
            'form.limit' => 'required',
        ]);

        // update to page
        $page = CbPage::where('id', $this->pageId)->first();
        $config = $page->config;
        $config[$this->rowIndex][$this->colIndex]['content']['config'] = $this->form;
        $page->config = $config;
        $page->save();
        $this->dispatch('saved', type: 'success', message: 'Element Table saved');
    }

    public function render()
    {
        return view('cb.element::Table.views.config');
    }
}
