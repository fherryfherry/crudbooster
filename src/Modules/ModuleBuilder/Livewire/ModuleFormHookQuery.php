<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;

class ModuleFormHookQuery extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $uuid;
    public $menu = 'HOOK_QUERY';
    public $menuIconOnly = true;
    public $columns = [];


    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->form = CbModule::where('uuid', $uuid)->first()->config;
        $this->columns = $this->form['hookQueryList'] ?? [];
    }

    public function addQuery()
    {
        $this->columns = $this->columns ?? [];
        $this->columns[] = [
            'type' => 'AND',
            'field' => null,
            'operator' => '=',
            'value' => null,
            'group' => false
        ];
    }

    public function removeQuery($index)
    {
        unset($this->columns[$index]);
    }

    public function updated()
    {
    }

    public function formSave()
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        if($this->columns) {
            $this->form['hookQueryList'] = $this->columns;
            CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        }

        $this->showAlertMessage('Hook query saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/browse-design'), navigate: true);
    }

    public function render()
    {
        return view("cb.module-builder::module_hook_query")->layout("cb.themes::layout-app");
    }

}
