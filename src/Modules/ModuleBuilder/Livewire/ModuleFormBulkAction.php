<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Livewire\Attributes\Computed;

class ModuleFormBulkAction extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $uuid;
    public $menu = 'BULK_ACTION';
    public $menuIconOnly = true;
    public $columns = [];
    public $status = false;


    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->form = CbModule::where('uuid', $uuid)->first()->config;
        $this->columns = $this->form['bulkActionList'] ?? [];
        $this->status = $this->form['bulkActionStatus'] ?? false;
    }

    public function addColumn()
    {
        $this->columns = $this->columns ?? [];
        $this->columns[] = [
            'label' => null,
            'icon' => null,
            'action' => null,
            'confirmTitle' => null,
            'confirmMessage' => null,
            'permission' => null,
        ];
    }

    public function removeColumn($index)
    {
        unset($this->columns[$index]);
    }

    #[Computed]
    public function actionList()
    {
        return [
          'DELETE_ALL'
        ];
    }

    public function updated()
    {
        // For relationship input fields
        foreach ($this->columns as $index => $relationship) {

        }

        $this->form['bulkActionStatus'] = $this->status;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
    }

    public function formSave()
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        if($this->columns) {
            $this->form['bulkActionList'] = $this->columns;
            $this->form['bulkActionStatus'] = $this->status;
            CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        }

        $this->showAlertMessage('Bulk action saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/action-button'), navigate: true);
    }

    public function render()
    {
        return view("cb.module-builder::module_bulk_action")->layout("cb.themes::layout-app");
    }

}
