<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Livewire\BaseBrowseComponent;
use CrudBooster\Livewire\ColumnBuilder\Column;
use CrudBooster\Modules\Menu\Models\CBMenu;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use CrudBooster\Modules\ModuleBuilder\Services\CbModuleService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Module extends BaseBrowseComponent
{
    public $pageTitle = 'Module Builder';
    protected $modelService = CbModuleService::class;
    protected $modelName = CbModule::class;

    public $buttonExportCsv = false;
    public $buttonExportXls = false;
    public $buttonExportPdf = false;
    public $buttonImport = false;
    public $buttonFilter = false;
    public $buttonDetail = false;
    public $buttonEdit = false;
    public $perPage = 100;
    public $buttonActionStyle = "ICON_TEXT";

    public function init(): void
    {
        // Prevent module builder from being accessed in production
        $this->freezeMode(config('app.ENV') === 'production');

        $this->addActionButton('Edit', 'module-builder/{uuid}/info', Icon::EDIT);

        $this->addActionButton('Hard Delete', function ($row) {
            if(config('cb.demo_mode')) {
                $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
                $this->redirectIntended(getCmsUrl('module-builder'));
                return;
            }

            $table = $row->config['table'];
            $path = $row->config['path'];
            // remove table
            if ($table) {
                Schema::disableForeignKeyConstraints();
                Schema::dropIfExists($table);
                Schema::enableForeignKeyConstraints();
            }
            // remove menu
            if ($path) CBMenu::where('menu_type', 'MODULE')->where('menu_value', $path)->delete();
            // remove directory script
            if ($table) {
                $directory = app_path('Cb/Modules/' . Str::studly($table));
                // Remove the directory
                if (is_dir($directory)) {
                    // remove directory with laravel
                    File::deleteDirectory($directory);
                }
            }

            // remove module
            $row->delete();

            $this->showAlertMessage('The data has been deleted!', 'success');
            $this->redirectIntended(getCmsUrl($this->browsePath));
        }, Icon::TRASH)
            ->buttonClass('btn btn-danger')
            ->confirmation();

        $this->makeColumns([
            Column::add(label: 'Module Name', key: 'name'),
        ]);
    }
}
