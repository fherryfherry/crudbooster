<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModuleFormBasicInfo extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $tableList;

    public $menu = 'BASIC_INFO';

    protected $lastName;

    public function mount($uuid = null)
    {
        $this->uuid = $uuid;
        $this->tableList = $this->tableList();

        if($this->uuid) {
            $this->form = CbModule::where('uuid', $this->uuid)->first()->config;
        } else {
            $this->form['permission_create'] = true;
            $this->form['permission_read'] = true;
            $this->form['permission_update'] = true;
            $this->form['permission_delete'] = true;

            $this->form['button_create'] = true;
            $this->form['button_edit'] = true;
            $this->form['button_delete'] = true;
            $this->form['button_detail'] = true;
            $this->form['button_export_xls'] = true;
            $this->form['button_export_csv'] = true;
            $this->form['button_export_pdf'] = true;
            $this->form['button_import'] = true;
            $this->form['button_filter'] = true;
            $this->form['button_search_bar'] = true;
            $this->form['button_bulk_action'] = true;
        }
    }

    public function updated()
    {
        if (isset($this->form['name']) && $this->lastName != $this->form['name']) {
            $this->lastName = $this->form['name'];
            $this->form['path'] = Str::slug($this->form['name'], '-');
        }

        if (isset($this->form['table']) && $this->form['table'] !== "_NEW_") {
            $this->form['model'] = sprintf("App\\Cb\\Modules\\%s\\Models\\%s::class", Str::studly($this->form['name']), Str::studly($this->form['table']));
            $this->form['service'] = sprintf("App\\Cb\\Modules\\%s\\Services\\%sService::class", Str::studly($this->form['name']), Str::studly($this->form['table']));
        }

        if (isset($this->form['table_name'])) {
            if ($this->form['table_name']) {
                $this->form['model'] = sprintf("App\\Cb\\Modules\\%s\\Models\\%s::class", Str::studly($this->form['name']), Str::studly($this->form['table_name']));
                $this->form['service'] = sprintf("App\\Cb\\Modules\\%s\\Services\\%sService::class", Str::studly($this->form['name']), Str::studly($this->form['table_name']));
            } else {
                $this->form['model'] = null;
                $this->form['service'] = null;
            }
        }
    }

    public function formSave()
    {
        $this->validate([
            'form.name' => 'required',
            'form.path' => 'required',
            'form.table' => 'required',
            'form.model' => 'required',
            'form.service' => 'required',
        ]);

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        if($this->uuid) {
            $uuid = $this->uuid;
            $module = CbModule::where('uuid', $uuid)->first();
            $module->name = $this->form['name'];
            $module->config = $this->form;
            $module->save();
        } else {
            $uuid = Str::uuid();
            CbModule::query()->insert([
                'created_at'=> now(),
                'uuid' => $uuid,
                'name'=> $this->form['name'],
                'config' => json_encode($this->form)
            ]);
        }
        $this->showAlertMessage("Module saved successfully", 'success');
        $this->redirect(getCmsUrl('module-builder/'.$uuid.'/table-schema'), navigate: true);
    }

    public function rebuildConfirmation()
    {
        $this->showConfirmMessage("Re/Build Confirmation", "Are you sure you want to re/build this module?", "rebuild", 'Yes', 'success');
    }

    public function render()
    {
        return view("cb.module-builder::module_info")->layout("cb.themes::layout-app");
    }

}
