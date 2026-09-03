<?php

namespace CrudBooster\Modules\QueryBuilder\Livewire;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Livewire\BaseBrowseComponent;
use CrudBooster\Livewire\ColumnBuilder\Column;
use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;

class QueryBuilder extends BaseBrowseComponent
{
    public $pageTitle = 'Query Builder';
    protected $modelService = CbQueryBuilderService::class;
    protected $modelName = CbQueryBuilder::class;

    public $buttonExportCsv = false;
    public $buttonExportPdf = false;
    public $buttonExportXls = false;
    public $buttonImport = false;
    public $buttonFilter = false;
    public $buttonDetail = false;
    public $buttonEdit = false;
    public $perPage = 100;

    public function init(): void
    {
        // Prevent module builder from being accessed in production
        $this->freezeMode(config('app.ENV') === 'production');

        $this->addActionButton('Edit', 'query-builder/{id}/form', Icon::EDIT);

        $this->makeColumns([
            Column::add(label: 'Query Name', key: 'name'),
        ]);
    }

}
