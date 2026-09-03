<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Heading;

use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class HeadingElementViewer extends Component
{
    public $config;
    public $heading;
    public $id;
    public function mount($id,$config)
    {
        $this->config = $config;
        $this->id = $id;
        $this->heading = $config['heading']??'';
    }
    public function render()
    {
        return view('cb.element::'.basename(__DIR__).'.views.view');
    }

}
