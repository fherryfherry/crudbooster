<?php

namespace CrudBooster\Modules\Menu\Livewire;

use CrudBooster\Attributes\OnDataDeleted;
use CrudBooster\Attributes\OnDragged;
use CrudBooster\Livewire\BaseBrowseComponent;
use CrudBooster\Livewire\ColumnBuilder\Column;
use CrudBooster\Modules\Menu\Models\CBMenu;
use CrudBooster\Modules\Menu\Services\CBMenuService;

class Menu extends BaseBrowseComponent
{
    public $pageTitle = 'Menu Management';
    protected $modelService = CBMenuService::class;
    protected $modelName = CBMenu::class;
    public $buttonExportPdf = false;
    public $buttonExportXls = false;
    public $buttonExportCsv = false;
    public $buttonImport = false;
    public $buttonFilter = false;
    public $perPage = 10000;
    public function init(): void
    {
        $this->makeColumns([
            Column::add(label: 'Menu Name', key: 'name', sortable: false),
        ])->draggable('menu_order', 'parent_id');
    }

    #[OnDragged]
    public function rebuildMenuOnDragged($ids)
    {
        cache()->forget('cb_menu_all');
    }

    #[OnDataDeleted]
    public function menuDeletedCallback($id)
    {
        cache()->forget('cb_menu_all');
    }
}
