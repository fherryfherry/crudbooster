<?php

namespace CrudBooster\Commands;

use CrudBooster\Helpers\Facades\SchemaUtil;
use CrudBooster\Modules\Menu\Models\CBMenu;
use CrudBooster\Modules\Menu\Services\CBMenuService;
use CrudBooster\Modules\Role\Services\CbRoleService;
use CrudBooster\Modules\Setting\Services\CbSettingService;
use CrudBooster\Modules\User\Models\User;
use CrudBooster\Modules\User\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'cb:install';
    protected $description = 'Install CRUDBooster';
    protected $totalSteps = 14;

    private $appName;
    private $email;
    private $password;

    private $setEnvCountDown = 3;
    private $forceInstall = false;
    private $freshTable = false;

    private $wantConfigDb;
    private $dbPlatform;
    private $dbDatabase;
    private $dbUsername;
    private $dbPassword;
    private $dbHost;
    private $dbPort;

    public function handle()
    {
        $this->clearConfigCache();

        \Laravel\Prompts\info("
 #####  ######  #     # ######  ######
#     # #     # #     # #     # #     #  ####   ####   ####  ##### ###### #####
#       #     # #     # #     # #     # #    # #    # #        #   #      #    #
#       ######  #     # #     # ######  #    # #    #  ####    #   #####  #    #
#       #   #   #     # #     # #     # #    # #    #      #   #   #      #####
#     # #    #  #     # #     # #     # #    # #    # #    #   #   #      #   #
 #####  #     #  #####  ######  ######   ####   ####   ####    #   ###### #    # ");
        \Laravel\Prompts\info('Welcome to CRUDBooster v7 installation. ^_^');

        $this->interviewBasicInfo();
        $this->interviewUserCredential();
        $this->interviewForceInstall();
        $this->interviewConfigDb();
        $this->runInstallation();
        $this->info('CRUDBooster installation finished.');
        $this->info('You may run development server first, `php artisan serve --port=8000`');
        $this->info('Here is your login information:');

        $this->table(['Credential', 'Value'], [
            ['Email', $this->email],
            ['Password', $this->password],
            ['Login URL', 'http://localhost:8000/cms/auth/login'],
        ]);

        $this->info('Useful links:');
        $this->table(['Page', 'Link'], [
            ['HomePage', 'https://github.com/fherryfherry/crudbooster'],
            ['Documentation', 'https://github.com/fherryfherry/crudbooster'],
            ['YouTube', 'https://www.youtube.com/@CRUDBoosterLaravel'],
            ['Author', 'Ferry Ariawan']
        ]);
    }

    private function interviewConfigDb()
    {
        $this->wantConfigDb = $this->choice('Do you want to configure the database?', ['Yes', 'No, I already config'], 0);
        if ($this->wantConfigDb === 'Yes') {
            $this->interviewDb();
        }
    }

    private function clearConfigCache()
    {
        $this->callSilent('config:clear');
        $this->callSilent('route:clear');
    }

    private function runInstallation()
    {
        $this->install();
    }

    private function interviewBasicInfo()
    {
        $this->appName = $this->ask('Enter your application or project name', 'My Awesome App');
    }

    private function interviewForceInstall()
    {
        $this->forceInstall = $this->choice('Do you want to force install?', ['Yes', 'No'], 1) === 'Yes';
        $this->freshTable = $this->choice('Do you want to refresh the database?', ['Yes', 'No'], 1) === 'Yes';
    }

    private function interviewUserCredential()
    {
        $this->name = $this->ask('What is your name?', 'John Doe');
        $this->email = $this->ask('Enter an email to login');

        if(!$this->email) {
            $this->warn('Email is required');
            $this->interviewUserCredential();
        }

        // validate valid email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->warn('Invalid email format. Please enter a valid email address.');
            $this->interviewUserCredential();
        }

        $this->password = $this->secret('Enter a new password');
    }

    private function interviewDb()
    {
        $this->dbPlatform = $this->choice('Select the database engine', ['mysql', 'sqlite', 'pgsql', 'sqlsrv'], 0);

        $defaultHost = '';
        $defaultPort = '';
        if ($this->dbPlatform === 'mysql' || $this->dbPlatform === 'mariadb') {
            $defaultHost = '127.0.0.1';
            $defaultPort = '3306';
        } elseif ($this->dbPlatform === 'pgsql') {
            $defaultHost = '127.0.0.1';
            $defaultPort = '5432';
        } elseif ($this->dbPlatform === 'sqlsrv') {
            $defaultHost = '127.0.0.1';
            $defaultPort = '1433';
        }

        $this->dbDatabase = $this->ask('Enter the database name', env('DB_DATABASE') ?? '');
        if ($this->dbPlatform != 'sqlite') {
            $this->dbUsername = $this->ask('Enter the database username', env('DB_USERNAME') ?? '');
            $this->dbPassword = $this->ask('Enter the database password', env('DB_PASSWORD') ?? '');
            $this->dbHost = $this->ask('Enter the database host', env('DB_HOST') ?? $defaultHost);
            $this->dbPort = $this->ask('Enter the database port', env('DB_PORT') ?? $defaultPort);
        }
    }

    private function install(): void
    {
        $this->settingEnv();
        usleep(500000); // 0.5 second sleep

        $this->refreshTable();
        usleep(500000); // 0.5 second sleep

        $this->forceInstall();
        usleep(500000); // 0.5 second sleep

        $this->checkUser ();
        usleep(500000); // 0.5 second sleep

        $this->checkWritableDirectory();
        usleep(500000); // 0.5 second sleep

        $this->createAppCbDir();
        usleep(500000); // 0.5 second sleep

        $this->moveStubs();
        usleep(500000); // 0.5 second sleep

        $this->runMigration();
        usleep(500000); // 0.5 second sleep

        $this->createInitialData();
        usleep(500000); // 0.5 second sleep

        $this->publishAsset();
        usleep(500000); // 0.5 second sleep

        $this->createFirstUser ();
        usleep(500000); // 0.5 second sleep

        $this->createDefaultRole();
        usleep(500000); // 0.5 second sleep

        $this->createMenu();
        usleep(500000); // 0.5 second sleep

        $this->additionalCallback();
        usleep(500000); // 0.5 second sleep

        $this->callSilent('optimize'); // Optimize the application

        // finish
        $this->info('Installation finished');
    }

    private function createInitialData()
    {
        $this->info('Creating initial data');
        CbSettingService::createOrUpdate('basic-info', [
            'app_name' => $this->appName
        ]);
        CbSettingService::createOrUpdate('appearance', [
            'login_welcome_text' => 'Welcome to ' . $this->appName,
            'login_welcome_sub_text' => 'Please login to continue',
            'login_footer_text' => 'Powered by CRUDBooster',
        ]);
    }

    private function additionalCallback(): void
    {
        $this->info('Execute additional callback');
        collect(CbInstallRegistrar::__getAll())->each(function ($item) {
            call_user_func($item['callback']);
        });
    }

    private function refreshTable()
    {
        // drop all tables
        if ($this->freshTable) {
            $this->info('Dropping all tables');
            // disable foreign key check
            Schema::disableForeignKeyConstraints();
            // get all tables
            $tables = SchemaUtil::getTableListing();
            // drop all tables
            foreach ($tables as $table) {
                Schema::dropIfExists($table);
            }
            // enable foreign key check
            Schema::enableForeignKeyConstraints();
        }
    }

    private function forceInstall()
    {
        // Check if argument force is set
        if ($this->forceInstall) {
            // Remove all app/cb directory
            $this->info('Removing App/Cb/ directory');
            File::deleteDirectory(app_path('Cb'));

            // Remove all assets
            $this->info("Removing assets...");
            $fileSystem = new Filesystem();
            $fileSystem->deleteDirectory(public_path('vendor/crudbooster'));

            // Remove all lang
            $this->info("Removing translation directory...");
            File::deleteDirectory(resource_path('lang/vendor/crudbooster'));

            // Remove config
            $this->info("Removing config file...");
            File::delete(config_path('cb.php'));
        }
    }

    private function createMenu()
    {
        $this->info('Creating default menu');
        $order = 0;

        CBMenu::create([
            'icon' => 'DASHBOARD',
            'menu_type' => 'MODULE',
            'menu_value' => 'dashboard',
            'name' => 'Dashboard',
            'is_dashboard' => true,
            'menu_order' => ++$order,
        ]);

        CBMenu::create([
            'icon' => 'USER',
            'menu_type' => 'MODULE',
            'menu_value' => 'user',
            'name' => 'User ',
            'is_dashboard' => false,
            'menu_order' => ++$order,
        ]);

        CBMenu::create([
            'icon' => 'KEY',
            'menu_type' => 'MODULE',
            'menu_value' => 'role',
            'name' => 'Role',
            'is_dashboard' => false,
            'tag' => 'Tools',
            'menu_order' => ++$order,
        ]);

        CBMenu::create([
            'icon' => 'BAR',
            'menu_type' => 'MODULE',
            'menu_value' => 'menu',
            'name' => 'Menu',
            'is_dashboard' => false,
            'tag' => 'Tools',
            'menu_order' => ++$order,
        ]);

        CBMenu::create([
            'icon' => 'CODE',
            'menu_type' => 'MODULE',
            'menu_value' => 'module-builder',
            'name' => 'Module Builder',
            'is_dashboard' => false,
            'tag' => 'Tools',
            'menu_order' => ++$order,
        ]);
        CBMenu::create([
            'icon' => 'DB',
            'menu_type' => 'MODULE',
            'menu_value' => 'query-builder',
            'name' => 'Query Builder',
            'is_dashboard' => false,
            'tag' => 'Tools',
            'menu_order' => ++$order,
        ]);

        CBMenuService::createIfNotExists([
            'icon' => 'BOLT',
            'menu_type' => 'MODULE',
            'menu_value' => 'api-builder',
            'name' => 'API Builder',
            'is_dashboard' => false,
            'tag' => 'Tools',
            'menu_order' => ++$order,
        ]);

        CBMenu::create([
            'icon' => 'MOUSE',
            'menu_type' => 'MODULE',
            'menu_value' => 'page-builder',
            'name' => 'Page Builder',
            'is_dashboard' => false,
            'tag' => 'Tools',
            'menu_order' => ++$order,
        ]);

        CBMenu::create([
            'icon' => 'COG',
            'menu_type' => 'MODULE',
            'menu_value' => 'setting',
            'name' => 'Setting',
            'is_dashboard' => false,
            'tag' => 'Tools',
            'menu_order' => ++$order,
        ]);
    }

    private function createAppCbDir()
    {
        $this->info('Creating App/Cb/ directory');
        if (!File::exists(app_path('Cb'))) {
            File::makeDirectory(app_path('Cb'), 0755, true);
            // Create Modules directory
            File::makeDirectory(app_path('Cb/Modules'), 0755, true);
        }
    }

    private function moveStubs()
    {
        $this->callSilent('config:clear');
        // Move all stubs to app/Cb directory and rename all files with suffix .stub to without .stub
        $this->info('Moving stubs');
        $fileSystem = new Filesystem();
        $stubs = $fileSystem->allFiles(__DIR__ . '/../Stubs');
        foreach ($stubs as $stub) {
            // check if its migration file then move to database/migrations
            if (str_contains($stub->getRelativePathname(), 'migration')) {
                $newPath = database_path('migrations/' . str_replace('.stub', '', $stub->getFilename()));
            } else {
                // check if its seeder file then move to database/seeders
                if (str_contains($stub->getRelativePathname(), 'seeder')) {
                    $newPath = database_path('seeders/' . str_replace('.stub', '', $stub->getFilename()));
                } else {
                    $newPath = app_path('Cb/' . str_replace('.stub', '', $stub->getRelativePathname()));
                }
            }
            $fileSystem->ensureDirectoryExists(dirname($newPath));
            $fileSystem->copy($stub->getPathname(), $newPath);
        }
    }

    private function settingEnv()
    {
        if ($this->setEnvCountDown == 0) {
            $this->warn("Reach maximum setting .env file. Please check your database configuration.");
            exit;
        }

        $this->callSilent('config:clear');
        $this->callSilent('config:cache');

        if ($this->wantConfigDb === 'Yes') {
            $this->info('Setting .env file');
            $env = file_get_contents(base_path('.env'));
            $env = preg_replace('/^APP_NAME=.*$/m', 'APP_NAME="' . $this->appName . '"', $env);
            $env = preg_replace('/^DB_CONNECTION=.*$/m', 'DB_CONNECTION=' . $this->dbPlatform, $env);
            $env = preg_replace('/^\s*#?\s*DB_DATABASE=.*$/m', 'DB_DATABASE=' . $this->dbDatabase, $env);
            $env = preg_replace('/^\s*#?\s*DB_USERNAME=.*$/m', 'DB_USERNAME=' . $this->dbUsername, $env);
            $env = preg_replace('/^\s*#?\s*DB_PASSWORD=.*$/m', 'DB_PASSWORD=' . $this->dbPassword, $env);
            $env = preg_replace('/^\s*#?\s*DB_HOST=.*$/m', 'DB_HOST=' . $this->dbHost, $env);
            $env = preg_replace('/^\s*#?\s*DB_PORT=.*$/m', 'DB_PORT=' . $this->dbPort, $env);
            file_put_contents(base_path('.env'), $env);

            putenv('DB_CONNECTION=' . $this->dbPlatform);
            putenv('DB_HOST=' . $this->dbHost);
            putenv('DB_PORT=' . $this->dbPort);
            putenv('DB_DATABASE=' . $this->dbDatabase);
            putenv('DB_USERNAME=' . $this->dbUsername);
            putenv('DB_PASSWORD=' . $this->dbPassword);
        }

        $this->callSilent('config:clear');
        $this->callSilent('config:cache');

        $this->setEnvCountDown--;
        $this->testConnection();
    }

    private function checkWritableDirectory()
    {
        $this->info('Checking directory permission');

        if (!is_writable(resource_path())) {
            $this->output->writeln("   \033[33m[FAILED]\033[0m");
            $this->warn("Resources path is not writable by PHP. Please check your file permission.");
            exit;
        }
        if (!is_writable(storage_path())) {
            $this->output->writeln("   \033[33m[FAILED]\033[0m");
            $this->warn("Storage path is not writable by PHP. Please check your file permission.");
            exit;
        }
    }

    private function testConnection()
    {
        try {
            DB::connection()->getPdo();
            $this->info('Testing database connection success');
        } catch (QueryException|\Exception $e) {
            $this->info('Testing database connection failed');
            return;
        }
    }

    private function checkUser ()
    {
        $this->info('Checking user table');

        if (!Schema::hasTable("users")) {
            return false;
        }

        if (Schema::hasTable('cb_modules')) {
            $this->info('CRUDBooster is already installed. You can\'t install it twice.');
            exit;
        } else {
            return false;
        }
    }

    private function createFirstUser (): void
    {
        $this->info('Creating first user');
        UserService::createUser ($this->name, $this->email, $this->password, "The CB Master", "08123456789");
    }

    private function createDefaultRole(): void
    {
        $this->info('Creating default role');
        $created = CbRoleService::create([
            'name' => config('cb.super_admin_role', 'SUPER ADMIN'),
            'created_at' => now(),
            'permissions' => [],
        ]);

        // get first user
        $user = User::first();

        DB::table('cb_role_users')->insert([
            'id' => Str::uuid()->toString(),
            'role_id' => $created->id,
            'user_id' => $user->id
        ]);
    }

    private function runMigration()
    {
        $this->info('Running migration');
        Artisan::call('migrate');
    }

    private function publishAsset()
    {
        $this->info('Publishing assets');
        $this->callSilent('vendor:publish', ['--tag' => 'cb-themes', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'cb-lang', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'cb-summernote-assets', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'livewire:assets', '--ansi' => true, '--force' => true]);
        $this->callSilent('storage:link');
    }
}
