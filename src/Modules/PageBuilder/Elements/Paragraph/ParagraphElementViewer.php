<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Paragraph;

use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ParagraphElementViewer extends Component
{
    public $config;
    public $paragraph;
    public $id;
    public function mount($id,$config)
    {
        $this->config = $config;
        $this->id = $id;
        $this->paragraph = $config['paragraph']??'';
    }
    public function render()
    {
        return view('cb.element::'.basename(__DIR__).'.views.view');
    }

}
