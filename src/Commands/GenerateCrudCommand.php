<?php

namespace CrudBooster\Commands;

use CrudBooster\Helpers\Facades\SchemaUtil;
use CrudBooster\Modules\ModuleBuilder\Builder\ModuleBuilder;
use CrudBooster\Modules\ModuleBuilder\Builder\ModuleFormConstructor;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class GenerateCrudCommand extends Command
{
    protected $signature = 'cb:crud
                        {--all : Generate CRUD for all tables}
                        {table? : The name of the table}
                        {--uuid : Tell the CB to use UUID as primary key}
                        {--name= : The name of the CRUD}';
    protected $description = 'Generate CRUD from table';

    protected array $skipTables = ['migrations', 'password_reset_tokens', 'failed_jobs', 'users', 'jobs', 'job_batches', 'sessions', 'cache', 'cache_locks','cb_settings','cb_menus','cb_modules','cb_pages','cb_queries','cb_roles','cb_role_users'];

    public function handle()
    {
        $primaryKeyIsUuid = $this->option('uuid') ?? false;

        if ($this->option('all')) {
            $total = $this->generateAllTables();
            $this->info('Total '.$total.' CRUDs generated successfully');
            return;
        }

        $tableName = $this->argument('table');
        $name = $this->option('name') ?? $this->makeName($tableName);

        if ($tableName) {
            // check the table is exists
            if (!Schema::hasTable($tableName)) {
                $this->error('Table `' . $tableName . '` does not exist, please create the table first');
                return;
            }
            $this->generateSingleTable($tableName, $name, $primaryKeyIsUuid);
            $this->info('CRUD generated successfully');
            return;
        }

        $this->promptUserForTable();
        $this->info('CRUD generated successfully');
    }

    private function generateAllTables(): int
    {
        $tables = SchemaUtil::getTableListing();
        $primaryKeyIsUuid = $this->option('uuid') ?? false;
        $total = 0;
        foreach ($tables as $tableName) {
            if (!in_array($tableName, $this->skipTables)) {
                $this->generateSingleTable($tableName, $this->makeName($tableName), $primaryKeyIsUuid);
                $total++;
            }
        }
        return $total;
    }

    private function clearingRouteCache()
    {
        spin(
            callback: fn() => $this->callSilent('route:clear'),
            message: 'Clearing route cache...',
        );
    }

    private function generateSingleTable(string $tableName, ?string $name, bool $primaryKeyIsUuid = false): void
    {
        spin(
            callback: fn() => $this->generateCrud($tableName, $name, $primaryKeyIsUuid),
            message: 'Generating CRUD for ' . $tableName . '...',
        );

        $this->clearingRouteCache();
    }

    private function promptUserForTable(): void
    {
        $askAll = text(label:'Do you want to generate CRUD for all tables? (yes/no)', default:'no');
        if (strtolower($askAll) === 'yes') {
            $this->generateAllTables();
            return;
        }

        $askTable = text(label: 'Enter the table name', required: true, validate: 'string', hint: 'The name of the table. Table must be exists in the database');

        // check the table is exists
        if (!Schema::hasTable($askTable)) {
            $this->error('Table `' . $askTable . '` does not exist, please create the table first');
            return;
        }

        $defaultName = $this->makeName($askTable);
        $askName = text(label: 'Enter the name of the CRUD', default: $defaultName, required: true, validate: 'string|max:40', hint: 'The name of the module. Max characters: 40');

        $askUuid = text(label: 'Is the primary key is UUID? (yes/no)', default: 'no', required: true, validate: 'string', hint: 'If the primary key is UUID, CB will generate the UUID automatically');
        $primaryKeyIsUuid = strtolower($askUuid) === 'yes';

        $this->generateSingleTable($askTable, $askName, $primaryKeyIsUuid);
    }

    private function generateCrud(string $tableName, ?string $name = null, bool $primaryKeyIsUuid = false): void
    {
        $form = ModuleFormConstructor::create($tableName, $name, $primaryKeyIsUuid);
        $formData = $form->toArray();
        // remove cb_modules by name
        CbModule::query()->where('name', $formData['name'])->delete();
        // save to cb modules
        CbModule::query()->insert([
            'uuid' => Str::uuid()->toString(),
            'name'=> $formData['name'],
            'config' => json_encode($formData),
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        $build = new ModuleBuilder($formData);
        $build->build();
    }

    /**
     * @param bool|array|string|null $tableName
     * @return string
     */
    public function makeName(bool|array|string|null $tableName): string
    {
        return Str::singular(Str::title(Str::snake($tableName, ' ')));
    }
}
