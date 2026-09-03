<?php

namespace CrudBooster\Modules\PageBuilder\Elements\GoogleMap;

use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GoogleMapElementViewer extends Component
{
    public $config;
    public $id;
    public $mapUrl;
    public function mount($id, $config)
    {
        $this->config = $config;
        $this->id = $id;
    }

    public function render()
    {
        return view('cb.element::' . basename(__DIR__) . '.views.view');
    }
}
