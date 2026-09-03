<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Chart;

use CrudBooster\Modules\PageBuilder\Models\CbPage;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ChartElement extends Component
{
    public $form = [];
    public $pageId;
    public $rowIndex;
    public $colIndex;
    public $config;
    public $id;
    public $showCreateQueryModal = false;

    public function mount($id = null, $rowIndex = null, $colIndex = null)
    {
        $this->id = $rowIndex . $colIndex;
        $this->pageId = $id;
        $this->rowIndex = $rowIndex;
        $this->colIndex = $colIndex;
        if ($this->pageId) {
            $this->config = CbPage::where('id', $this->pageId)->first()?->config;
            $this->form = ($this->config) ? $this->config[$rowIndex][$colIndex]['content']['config'] ?? [] : [];
            $this->form['chartType'] = $this->form['chartType'] ?? 'line';
            if (!isset($this->form['datasets'])) {
                $this->addDataset();
            }
        }
    }

    #[Computed]
    public function queryBuilderList()
    {
        return CbQueryBuilderService::getDataWithOutputTypeArray();
    }

    public function save()
    {
        $this->validate([
            'form.title' => 'required',
            'form.datasets' => 'required'
        ]);

        // update to page
        $page = CbPage::where('id', $this->pageId)->first();
        $config = $page->config;
        $config[$this->rowIndex][$this->colIndex]['content']['config'] = $this->form;
        $page->config = $config;
        $page->save();
        $this->dispatch('saved', type: 'success', message: 'Element Chart saved');
    }

    public function render()
    {
        return view('cb.element::Chart.views.config');
    }

    public function addDataset()
    {
        $this->form['datasets'][] = [
            'label' => '',
            'backgroundColor' => '#ffffff',
            'borderColor' => '#000000',
            'query' => null,
            'pointField' => 'total',
            'borderWidth' => 1,
            'fill' => false
        ];
    }

    public function removeDataset($index)
    {
        unset($this->form['datasets'][$index]);
        $this->form['datasets'] = array_values($this->form['datasets']);
    }
}
