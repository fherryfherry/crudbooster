<?php

namespace CrudBooster\Modules\PageBuilder\Elements\BoxCounter;

use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Livewire\Component;

class BoxCounterElementViewer extends Component
{
    public $config;
    public $percentage;
    public $count = null;
    public $id;
    public function mount($id,$config)
    {
        $this->config = $config;
        $this->id = $id;
    }

    public function loadData()
    {
        $this->count = 0;

        $queryId = $this->config['queryBuilder']??null;
        if($queryId) {
            $query = CbQueryBuilder::where('id',$queryId)->first();
            if($query) {
                $config = $query->config;
                $result = CbQueryBuilderService::runQuery($config);
                if($result) {
                    $this->count = $result[0]->aggregate_result ?? 0;
                }
            }
        }

        $this->percentage = 0;
        $queryId = $this->config['queryLast']??null;
        if($queryId) {
            $query = CbQueryBuilder::where('id',$queryId)->first();
            if($query) {
                $config = $query->config;
                $result = CbQueryBuilderService::runQuery($config);
                if($result) {
                    $lastCount = $result[0]->aggregate_result ?? 0;
                    // calculate percentage and allow if it minus or plus
                    $this->percentage = $lastCount ? round(($this->count - $lastCount) / $lastCount * 100) : 100;
                }
            }
        }
    }

    public function render()
    {
        return view('cb.element::'.basename(__DIR__).'.views.view');
    }

}
