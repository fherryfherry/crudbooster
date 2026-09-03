<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModuleFormTableSchema extends ModuleForm
{
    use WithAlertMessage;
    use WithDbSchema;
    use WithConfirmMessage;

    public $form;
    public $uuid;
    public $menu = 'TABLE_SCHEMA';
    public $columns;

    public $columnName;
    public $columnDataType = 'String';
    public $columnConfig = [];

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->form = CbModule::where('uuid', $uuid)->first()->config;
        $this->form['primaryKey'] = $this->form['primaryKey'] ?? 'id';
        $this->columns = $this->form['schema'] ?? $this->getColumnFromTable($this->form['table_name'] ?? $this->form['table']);
        $this->columns = $this->columns ?: $this->defaultColumn();
        $this->columnConfig = $this->defaultColumnConfig();
    }

    private function getColumnFromTable($table)
    {
        // get column list and type from table
        $columns = Schema::getColumnListing($table);
        $columnList = [];
        foreach ($columns as $column) {
            $config = $this->defaultColumnConfig();
            $config['autoIncrement'] = $this->isAutoIncrement($table, $column);
            $config['length'] = $this->getColumnLength($table, $column);
            $config['default'] = $this->getColumnDefault($table, $column);
            $config['unique'] = $this->isUnique($table, $column);
            $config['nullable'] = $this->isNullable($table, $column);
            $columnList[] = [
                'name' => $column,
                'type' => convertSqlTypeToLaravelType(Schema::getColumnType($table, $column)),
                'config' => $config
            ];
        }
        return $columnList;
    }

    private function defaultColumn()
    {
        $defaultColumns = [];
        $defaultConfig = $this->defaultColumnConfig();
        $defaultColumns[] = [
            'name' => 'id',
            'type' => 'uuid'
        ];

        $defaultColumns[] = [
            'name' => 'created_at',
            'type' => 'timestamp',
            'config' => array_merge($defaultConfig, ['nullable' => true])
        ];

        $defaultColumns[] = [
            'name' => 'updated_at',
            'type' => 'timestamp',
            'config' => array_merge($defaultConfig, ['nullable' => true])
        ];

        $defaultColumns[] = [
            'name' => 'deleted_at',
            'type' => 'timestamp',
            'config' => array_merge($defaultConfig, ['nullable' => true])
        ];

        return $defaultColumns;
    }

    private function defaultColumnConfig()
    {
        return [
            'nullable' => true,
            'default' => null,
            'unique' => false,
            'length' => 255,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ];
    }

    public function addColumn()
    {
        if ($this->columnName != '' && $this->columnDataType != '') {
            $this->columns[] = [
                'name' => $this->columnName,
                'type' => $this->columnDataType,
                'config' => $this->columnConfig
            ];
            $this->columnName = '';
            $this->columnDataType = 'String';
            $this->columnConfig = $this->defaultColumnConfig();
        }
    }

    public function removeColumn($name)
    {
        $this->columns = array_filter($this->columns, function ($column) use ($name) {
            return $column['name'] != $name;
        });
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

        try {
            $this->validatePrimaryKeySchema();
        } catch (\Exception $e) {
            $this->showAlertMessage($e->getMessage());
            $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/table-schema'), navigate: true);
            return;
        }

        $this->form['schema'] = $this->columns;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);

        // Create default browse column and form column
        if(!isset($this->form['browse_columns'])) {
            $this->createDefaultBrowseColumn($this->form['table_name'] ?? $this->form['table'], $this->columns);
        }
        if(!isset($this->form['formDesignList'])) {
            $this->createDefaultFormColumn($this->form['table_name'] ?? $this->form['table'], $this->columns);
        }

        $this->showAlertMessage('Schema saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/relationship'), navigate: true);
    }

    private function createDefaultBrowseColumn($table, $columns)
    {
        // Filter column id, uuid, created_at, updated_at, deleted_at
        $columns = array_filter($columns, function ($column) {
            return !in_array($column['name'], ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at']);
        });
        $browseColumns = [];
        foreach ($columns as $column) {
            $browseColumns[] = [
                'key' => $table . '.' . $column['name'],
                'label' => $column['name'],
                'sortable' => true,
                'exportable' => true,
                'filterable' => true,
                'searchable' => true
            ];
        }
        $this->form['browse_columns'] = $browseColumns;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
    }
    private function passwordTypeCheck($type)
    {
        $passwordType = [
            'password',
            'pin',
            'secret',
            'passcode',
            'security_code',
            'access_code',
            'auth_code',
            'verification_code',
            'otp',
            'token',
            'api_key',
            'private_key',
            'encryption_key',
            'decryption_key',
            'credential',
            'login_key',
            'passphrase',
            'keyphrase',
            'secret_key',
            'master_key'
        ];
        foreach ($passwordType as $password) {
            if (Str::contains($type, $password)) {
                return true;
            }
        }
        return false;
    }
    private function createDefaultFormColumn($table, $columns)
    {
        $columns = array_filter($columns, function ($column) {
            return !in_array($column['name'], ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at']);
        });
        $columns = array_filter($columns, function ($column) {
            return !$this->passwordTypeCheck($column['type']);
        });
        $formColumns = [];
        foreach ($columns as $column) {
            $label = Str::title(str_replace('_',' ',$column['name']));
            $type = $this->getFormType($column['type']);
            $formColumns[] = [[
                'key' => $table . '.' . $column['name'],
                'type' => $type,
                'label' => $label,
                'helpText' => 'Input the ' . $label . ' here',
                'showCreate' => true,
                'showEdit' => true,
                'showDetail' => true
            ]];
        }
        $this->form['formDesignList'] = $formColumns;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
    }

    private function getFormType($schemaType): string
    {
        switch ($schemaType) {
            case 'bigIncrements':
            case 'mediumInteger':
            case 'smallInteger':
            case 'integer':
            case 'float':
            case 'double':
            case 'decimal':
            case 'bigInteger':
                return 'number';
            case 'binary':
                return 'file';
            case 'boolean':
                return 'checkbox';
            case 'date':
            case 'dateTime':
            case 'dateTimeTz':
                return 'date';
            case 'ipAddress':
                return 'string';
            case 'json':
            case 'jsonb':
            case 'longText':
            case 'mediumText':
            case 'macAddress':
                return 'textarea';
            default:
                return 'text';
        }
    }

    public function render()
    {
        return view("cb.module-builder::module_schema")->layout("cb.themes::layout-app");
    }

}
