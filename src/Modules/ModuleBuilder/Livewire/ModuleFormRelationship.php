<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

class ModuleFormRelationship extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $uuid;
    public $menu = 'RELATIONSHIP';
    public $menuIconOnly = true;
    public $relationships = [];


    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->form = CbModule::where('uuid', $uuid)->first()->config;
        $this->relationships = $this->form['relationships'] ?? [];
    }

    public function addRelation()
    {
        $this->relationships = $this->relationships ?? [];
        $this->relationships[] = [
            'key' => null,
            'tableFirst' => null,
            'firstField' => null,
            'tableSecond' => null,
            'secondField' => null,
            'operator' => '=',
            'type' => 'left'
        ];
    }

    public function removeRelation($index)
    {
        unset($this->relationships[$index]);
    }

    #[Computed]
    public function tableSecondList()
    {
        // Add table from form and table from relationship to array
        $result = [['key' => $this->form['table_name'] ?? $this->form['table'], 'table' => $this->form['table_name'] ?? $this->form['table']]];
        foreach ($this->relationships as $relationship) {
            if (isset($relationship['key'])) {
                $result[] = [
                    'key' => $relationship['key'],
                    'table' => $relationship['tableFirst']
                ];
            }
        }
        return $result;
    }

    public function updated()
    {
        // For relationship input fields
        foreach ($this->relationships as $index => $relationship) {
            if (!empty($relationship['tableFirst'])) {
                $this->relationships[$index]['key'] = Str::singular($relationship['tableFirst']) . $index;
                $this->relationships[$index]['tableFirstFields'] = Schema::getColumnListing($relationship['tableFirst']);
            }
            if (!empty($relationship['tableSecond'])) {
                $realTable = collect($this->tableSecondList())->where('key', $relationship['tableSecond'])->first()['table'];
                if($realTable == ($this->form['table_name'] ?? '')) {
                    $this->relationships[$index]['tableSecondFields'] = collect($this->form['schema'])->pluck('name')->toArray();
                } else {
                    $this->relationships[$index]['tableSecondFields'] = Schema::getColumnListing($realTable);
                }
            }
        }
    }

    public function formSave()
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        $this->form['relationships'] = $this->relationships ?? [];
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        $this->showAlertMessage('Relationship saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/hook-query'), navigate: true);
    }

    public function render()
    {
        return view("cb.module-builder::module_relationship")->layout("cb.themes::layout-app");
    }

}
