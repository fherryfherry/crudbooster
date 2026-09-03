<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Illuminate\Support\Facades\Schema;

class ModuleFormBrowseDesign extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $table;
    public $uuid;
    public $menu = 'BROWSE_DESIGN';
    public $menuIconOnly = true;

    public $input = [];
    public $lastKey;
    public $columns = [];
    public $displayFieldList;

    public function mount($uuid)
    {
        $module = CbModule::where('uuid', $uuid)->first();
        $this->uuid = $uuid;
        $this->form = $module->config;
        $this->table = $this->form['table_name'] ?? $this->form['table'];
        $this->input = $this->defaultInput();
        $this->columns = $this->form['browse_columns'] ?? [];
    }

    public function resetColumns()
    {
        $groups = $this->fieldGroup();
        $columns = [];
        foreach ($groups as $group) {
            foreach ($group['fields'] as $field) {
                $columns[] = $group['table'].'.'.$field;
            }
        }

        foreach ($columns as $column) {
            $this->columns[] = [
                'label' => ucwords(str_replace(['.','_'], ' ', $column)),
                'key' => $column,
                'searchable' => true,
                'sortable' => true,
                'filterable' => true,
                'exportable' => true,
                'config' => []
            ];
        }
    }

    public function addEmptyInputRelation()
    {
        $this->input['config']['relationList'] = $this->input['config']['relationList'] ?? [];
        $this->input['config']['relationList'][] = [
            'key' => null,
            'table' => null,
            'first' => null,
            'second' => null,
            'operator' => '=',
            'type' => 'left'
        ];
    }

    public function removeInputRelation($index)
    {
        unset($this->input['config']['relationList'][$index]);
    }

    public function updated()
    {
        // For relationship input fields
        if (!empty($this->input['config']['relationList'])) {
            foreach ($this->input['config']['relationList'] as $key => $relation) {
                if (!empty($relation['table'])) {
                    $this->input['config']['relationList'][$key]['tableFields'] = Schema::getColumnListing($relation['table']);
                    if(empty($relation['key'])) {
                        $this->input['config']['relationList'][$key]['key'] = $relation['table'].$key;
                    }
                    // Set first to primary key of table
                    $this->input['config']['relationList'][$key]['first'] = 'id';
                    // Set second to foreign key of table
                    $this->input['config']['relationList'][$key]['second'] = ($this->form['table_name']??$this->form['table']) . '.' . $relation['table'] . '_id';
                } else {
                    $this->input['config']['relationList'][$key]['tableFields'] = [];
                }
            }
        }

        if (!empty($this->input['config']['model'])) {
            $this->input['config']['modelFields'] = Schema::getColumnListing((new $this->input['config']['model'])->getTable());
        } else {
            $this->input['config']['modelFields'] = [];
        }
        if (!empty($this->input['config']['modelMany'])) {
            $this->input['config']['modelManyFields'] = Schema::getColumnListing((new $this->input['config']['modelMany'])->getTable());
        } else {
            $this->input['config']['modelManyFields'] = [];
        }
        if (!empty($this->input['config']['displayModel'])) {
            $this->input['config']['displayModelFields'] = Schema::getColumnListing((new $this->input['config']['displayModel'])->getTable());
        } else {
            $this->input['config']['displayModelFields'] = [];
        }

        foreach ($this->columns as $key => $column) {
            if (!empty($column['config']['model'])) {
                $this->columns[$key]['config']['modelFields'] = Schema::getColumnListing((new $column['config']['model'])->getTable());
            } else {
                $this->columns[$key]['config']['modelFields'] = [];
            }
            if (!empty($column['config']['modelMany'])) {
                $this->columns[$key]['config']['modelManyFields'] = Schema::getColumnListing((new $column['config']['modelMany'])->getTable());
            } else {
                $this->columns[$key]['config']['modelManyFields'] = [];
            }
            if (!empty($column['config']['displayModel'])) {
                $this->columns[$key]['config']['displayModelFields'] = Schema::getColumnListing((new $column['config']['displayModel'])->getTable());
            } else {
                $this->columns[$key]['config']['displayModelFields'] = [];
            }
            if(!empty($column['config']['transformNumberFormat'])) {
                $this->columns[$key]['config']['decimal'] = $column['config']['decimal'] ?? 0;
                $this->columns[$key]['config']['decimalSeparator'] = $column['config']['decimalSeparator'] ?? '.';
                $this->columns[$key]['config']['thousandSeparator'] = $column['config']['thousandSeparator'] ?? ',';
            }
            if(!empty($column['config']['transformDateFormat'])) {
                $this->columns[$key]['config']['dateFormat'] = $column['config']['dateFormat'] ?? 'Y-m-d';
            }
        }
    }

    private function defaultInput()
    {
        return [
            'label' => null,
            'key' => null,
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'exportable' => true,
            'config' => []
        ];
    }

    public function addColumn()
    {
        $this->columns[] = $this->defaultInput();
    }

    public function removeColumn($index)
    {
        if ($this->lastKey == $this->columns[$index]['key']) {
            $this->lastKey = null;
        }
        unset($this->columns[$index]);
    }

    public function formSave()
    {
        $this->validate([
            'columns' => 'required',
        ]);

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        // Remove column with empty label
        $this->columns = array_filter($this->columns, function($column) {
            return !empty($column['label']);
        });

        $this->form['browse_columns'] = $this->columns;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        $this->showAlertMessage('Browse design saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/bulk-action'), navigate: true);
    }

    public function render()
    {
        return view("cb.module-builder::module_browse_design")->layout("cb.themes::layout-app");
    }

}
