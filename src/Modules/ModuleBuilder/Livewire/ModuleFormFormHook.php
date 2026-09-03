<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;

class ModuleFormFormHook extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $uuid;
    public $menu = 'FORM_HOOK';
    public $menuIconOnly = false;
    public $input = [];
    public $status = false;


    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->form = CbModule::where('uuid', $uuid)->first()->config;
        $this->input = $this->form['formHook'] ?? [];
        $this->status = $this->form['formHookStatus'] ?? false;
    }

    public function updated()
    {
        $this->form['formHookStatus'] = $this->status;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
    }

    public function formSave()
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        if($this->input) {
            $this->form['formHook'] = $this->input;
            $this->form['formHookStatus'] = $this->status;
            CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        }

        $this->showAlertMessage('Form hook saved successfully');

        $this->buildModuleConfirm();
    }

    public function render()
    {
        return view("cb.module-builder::module_form_hook")->layout("cb.themes::layout-app");
    }

}
