<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Table;

use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Livewire\Component;

class TableElementViewer extends Component
{
    public $config;
    public $id;
    public $results = [];
    public function mount($id, $config)
    {
        $this->config = $config;
        $this->id = $id;
        if ($this->config['query']) {
            $query = CbQueryBuilder::where('id', $this->config['query'])->first();
            $this->results = CbQueryBuilderService::runQuery($query->config, $this->config['limit']);
            $this->results = collect($this->results)->take($this->config['limit'])->toArray();
        }
    }
    public function render()
    {
        return view('cb.element::' . basename(__DIR__) . '.views.view');
    }
}
