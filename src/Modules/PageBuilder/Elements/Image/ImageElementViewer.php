<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Image;

use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ImageElementViewer extends Component
{
    public $config;
    public $image;
    public $id;
    public function mount($id,$config)
    {
        $this->config = $config;
        $this->id = $id;
        $this->image = $config['image']??'';
    }
    public function render()
    {
        return view('cb.element::'.basename(__DIR__).'.views.view');
    }

}
