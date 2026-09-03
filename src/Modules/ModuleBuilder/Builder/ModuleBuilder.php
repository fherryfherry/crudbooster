<?php

namespace CrudBooster\Modules\ModuleBuilder\Builder;

use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Modules\Menu\Services\CBMenuService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ModuleBuilder
{
    private array $importClass = [];

    public function __construct(public array $form = [])
    {
    }

    public function validate(): ?string
    {
        // should table already selected
        if (empty($this->form['table'])) {
            return false;
        }
        // should module name, path, class, model already filled
        if (empty($this->form['name']) || empty($this->form['path']) || empty($this->form['model']) || empty($this->form['service'])) {
            return false;
        }

        // should browse columns already filled
        if (empty($this->form['browse_columns'])) {
            return false;
        }

        // should form design already filled
        if (empty($this->form['formDesignList'])) {
            return false;
        }

        return true;
    }

    /**
     * Build module
     * @return void
     */
    public function build()
    {
        if (config('cb.demo_mode')) {
            return;
        }

        // Prevent module builder from being accessed in production
        if (config('app.env') === 'production') {
            return;
        }
        // remove module directory
        $this->remove();
        // prepare directory
        $this->prepareDirectory();
        // create service provider
        $this->createServiceProvider();
        // create router
        $this->createRouter();
        // create livewire module
        $this->createLivewireModule();
        // create livewire module form
        $this->createLivewireModuleForm();
        // create migration
        $this->createMigrationV2();
        // create model
        $this->createModel();
        // create service
        $this->createService();
        // create a menu
        $this->createMenu();
        // dump auto load
        $this->composerDump();
        // Clear route cache
        Artisan::call('route:clear');
    }

    public function composerDump()
    {
        if (function_exists('shell_exec')) {
            shell_exec("composer dumpautoload");
        }
    }

    public function createMenu()
    {
        CBMenuService::createIfNotExists([
            'icon' => 'BAR',
            'parent_id' => null,
            'name' => $this->form['name'],
            'menu_type' => 'MODULE',
            'menu_value' => $this->form['path'],
            'menu_order' => 99,
            'is_dashboard' => 0,
        ]);
    }

    /**
     * Remove module directory
     * @return void
     */
    public function remove(): void
    {
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        if (is_dir($modulePath)) {
            $filesystem = new Filesystem();
            $filesystem->deleteDirectory($modulePath);
        }
    }

    /**
     * Prepare directory
     * @return void
     */
    public function prepareDirectory(): void
    {
        // Create directory on app/Cb/Modules
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        if (!is_dir($modulePath)) {
            mkdir($modulePath, 0755, true);
        }
        // Create Database/migration
        $migrationPath = $modulePath . '/Database/migrations';
        if (!is_dir($migrationPath)) {
            mkdir($migrationPath, 0755, true);
        }
        // Create directory on app/Cb/Modules/{ModuleName}/Models
        $modelPath = $modulePath . '/Models';
        if (!is_dir($modelPath)) {
            mkdir($modelPath, 0755, true);
        }
        // Create directory on app/Cb/Modules/{ModuleName}/Services
        $servicePath = $modulePath . '/Services';
        if (!is_dir($servicePath)) {
            mkdir($servicePath, 0755, true);
        }
        // Create directory Livewire on app/Cb/Modules/{ModuleName}/Livewire
        $livewirePath = $modulePath . '/Livewire';
        if (!is_dir($livewirePath)) {
            mkdir($livewirePath, 0755, true);
        }
    }

    /**
     * Create Service Provider
     * @return void
     */
    public function createServiceProvider(): void
    {
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $serviceProviderPath = $modulePath . '/' . Str::studly($this->form['name']) . 'ServiceProvider.php';
        $serviceProviderContent = file_get_contents(__DIR__ . '/stubs/service_provider.stub');
        // Replace moduleClass
        $serviceProviderContent = str_replace('{moduleClass}', Str::studly($this->form['name']), $serviceProviderContent);
        // Replace alias
        $serviceProviderContent = str_replace('{namespace}', 'App\Cb\Modules\\' . Str::studly($this->form['name']), $serviceProviderContent);
        $serviceProviderContent = str_replace('{className}', Str::studly($this->form['name']) . 'ServiceProvider', $serviceProviderContent);
        // replace alias {name}
        $serviceProviderContent = str_replace('{name}', $this->form['name'], $serviceProviderContent);
        // replace alias {key}
        $serviceProviderContent = str_replace('{key}', $this->form['path'], $serviceProviderContent);
        // replace alias {permissionAvailable}
        $permissionAvailable = '';
        if (isset($this->form['permission_create']) && $this->form['permission_create'] === true) {
            $permissionAvailable .= 'RolePermission::CREATE, ';
        }
        if (isset($this->form['permission_read']) && $this->form['permission_read'] === true) {
            $permissionAvailable .= 'RolePermission::READ, ';
        }
        if (isset($this->form['permission_update']) && $this->form['permission_update'] === true) {
            $permissionAvailable .= 'RolePermission::UPDATE, ';
        }
        if (isset($this->form['permission_delete']) && $this->form['permission_delete'] === true) {
            $permissionAvailable .= 'RolePermission::DELETE, ';
        }
        $serviceProviderContent = str_replace('{permissionAvailable}', $permissionAvailable, $serviceProviderContent);
        file_put_contents($serviceProviderPath, $serviceProviderContent);
    }

    /**
     * Create Router
     * @return void
     */
    public function createRouter(): void
    {
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $routerPath = $modulePath . '/router.php';
        $routerContent = file_get_contents(__DIR__ . '/stubs/router.stub');
        // replace {namespace}
        $routerContent = str_replace('{namespace}', 'App\Cb\Modules\\' . Str::studly($this->form['name']), $routerContent);
        // replace {className}
        $routerContent = str_replace('{className}', Str::studly($this->form['name']), $routerContent);
        // replace {path}
        $routerContent = str_replace('{path}', $this->form['path'], $routerContent);
        file_put_contents($routerPath, $routerContent);
    }

    private function getPrimaryKey(string $table): string
    {
        $primaryKey = Schema::getColumnListing($table);
        return $primaryKey ? $primaryKey[0] : 'id';
    }

    /**
     * Create Model
     * @return void
     */
    public function createModel(): void
    {
        $tableCamelCase = Str::studly($this->form['table_name'] ?? $this->form['table']);
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $modelPath = $modulePath . '/Models/' . $tableCamelCase . '.php';
        $modelContent = file_get_contents(__DIR__ . '/stubs/model.stub');
        // replace {namespace}
        $modelContent = str_replace('{namespace}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\Models', $modelContent);
        // replace {className}
        $modelContent = str_replace('{className}', $tableCamelCase, $modelContent);
        // replace {table}
        $modelContent = str_replace('{table}', $this->form['table_name'] ?? $this->form['table'], $modelContent);
        // replace {primaryKey}
        $primaryKey = $this->form['primaryKey'] ?? $this->getPrimaryKey($this->form['table_name'] ?? $this->form['table']);
        $modelContent = str_replace('{primaryKey}', $primaryKey, $modelContent);

        // get type of primary key
        $primaryKeyType = collect($this->form['schema'])->firstWhere('name', $primaryKey)['type'] ?? 'integer';
        $primaryKeyIsUuid = $this->form['primaryKeyIsUuid'] ?? false;
        if ($primaryKeyType == 'uuid' || $primaryKeyIsUuid) {
            $modelContent = str_replace(['{hasUuid}', '{import_hasUuid}'], ['use HasUuids;', 'use Illuminate\Database\Eloquent\Concerns\HasUuids;'], $modelContent);
        } else {
            $modelContent = str_replace(['{hasUuid}', '{import_hasUuid}'], '', $modelContent);
        }

        // get has soft delete
        $isSoftDelete = collect($this->form['schema'])->where('name', 'deleted_at')->count();
        if ($isSoftDelete) {
            $modelContent = str_replace(['{hasSoftDelete}', '{import_hasSoftDelete}'], ['use SoftDeletes;', 'use Illuminate\Database\Eloquent\SoftDeletes;'], $modelContent);
        } else {
            $modelContent = str_replace(['{hasSoftDelete}', '{import_hasSoftDelete}'], '', $modelContent);
        }

        // set fillable
        $fillable = collect($this->form['schema'])->map(function ($item) {
            return "'{$item['name']}'";
        })->toArray();
        if ($fillable) {
            $modelContent = str_replace('{fillable}', "[" . implode(",", $fillable) . "]", $modelContent);
        } else {
            $modelContent = str_replace('{fillable}', "['*']", $modelContent);
        }

        file_put_contents($modelPath, $modelContent);
    }

    /**
     * Create Service
     * @return void
     */
    public function createService(): void
    {
        $tableCamelCase = Str::studly($this->form['table_name'] ?? $this->form['table']);
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $servicePath = $modulePath . '/Services/' . $tableCamelCase . 'Service.php';
        $serviceContent = file_get_contents(__DIR__ . '/stubs/service.stub');
        // replace {namespace}
        $serviceContent = str_replace('{namespace}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\Services', $serviceContent);
        // replace {className}
        $serviceContent = str_replace('{className}', $tableCamelCase . 'Service', $serviceContent);
        // replace {modelClass}
        $serviceContent = str_replace('{modelClass}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\Models\\' . $tableCamelCase, $serviceContent);
        // replace {modelName}
        $serviceContent = str_replace('{modelName}', $tableCamelCase, $serviceContent);
        file_put_contents($servicePath, $serviceContent);
    }

    /**
     * Create Livewire Module
     * @return void
     */
    public function createLivewireModule(): void
    {
        $tableCamelCase = Str::studly($this->form['table_name'] ?? $this->form['table']);
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $livewirePath = $modulePath . '/Livewire/' . Str::studly($this->form['name']) . '.php';
        $livewireContent = file_get_contents(__DIR__ . '/stubs/livewire_module.stub');
        // replace {namespace}
        $livewireContent = str_replace('{namespace}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\Livewire', $livewireContent);
        // replace {className}
        $livewireContent = str_replace('{className}', Str::studly($this->form['name']), $livewireContent);
        // replace {serviceClassName}
        $livewireContent = str_replace('{serviceClassName}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\\Services\\' . $tableCamelCase . 'Service', $livewireContent);
        // replace {modelClassName}
        $livewireContent = str_replace('{modelClassName}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\\Models\\' . $tableCamelCase . " as {$tableCamelCase}Model", $livewireContent);
        // replace {serviceName}
        $livewireContent = str_replace('{serviceName}', $tableCamelCase . 'Service', $livewireContent);
        // replace {modelName}
        $livewireContent = str_replace('{modelName}', $tableCamelCase . "Model", $livewireContent);
        // replace {name}
        $livewireContent = str_replace('{name}', $this->form['name'], $livewireContent);
        // replace {columns}
        $livewireContent = str_replace('{columns}', $this->generateColumns($this->form['browse_columns']), $livewireContent);
        // replace buttons
        $livewireContent = str_replace('{buttonSearch}', $this->form['button_search_bar'] ?? false ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonCreate}', $this->form['button_create'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonFilter}', $this->form['button_filter'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonImport}', $this->form['button_import'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonExportXls}', $this->form['button_export_xls'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonExportCsv}', $this->form['button_export_csv'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonExportPdf}', $this->form['button_export_pdf'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonBulkAction}', $this->form['button_bulk_action'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonActionStyle}', $this->form['button_action_style'] ?? 'ICON_ONLY', $livewireContent);
        $livewireContent = str_replace('{buttonEdit}', $this->form['button_edit'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonDelete}', $this->form['button_delete'] ?? null ? 'true' : 'false', $livewireContent);
        $livewireContent = str_replace('{buttonDetail}', $this->form['button_detail'] ?? null ? 'true' : 'false', $livewireContent);

        // Replace relationship
        if (isset($this->form['relationships']) && count($this->form['relationships']) > 0) {
            $relationship = $this->generateRelationshipQuery();
            $livewireContent = str_replace('{relationship}', $relationship, $livewireContent);
        } else {
            $livewireContent = str_replace('{relationship}', '', $livewireContent);
        }

        // Replace Hook Query
        $hookQuery = $this->generateHookQuery();
        $livewireContent = str_replace('{hookQuery}', $hookQuery, $livewireContent);

        // replace bulk action
        $bulkAction = $this->generateBulkAction();
        $livewireContent = str_replace('{bulkAction}', $bulkAction, $livewireContent);

        // replace rowActionButton
        $rowActionButton = $this->generateRowActionButton();
        $livewireContent = str_replace('{rowActionButton}', $rowActionButton, $livewireContent);

        // Replace template all
        file_put_contents($livewirePath, $livewireContent);

        // clean tidy too much new line
        $this->cleanTidyTooMuchNewLine($livewirePath);
    }

    /**
     * Generate row action button
     * @return string
     */
    private function generateRowActionButton(): string
    {
        $rowActionButton = '';
        if (isset($this->form['actionButtonList']) && count($this->form['actionButtonList']) > 0 && $this->form['actionButtonStatus']) {
            foreach ($this->form['actionButtonList'] as $action) {
                //$this->addActionButton('Edit', 'category/action/{id}', Icon::EDIT);
                //['url'=>'/category/your-action/{id}','icon'=>'BOLT','class'=>'btn btn-primary','label'=>'Tester','target'=>'_self','confirm'=>false,'permission'=>'Read','templateMode'=>'ICON_ONLY']
                $action['confirm'] = (bool)$action['confirm'];
                $label = $action['label'] ?? '';
                $urlAction = $action['url'] ?? null;
                $icon = $action['icon'] ?? 'BOLT';
                $rowActionButton .= "\$this->addActionButton(\"{$label}\", '{$urlAction}')";
                if ($icon) {
                    $icon = strtoupper($icon);
                    $rowActionButton .= "->icon(\CrudBooster\Components\Icon\Icon::{$icon})";
                }
                if ($action['confirm']) {
                    $rowActionButton .= "->confirmation()";
                }
                if (isset($action['class']) && $action['class']) {
                    $rowActionButton .= "->buttonClass('{$action['class']}')";
                }
                if (isset($action['permission']) && $action['permission']) {
                    $permission = "\CrudBooster\Modules\Role\Enum\RolePermission::" . strtoupper($action['permission']);
                    $rowActionButton .= "->permission({$permission})";
                }
                $rowActionButton .= ";\n";
            }
        }
        return trim($rowActionButton);
    }

    /**
     * Generate Bulk Action
     * @return string
     */
    private function generateBulkAction(): string
    {
        $bulkAction = '';
        if (isset($this->form['bulkActionList']) && count($this->form['bulkActionList']) > 0 && $this->form['bulkActionStatus']) {
            foreach ($this->form['bulkActionList'] as $action) {
                $confirmMessage = $action['confirmation']['message'] ?? '';
                $confirmTitle = $action['confirmation']['title'] ?? '';

                // Pre-defined action
                switch ($action['action']) {
                    case 'DELETE_ALL':
                        $actionFn = "fn(\$ids) => \$this->modelService::deleteByIds(\$ids)";
                        break;
                    default:
                        $actionFn = "fn(\$ids) => null";
                        break;
                }

                $bulkAction .= "\$this->addBulkAction('{$action['label']}', '{$action['icon']}', {$actionFn}, \"{$confirmTitle}\", \"{$confirmMessage}\");\n";
            }
        }
        return trim($bulkAction);
    }

    /**
     * Generate Hook Query
     * @return string
     */
    private function generateHookQuery(): string
    {
        if (isset($this->form['hookQueryList']) && count($this->form['hookQueryList']) > 0) {
            $hookQuery = "\$this->hookQuery(function(Builder \$query) {\n";
            $groupStarted = false;
            foreach ($this->form['hookQueryList'] as $column) {
                // if empty then skip
                if (empty($column['field']) || empty($column['operator']) || empty($column['value'])) {
                    continue;
                }
                if ($column['group'] === true && !$groupStarted) {
                    $groupStarted = true;
                    $hookQuery .= "             \$query->where(function(\$query) {\n";
                }
                if ($column['group'] === false && $groupStarted) {
                    $groupStarted = false;
                    $hookQuery .= "             });\n";
                }
                if ($column['type'] == 'AND') {
                    $hookQuery .= "                 \$query->where('{$column['field']}', '{$column['operator']}', '{$column['value']}')";
                } else {
                    $hookQuery .= "                 \$query->orWhere('{$column['field']}', '{$column['operator']}', '{$column['value']}')";
                }
                $hookQuery .= ";\n";
            }
            $hookQuery .= "        });\n";
            return trim($hookQuery);
        }
        return '';
    }

    /**
     * Generate Relationship Query
     * @return string
     */
    private function generateRelationshipQuery(): string
    {
        $relationship = "\$this->hookQuery(function(Builder \$query) {\n";
        foreach ($this->form['relationships'] as $column) {
            $relationship .= "            \$query->join('{$column['tableFirst']} as {$column['key']}', '{$column['key']}.{$column['firstField']}', '{$column['operator']}', '{$column['tableSecond']}.{$column['secondField']}', '{$column['type']}');\n";
            $availableTableFirstColumns = collect(Schema::getColumnListing($column['tableFirst']))
                ->map(function ($item) use ($column) {
                    return $column['key'] . '.' . $item;
                })->filter(function ($item) {
                    return collect($this->form['browse_columns'])->contains('key', $item);
                })->toArray();
            foreach ($availableTableFirstColumns as $tableFirstColumn) {
                $relationship .= "            \$query->addSelect('{$tableFirstColumn} as {$tableFirstColumn}');\n";
            }
        }
        $relationship .= "        });\n";
        return trim($relationship);
    }

    /**
     * Generate Columns
     * @param array $browseColumns
     * @return string
     */
    private function generateColumns(array $browseColumns): string
    {
        $columns = '';
        foreach ($browseColumns as $column) {
            $key = $column['key'] ?? '';
            $searchable = $column['searchable'] ?? false;
            $filterable = $column['filterable'] ?? false;
            $exportable = $column['exportable'] ?? false;
            $sortable = $column['sortable'] ?? false;
            $columns .= "Column::add(label: '{$column['label']}', key: '{$key}'";
            $columns .= !$searchable ? ", searchable: false" : "";
            $columns .= !$filterable ? ", filterable: false" : "";
            $columns .= !$exportable ? ", exportable: false" : "";
            $columns .= !$sortable ? ", sortable: false" : "";
            $columns .= ")";
            $columns = $this->additionalConfigBrowseHandle($column, $columns);
            $columns .= ",\n            ";
        }
        return $columns;
    }

    /**
     * Additional Config Browse Handle
     * @param $column
     * @param $columns
     * @return mixed|string
     */
    private function additionalConfigBrowseHandle($column, $columns)
    {
        if (isset($column['config']['transformDateFormat']) && $column['config']['transformDateFormat']) {
            $columns .= "->transform(fn(\$value) => date('{$column['config']['dateFormat']}', strtotime(\$value)))";
        }
        if (isset($column['config']['transformTemplate']) && $column['config']['transformTemplate']) {
            $letterCase = $column['config']['letterCase'] ?? '';
            switch ($letterCase) {
                case 'capitalize':
                    $value = "ucwords(\$value)";
                    break;
                case 'uppercase':
                    $value = "strtoupper(\$value)";
                    break;
                case 'lowercase':
                    $value = "strtolower(\$value)";
                    break;
                default:
                    $value = "\$value";
                    break;
            }
            $columns .= "->transform(fn(\$value) => sprintf('%s%s%s', \"{$column['config']['prefix']}\", {$value}, \"{$column['config']['suffix']}\") )";
        }
        if (isset($column['config']['transformImage']) && $column['config']['transformImage']) {
            $imageWidth = $column['config']['imageWidth'] ?? 36;
            $imageHeight = $column['config']['imageHeight'] ?? 36;
            $style = $column['config']['style'] ?? 'rounded';
            $columns .= "->image(['width' => \"{$imageWidth}\", 'height' => \"{$imageHeight}\", 'style' => \"{$style}\"])";
        }
        if (isset($column['config']['transformNumberFormat']) && $column['config']['transformNumberFormat']) {
            $decimal = $column['config']['decimal'] ?? 2;
            $thousand = $column['config']['thousand'] ?? ',';
            $decimalPoint = $column['config']['decimalPoint'] ?? '.';
            $columns .= "->transform(fn(\$value) => number_format(\$value, {$decimal}, '{$decimalPoint}', '{$thousand}'))";
        }
        if (isset($column['config']['transformLink']) && $column['config']['transformLink']) {
            $url = $column['config']['url'] ?? '';
            $target = $column['config']['target'] ?? '';
            $columns .= "->transform(fn(\$value) => sprintf('<a href=\"%s\" target=\"%s\">%s</a>', \"{$url}\", \"{$target}\", \$value))";
        }
        return $columns;
    }

    /**
     * Create Livewire Module Form
     * @return void
     */
    public function createLivewireModuleForm()
    {
        $tableCamelCase = Str::studly($this->form['table_name'] ?? $this->form['table']);
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $livewirePath = $modulePath . '/Livewire/' . Str::studly($this->form['name']) . 'Form.php';
        $livewireContent = file_get_contents(__DIR__ . '/stubs/livewire_module_form.stub');
        // replace {namespace}
        $livewireContent = str_replace('{namespace}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\Livewire', $livewireContent);
        // replace {className}
        $livewireContent = str_replace('{className}', Str::studly($this->form['name']) . 'Form', $livewireContent);
        // replace {serviceClassName}
        $livewireContent = str_replace('{serviceClassName}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\\Services\\' . $tableCamelCase . 'Service', $livewireContent);
        // replace {modelClassName}
        $livewireContent = str_replace('{modelClassName}', 'App\Cb\Modules\\' . Str::studly($this->form['name']) . '\\Models\\' . $tableCamelCase . " as {$tableCamelCase}Model", $livewireContent);
        // replace {serviceName}
        $livewireContent = str_replace('{serviceName}', $tableCamelCase . 'Service', $livewireContent);
        // replace {modelName}
        $livewireContent = str_replace('{modelName}', $tableCamelCase . "Model", $livewireContent);
        // replace {name}
        $livewireContent = str_replace('{name}', $this->form['name'], $livewireContent);
        // replace {fields}
        $fields = '';
        foreach ($this->form['formDesignList'] as $column) {
            if (is_array($column)) {
                if (count($column) == 1) {
                    $column = $column[0];
                    if (isset($column['label'])) {
                        $fields = $this->constructForm($column, $fields);
                    }
                } else {
                    $fields .= "[\n";
                    foreach ($column as $field) {
                        if (isset($field['label'])) {
                            $fields = $this->constructForm($field, $fields);
                        } else {
                            $fields .= "Form::empty(),\n            ";
                        }
                    }
                    $fields .= "],\n            ";
                }
            }
        }
        $livewireContent = str_replace('{fields}', $fields, $livewireContent);
        // Replace importClass
        $livewireContent = str_replace('{importClass}', implode("\n", array_unique($this->importClass)), $livewireContent);
        // replace {onFormSaved}
        if (isset($this->form['formHook']) && isset($this->form['formHookStatus']) && $this->form['formHookStatus']) {
            $formSaving = $this->form['formHook']['onFormSaving'] ?? '';
            $formSaved = $this->form['formHook']['onFormSaved'] ?? '';
            $livewireContent = str_replace('{onFormSaving}', $formSaving, $livewireContent);
            $livewireContent = str_replace('{onFormSaved}', $formSaved, $livewireContent);
        } else {
            $livewireContent = str_replace('{onFormSaving}', '', $livewireContent);
            $livewireContent = str_replace('{onFormSaved}', '', $livewireContent);
        }

        file_put_contents($livewirePath, $livewireContent);

        // clean tidy too much new line
        $this->cleanTidyTooMuchNewLine($livewirePath);
    }

    public function getExistingFields($tableName)
    {
        $columns = Schema::getColumnListing($tableName);
        $existingFields = [];
        foreach ($columns as $column) {
            $columnDetails = Schema::getConnection()->getSchemaBuilder()->getColumns($tableName);
            foreach ($columnDetails as $details) {
                if ($details['name'] === $column) {
                    $existingFields[] = [
                        'name' => $details['name'],
                        'type' => convertSqlTypeToLaravelType($details['type_name']),
                        'config' => [
                            'autoIncrement' => $details['auto_increment'] ?? false,
                        ],
                    ];
                    break;
                }
            }
        }
        return $existingFields;
    }

    public function createMigrationV2()
    {
        $tableName = $this->form['table_name'] ?? $this->form['table'];
        $schema = $this->form['schema'];
        $primaryKey = $this->form['primaryKey'] ?? 'id';
        $isTableExist = Schema::hasTable($tableName);
        $createOrUpdate = $isTableExist ? 'update' : 'create';
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $migrationFilePath = $modulePath . '/Database/migrations/' . date('Y_m_d_His') . '_' . $createOrUpdate . '_' . $tableName . '_table.php';

        $anyChanges = false;

        $migrationContent = "<?php\n\n";
        $migrationContent .= "use Illuminate\Database\Migrations\Migration;\n";
        $migrationContent .= "use Illuminate\Database\Schema\Blueprint;\n";
        $migrationContent .= "use Illuminate\Support\Facades\Schema;\n\n";
        $migrationContent .= "return new class extends Migration\n";
        $migrationContent .= "{\n";
        $migrationContent .= "    public function up()\n";
        $migrationContent .= "    {\n";


        // Check schema if not have primary key then add one
        $hasPrimaryKey = collect($schema)->firstWhere('name', $primaryKey);
        if (!$hasPrimaryKey) {
            $schema[] = [
                'name' => $primaryKey,
                'type' => 'uuid',
                'config' => [],
            ];
        }

        if (!Schema::hasTable($tableName)) {
            $migrationContent .= "        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
            foreach ($schema as $newField) {
                $fieldType = $newField['type'];
                $fieldType = $fieldType == 'uuid' ? 'char' : $fieldType;
                $length = $fieldType == 'uuid' ? 36 : '';
                $flagPrimaryKey = $newField['name'] === $primaryKey ? '->primary()' : '';
                $nullable = !$flagPrimaryKey ? '->nullable()' : '';
                $autoIncrement = $newField['config']['autoIncrement'] ?? false ? '->autoIncrement()' : '';
                $migrationContent .= "            \$table->{$fieldType}('{$newField['name']}'{$length}){$flagPrimaryKey}{$nullable}{$autoIncrement};\n";
            }
            $migrationContent .= "        });\n";
            $anyChanges = true;
        } else {
            $existingFields = $this->getExistingFields($tableName);
            $migrationContent .= "        Schema::table('{$tableName}', function (Blueprint \$table) {\n";

            foreach ($schema as $newField) {
                $existingField = collect($existingFields)->firstWhere('name', $newField['name']);

                if (!$existingField) {
                    $fieldType = $newField['type'];
                    $fieldType = $fieldType == 'uuid' ? 'char' : $fieldType;
                    $length = $fieldType == 'uuid' ? 36 : '';
                    $flagPrimaryKey = $newField['name'] === $primaryKey ? '->primary()' : '';
                    $nullable = !$flagPrimaryKey ? '->nullable()' : '';
                    $autoIncrement = isset($newField['config']['autoIncrement']) && $newField['config']['autoIncrement'] ? '->autoIncrement()' : '';
                    $migrationContent .= "            \$table->{$fieldType}('{$newField['name']}'{$length})" . trim("{$flagPrimaryKey}{$nullable}{$autoIncrement}") . ";\n";
                    $anyChanges = true;
                } else {
                    $existingType = $existingField['type'];
                    $newTypeSimilar = convertSqlTypeToLaravelType(convertMigrationTypeToSql($newField['type']));
                    $newType = $newField['type'];

                    if ($existingType !== $newTypeSimilar) {
                        $newType = $newType == 'uuid' ? 'char' : $newType;
                        $length = $newType == 'uuid' ? 36 : '';
                        $migrationContent .= "            \$table->{$newType}('{$newField['name']}'{$length})";
                        $flagPrimaryKey = $newField['name'] === $primaryKey ? '->primary()' : '';
                        $nullable = !$flagPrimaryKey ? '->nullable()' : '';
                        $autoIncrement = isset($newField['config']['autoIncrement']) && $newField['config']['autoIncrement'] ? '->autoIncrement()' : '';
                        $migrationContent .= trim("{$flagPrimaryKey}{$nullable}{$autoIncrement}->change()") . ";\n";
                        $anyChanges = true;
                    }
                }
            }

            $migrationContent .= "        });\n";
        }

        $migrationContent .= "    }\n\n";
        $migrationContent .= "    public function down()\n";
        $migrationContent .= "    {\n";
        $migrationContent .= "        Schema::dropIfExists('{$tableName}');\n";
        $migrationContent .= "    }\n";
        $migrationContent .= "};\n";

        if ($anyChanges) {
            file_put_contents($migrationFilePath, $migrationContent);
            $this->runMigration($migrationFilePath);
        }
    }

    /**
     * Create Migration
     * @return void
     */
    public function createMigration()
    {
        $typeWithLength = ['char', 'string', 'binary', 'ulid', 'foreignUlid'];
        $skipField = ['id', 'created_at', 'updated_at', 'deleted_at'];

        $tableName = $this->form['table_name'] ?? $this->form['table'];
        $isTableExist = Schema::hasTable($tableName);
        $modulePath = app_path('Cb/Modules/' . Str::studly($this->form['name']));
        $migrationPath = $modulePath . '/Database/migrations/' . date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
        $migrationContent = file_get_contents(__DIR__ . '/stubs/migration.stub');
        // replace {table}
        $migrationContent = str_replace('{table}', $tableName, $migrationContent);
        // replace {fields}
        $fields = '';
        $fieldsChange = '';
        foreach ($this->form['schema'] as $column) {
            $type = strtolower($column['type']);
            // skip fields
            if (in_array($column['name'], $skipField)) {
                continue;
            }

            $fields .= "\$table->{$type}('{$column['name']}'";
            $fieldsChange .= "\$table->{$type}('{$column['name']}'";
            if (in_array($type, $typeWithLength) && isset($column['config']) && isset($column['config']['length'])) {
                $fields .= ",{$column['config']['length']})";
                $fieldsChange .= ",{$column['config']['length']})";
            } else {
                $fields .= ')';
                $fieldsChange .= ')';
            }

            if ($column['config']['nullable'] ?? false) {
                $fields .= '->nullable()';
                $fieldsChange .= '->nullable()';
            }
            if ($column['config']['default'] ?? false) {
                $fields .= "->default('{$column['config']['default']}')";
                $fieldsChange .= "->default('{$column['config']['default']}')";
            }

            if ($column['config']['unique'] ?? false) {
                $fields .= '->unique()';
            }

            if ($isTableExist && Schema::hasColumn($tableName, $column['name'])) {
                $fieldsChange .= '->change()';
            }

            $fields .= ';';
            $fields .= "\n            ";
            $fieldsChange .= ';';
            $fieldsChange .= "\n            ";
        }

        // Compare fields with existing table, if newer is gone, remove it
        if ($isTableExist) {
            $dropExcept = ['id', 'created_at', 'updated_at', 'deleted_at'];
            $columns = Schema::getColumnListing($tableName);
            foreach ($columns as $column) {
                $found = false;
                foreach ($this->form['schema'] as $field) {
                    if ($field['name'] == $column) {
                        $found = true;
                        break;
                    }
                }
                if (!$found && !in_array($column, $dropExcept)) {
                    $fields .= "\$table->dropColumn('{$column}');";
                    $fields .= "\n            ";
                    $fieldsChange .= "\$table->dropColumn('{$column}');";
                    $fieldsChange .= "\n            ";
                }
            }
        }

        $migrationContent = str_replace(['{fields}', '{fieldsChange}'], [$fields, $fieldsChange], $migrationContent);
        file_put_contents($migrationPath, $migrationContent);

        // Run migration
        $this->runMigration($migrationPath);
    }

    /**
     * Run migration
     * @param $migrationPath
     * @return void
     */
    public function runMigration($migrationPath)
    {
        $migration = require $migrationPath;
        $migration->up();

        // insert to migration table
        $migrationName = pathinfo($migrationPath, PATHINFO_FILENAME);
        $migrationName = str_replace('.php', '', $migrationName);
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => 1,
        ]);
    }

    /**
     * Construct form
     * @param mixed $column
     * @param string $fields
     * @return string
     */
    private function constructForm(mixed $column, string $fields): string
    {
        $registrarTypes = CBTypeRegistrar::__getTypes($column['type'], 'clazz');
        $typeClassName = last(explode('\\', $registrarTypes));

        $this->importClass[$column['type']] = 'use CrudBooster\Components\Type\\' . $typeClassName . '\\Function\\' . $typeClassName . ';';

        $key = isset($column['key']) ? last(explode(".", $column['key'])) : "";
        $validation = $column['validation'] ?? '';
        $placeholder = $column['placeholder'] ?? '';
        $helpText = $column['helpText'] ?? '';
        $fields .= "Form::add(label: \"{$column['label']}\", key: \"{$key}\", type: \"{$column['type']}\", validation: \"{$validation}\", placeholder: \"{$placeholder}\", helpText: \"{$helpText}\")";

        if (isset($column['options'])) {
            $fields .= "->option({$typeClassName}::option()";
            foreach ($column['options'] as $key => $option) {
                if ($option['enable'] ?? false) {
                    unset($option['enable']);

                    // If contain option, then make it as option
                    if ($option && count($option)) {
                        if (count($option) == 1) {
                            $arrayString = '"' . $option[array_key_first($option)] . '"';
                        } else {
                            // convert each key to camelCase
                            foreach ($option as $k => $v) {
                                $option[Str::camel($k)] = $v;
                                unset($option[$k]);
                            }

                            // each key that contain callback, then make it as callback
                            $nonQuoteKey = [];
                            foreach ($option as $k => $v) {
                                if (str_contains($v, '{') && str_contains($k, 'Callback')) {
                                    $nonQuoteKey[] = $k;
                                }
                            }

                            $arrayString = min_var_export($option, $nonQuoteKey);
                        }
                        $fields .= "->{$key}(" . $arrayString . ")";
                    } else {
                        $fields .= "->{$key}()";
                    }
                }
            }
            $fields .= ")";
        }

        if ($column['default'] ?? false) {
            switch ($column['default']) {
                case str_contains($column['default'], '{session.'):
                    $fields .= "->default(session('" . str_replace(['{session.', '}'], '', $column['default']) . "'))";
                    break;
                case '{current_date}':
                    $fields .= "->default(now())";
                    break;
                case '{current_time}':
                    $fields .= "->default(now()->format('H:i:s'))";
                    break;
                case '{current_datetime}':
                    $fields .= "->default(now()->format('Y-m-d H:i:s'))";
                    break;
                case '{random_string}':
                    $fields .= "->default(\Illuminate\Support\Str::random(10))";
                    break;
                case '{random_number}':
                    $fields .= "->default(rand(1000, 9999))";
                    break;
                case '{uuid}':
                    $fields .= "->default(\Illuminate\Support\Str::uuid()->toString())";
                    break;
                case '{auth.id}':
                    $fields .= "->default(auth()->id())";
                    break;
                case '{auth.name}':
                    $fields .= "->default(auth()->user()->name)";
                    break;
                case '{auth.email}':
                    $fields .= "->default(auth()->user()->email)";
                    break;
                default:
                    $fields .= "->default('{$column['default']}')";
                    break;
            }
        }

        if ($column['readonly'] ?? false) {
            $fields .= "->readonly()";
        }

        if ($column['showDetail'] ?? false) {
            $fields .= "->showDetail(true)";
        } else {
            $fields .= "->showDetail(false)";
        }
        if ($column['showCreate'] ?? false) {
            $fields .= "->showCreate(true)";
        } else {
            $fields .= "->showCreate(false)";
        }
        if ($column['showEdit'] ?? false) {
            $fields .= "->showEdit(true)";
        } else {
            $fields .= "->showEdit(false)";
        }

        // trim ,\n
        $fields = "{$fields},\n            ";

        return $fields;
    }

    /**
     * @param string $filePath
     * @return void
     */
    private function cleanTidyTooMuchNewLine(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File does not exist: $filePath");
        }

        $content = file($filePath, FILE_IGNORE_NEW_LINES);
        $cleanedContent = [];
        $previousLineEmpty = false;

        foreach ($content as $line) {
            if (trim($line) === '') {
                if (!$previousLineEmpty) {
                    $cleanedContent[] = $line;
                    $previousLineEmpty = true;
                }
            } else {
                $cleanedContent[] = $line;
                $previousLineEmpty = false;
            }
        }

        file_put_contents($filePath, implode("\n", $cleanedContent));
    }
}
