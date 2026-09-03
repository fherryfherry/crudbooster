<?php

namespace CrudBooster\Modules\Role\Livewire;

use CrudBooster\Livewire\BaseBrowseComponent;
use CrudBooster\Livewire\ColumnBuilder\Column;
use CrudBooster\Modules\Role\Models\CBRole;
use CrudBooster\Modules\Role\Services\CbRoleService;

class Role extends BaseBrowseComponent
{
    public $pageTitle = 'Role Management';
    protected $modelService = CbRoleService::class;
    protected $modelName = CBRole::class;
    public $buttonExportCsv = false;
    public $buttonExportPdf = false;
    public $buttonExportXls = false;
    public $buttonImport = false;
    public $buttonFilter = false;
    public function init(): void
    {
        // Hide delete button for the first row, since first row is the default role
        $this->hideDeleteButtonWhen('name', config('cb.super_admin_role') ?? 'SUPER ADMIN');
        $this->hideCheckboxWhen('name', config('cb.super_admin_role') ?? 'SUPER ADMIN');
        $this->hideEditButtonWhen('name', config('cb.super_admin_role') ?? 'SUPER ADMIN');

        $this->makeColumns([
            Column::add(label: 'Role Name', key: 'name'),
        ]);
    }
}
