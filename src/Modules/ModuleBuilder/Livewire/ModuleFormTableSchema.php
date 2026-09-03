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

        // Any column linked to another table (via the "Relation" picker) gets its join
        // auto-added to the Relationship step, so the user never has to define it twice.
        $this->syncRelationsFromSchema($this->form['table_name'] ?? $this->form['table'], $this->columns);

        // Create default browse column and form column for any column that doesn't have one yet,
        // so newly added columns on a re-saved schema also get sensible defaults.
        $this->createDefaultBrowseColumn($this->form['table_name'] ?? $this->form['table'], $this->columns);
        $this->createDefaultFormColumn($this->form['table_name'] ?? $this->form['table'], $this->columns);

        $this->showAlertMessage('Schema saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/relationship'), navigate: true);
    }

    /**
     * Look up what we know about another table: its column names and primary key,
     * either from a module already built with this wizard (config['schema']),
     * or from the live database if the table already physically exists.
     */
    private function resolveTargetTableMeta($table)
    {
        $module = CbModule::query()->get()->first(function ($m) use ($table) {
            $cfg = $m->config ?? [];
            return ($cfg['table_name'] ?? $cfg['table'] ?? null) === $table;
        });

        if ($module) {
            $cfg = $module->config;
            return [
                'fields' => collect($cfg['schema'] ?? [])->pluck('name')->filter()->values()->all(),
                'primaryKey' => $cfg['primaryKey'] ?? 'id',
                'name' => $cfg['name'] ?? Str::title(str_replace('_', ' ', $table)),
            ];
        }

        if (Schema::hasTable($table)) {
            return [
                'fields' => Schema::getColumnListing($table),
                'primaryKey' => 'id',
                'name' => Str::title(str_replace('_', ' ', $table)),
            ];
        }

        return ['fields' => [], 'primaryKey' => 'id', 'name' => Str::title(str_replace('_', ' ', $table))];
    }

    /**
     * Best-effort guess for which field of a related table is worth showing
     * to a human (e.g. "name" instead of the raw foreign key id).
     */
    private function guessDisplayField(array $fields): string
    {
        $preferred = ['name', 'title', 'label', 'email', 'username'];
        foreach ($preferred as $field) {
            if (in_array($field, $fields)) {
                return $field;
            }
        }
        foreach ($fields as $field) {
            if (!in_array($field, ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at'])) {
                return $field;
            }
        }
        return 'id';
    }

    /**
     * For every schema column linked to another table (config.relation.table),
     * make sure a matching join exists in the Relationship step. Existing
     * relationships (including ones the user has since edited by hand) are left alone.
     */
    private function syncRelationsFromSchema($table, $columns)
    {
        $relationships = $this->form['relationships'] ?? [];
        $existingPairs = collect($relationships)
            ->map(fn($r) => ($r['tableFirst'] ?? '') . '|' . ($r['secondField'] ?? ''))
            ->all();

        $changed = false;
        foreach ($columns as $column) {
            $targetTable = $column['config']['relation']['table'] ?? null;
            if (!$targetTable) {
                continue;
            }

            $pairKey = $targetTable . '|' . $column['name'];
            if (in_array($pairKey, $existingPairs)) {
                continue;
            }

            $meta = $this->resolveTargetTableMeta($targetTable);
            $index = count($relationships);
            $relationships[] = [
                'key' => Str::singular($targetTable) . $index,
                'tableFirst' => $targetTable,
                'firstField' => $meta['primaryKey'],
                'tableSecond' => $table,
                'secondField' => $column['name'],
                'operator' => '=',
                'type' => 'left',
                'tableFirstFields' => $meta['fields'],
                'tableSecondFields' => collect($columns)->pluck('name')->toArray(),
            ];
            $existingPairs[] = $pairKey;
            $changed = true;
        }

        if ($changed) {
            $this->form['relationships'] = $relationships;
            CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        }
    }

    private function createDefaultBrowseColumn($table, $columns)
    {
        // Filter column id, uuid, created_at, updated_at, deleted_at
        $columns = array_filter($columns, function ($column) {
            return !in_array($column['name'], ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at']);
        });

        $browseColumns = $this->form['browse_columns'] ?? [];
        $existingKeys = array_column($browseColumns, 'key');
        $relationships = $this->form['relationships'] ?? [];

        foreach ($columns as $column) {
            $targetTable = $column['config']['relation']['table'] ?? null;

            if ($targetTable) {
                // Show a field from the linked table (e.g. category name) instead of the raw id,
                // reusing the join already added to the Relationship step.
                $relation = collect($relationships)->first(fn($r) => ($r['tableFirst'] ?? '') === $targetTable && ($r['secondField'] ?? '') === $column['name']);
                if (!$relation) {
                    continue;
                }
                $meta = $this->resolveTargetTableMeta($targetTable);
                $displayField = $this->guessDisplayField($meta['fields']);
                $key = $relation['key'] . '.' . $displayField;
                $label = Str::title(str_replace('_id', '', $column['name']));
            } else {
                $key = $table . '.' . $column['name'];
                $label = $column['name'];
            }

            if (in_array($key, $existingKeys)) {
                continue;
            }
            $browseColumns[] = [
                'key' => $key,
                'label' => $label,
                'sortable' => !$targetTable,
                'exportable' => true,
                'filterable' => true,
                'searchable' => !$targetTable
            ];
            $existingKeys[] = $key;
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

        $formColumns = $this->form['formDesignList'] ?? [];
        $existingKeys = [];
        foreach ($formColumns as $row) {
            foreach ($row as $field) {
                $existingKeys[] = $field['key'] ?? null;
            }
        }

        foreach ($columns as $column) {
            $key = $table . '.' . $column['name'];
            if (in_array($key, $existingKeys)) {
                continue;
            }
            $label = Str::title(str_replace('_',' ',$column['name']));
            $type = $this->getFormType($column['type']);
            $field = [
                'key' => $key,
                'type' => $type,
                'label' => $label,
                'helpText' => 'Input the ' . $label . ' here',
                'showCreate' => true,
                'showEdit' => true,
                'showDetail' => true
            ];

            $targetTable = $column['config']['relation']['table'] ?? null;
            if ($targetTable) {
                $field = $this->buildRelationSelectField($targetTable, $field);
            }

            $formColumns[] = [$field];
        }
        $this->form['formDesignList'] = $formColumns;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
    }

    /**
     * Turn a plain form field into a "select from related table" field. The target model
     * class is resolved from the module already registered for that table if there is one
     * (accurate even before that module is built), otherwise guessed from the table name
     * using this wizard's own naming convention. If the target module hasn't been built yet,
     * the select won't have data to query until it is — same as wiring it up manually.
     */
    private function buildRelationSelectField($targetTable, array $field): array
    {
        $meta = $this->resolveTargetTableMeta($targetTable);
        $modelClass = 'App\\Cb\\Modules\\' . Str::studly($meta['name']) . '\\Models\\' . Str::studly($targetTable);
        $displayField = $this->guessDisplayField($meta['fields']);

        $field['type'] = 'select';
        $field['helpText'] = 'Select the related ' . Str::title(str_replace('_', ' ', $targetTable)) . ' here';
        $field['options'] = [
            'model' => [
                'enable' => true,
                'ModelName' => $modelClass,
                'Key' => $meta['primaryKey'],
                'Label' => $displayField,
            ],
        ];
        return $field;
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
