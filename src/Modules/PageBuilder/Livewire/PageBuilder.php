<?php

namespace CrudBooster\Modules\PageBuilder\Livewire;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Livewire\BaseBrowseComponent;
use CrudBooster\Livewire\ColumnBuilder\Column;
use CrudBooster\Modules\PageBuilder\Models\CbPage;
use CrudBooster\Modules\PageBuilder\Services\CbPageService;

class PageBuilder extends BaseBrowseComponent
{
    public $pageTitle = 'Page Builder';
    protected $modelService = CbPageService::class;
    protected $modelName = CbPage::class;

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

        $this->addActionButton('Edit', 'page-builder/{id}/studio', Icon::EDIT);
        $this->addActionButton('Preview', 'p/{id}', Icon::EYE);

        $this->makeColumns([
            Column::add(label: 'Page Name', key: 'name')
        ]);
    }

    public function create()
    {
        // Create a new page with default title
        $data = [
            'name' => 'Sample Page Title',
            'path' => 'sample-page-title',
            'config' => [],
        ];
        $page = \CrudBooster\Modules\PageBuilder\Models\CbPage::create($data);
        // Redirect to the studio editor for the new page
        return redirect()->to(getCmsUrl('page-builder/' . $page->id . '/studio'));
    }

    public function createWithTitle($title)
    {
        $data = [
            'name' => $title,
            'path' => \Str::slug($title),
            'config' => [],
        ];
        $page = \CrudBooster\Modules\PageBuilder\Models\CbPage::create($data);
        $this->redirect(getCmsUrl('page-builder/' . $page->id . '/studio'), navigate: true);
    }
}
