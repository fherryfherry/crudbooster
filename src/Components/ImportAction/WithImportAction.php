<?php

namespace CrudBooster\Components\ImportAction;

use CrudBooster\Components\ExportImport\DataImport;
use CrudBooster\Components\ExportImport\DataTemplate;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

trait WithImportAction
{
    use withFileUploads;

    public $importFile;
    public $importActionClass = DataImport::class;
    public $importTemplateClass = DataTemplate::class;

    public function downloadTemplate()
    {
        $fields = $this->modelService::getFieldExceptPrimary();
        return (new $this->importTemplateClass($fields))->download(sprintf('import_%s_template.xlsx', $this->pageTitle));
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xls,xlsx|max:' . config('cb.max_import_size', 1024),
        ]);

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $browsePath = $this->browsePath ?? 'dashboard';
            $this->redirectIntended(getCmsUrl($browsePath), navigate: true);
            return;
        }

        $file = $this->importFile->store('public');
        Excel::import(new $this->importActionClass($this->modelService), $file);
        $this->showAlertMessage(sprintf('Data imported successfully from file %s', $this->importFile->getClientOriginalName()));
        $this->dispatch('closeModalImport');
    }
}
