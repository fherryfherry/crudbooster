<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Components\Icon\Icon;
use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Helpers\Facades\SchemaUtil;
use CrudBooster\Modules\ModuleBuilder\Builder\ModuleBuilder;
use CrudBooster\Modules\ModuleBuilder\Builder\ModuleBuilderCommon;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ModuleForm extends Component
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $uuid;

    #[Computed]
    public function typeList()
    {
        return CBTypeRegistrar::__getAllTypeGrouped();
    }
    #[Computed]
    public function iconList()
    {
        $class = new \ReflectionClass(Icon::class);
        $result = [];
        // get all constants
        foreach ($class->getConstants() as $key => $value) {
            $result[] = $key;
        }
        sort($result);
        return $result;
    }
    #[Computed]
    public function fieldGroup()
    {
        return (new ModuleBuilderCommon($this->form ?? []))->fieldGroup();
    }

    #[Computed]
    public function tableList()
    {
        return SchemaUtil::getTableListing();
    }

    #[Computed]
    public function tableFields()
    {
        $tableFields = Schema::getColumnListing($this->form['table_name'] ?? $this->form['table']);
        return $tableFields ?: collect($this->form['schema'])->map(fn($column) => $column['name'])->toArray();
    }

    #[Computed]
    public function modelList()
    {
        return getModelList();
    }

    #[Computed]
    public function validateBuild()
    {
        return (new ModuleBuilder($this->form ?? []))->validate();
    }

    public function buildModuleConfirm()
    {
        $this->showConfirmMessage('Are you sure want to build the module?', '
            Building/Rebuilding the module will delete all the files in the module directory.
            ', 'buildModule', 'Yes', 'success');
    }

    public function validatePrimaryKeySchema()
    {
        // check if primary key is exist
        if(!isset($this->form['primaryKey'])) {
            throw new \Exception('Primary key is required');
        }
        // check if primary key is in scheme
        if(isset($this->form['schema'])) {
            $primaryKeyExist = collect($this->form['schema'])->contains('name', $this->form['primaryKey']);
            if(!$primaryKeyExist) {
                throw new \Exception('Primary key is not exist in schema');
            }
        }
    }

    public function buildModule($uuid = null)
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        try {
            $this->validatePrimaryKeySchema();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->showAlertMessage($e->getMessage());
            $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/table-schema'), navigate: true);
            return;
        }

        $builder = new ModuleBuilder($this->form);
        $builder->build();

        // Save table name to table
        $this->saveTableName($uuid);

        if(!$uuid) {
            $this->showAlertMessage("Module built successfully", 'success');
            $this->redirect(getCmsUrl('module-builder'), navigate: true);
        }
    }

    private function saveTableName($uuid = null)
    {
        $this->form['table'] = $this->form['table_name'] ?? $this->form['table'];
        $this->form['table_name'] = null;
        unset($this->form['is_new_table']);
        CbModule::where('uuid', $uuid ?: $this->uuid)->update(['config' => $this->form]);
    }
}
