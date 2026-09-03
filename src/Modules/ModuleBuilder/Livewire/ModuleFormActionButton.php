<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;

class ModuleFormActionButton extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $uuid;
    public $menu = 'ACTION_BUTTON';
    public $menuIconOnly = false;
    public $columns = [];
    public $status = false;


    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->form = CbModule::where('uuid', $uuid)->first()->config;
        $this->columns = $this->form['actionButtonList'] ?? [];
        $this->status = $this->form['actionButtonStatus'] ?? false;
    }

    public function addColumn()
    {
        $this->columns = $this->columns ?? [];
        $this->columns[] = [
            'label' => null,
            'url'=> sprintf('/%s/your-action/{id}', $this->form['path']),
            'templateMode'=>'ICON_ONLY',
            'class'=> 'btn btn-primary',
            'confirm'=> 0,
            'target'=>'_self',
            'permission'=>'Read',
            'icon'=>'BOLT'
        ];
    }

    public function removeColumn($index)
    {
        unset($this->columns[$index]);
    }

    public function updated()
    {
        $this->form['actionButtonStatus'] = $this->status;
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
            $this->form['actionButtonList'] = $this->columns;
            $this->form['actionButtonStatus'] = $this->status;
            CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        }

        $this->showAlertMessage('Action button saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/form-design'), navigate: true);
    }

    public function render()
    {
        return view("cb.module-builder::module_action_button")->layout("cb.themes::layout-app");
    }

}
