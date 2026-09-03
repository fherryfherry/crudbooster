<?php

namespace CrudBooster\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class GenerateSettingCommand extends Command
{
    protected $signature = 'cb:setting
                        {--name= : The name of the setting module}
                        {--key= : The key for the setting (default: auto-generated from name)}
                        {--icon= : The icon for the setting (default: COG)}
                        {--order=1 : The order of the setting in the menu}';
    
    protected $description = 'Generate a new setting module';

    public function handle()
    {
        $name = $this->option('name');
        $key = $this->option('key');
        $icon = $this->option('icon') ?? 'COG';
        $order = $this->option('order') ?? 1;

        if (!$name) {
            $name = text(
                label: 'Enter the name of the setting module',
                required: true,
                validate: 'string|max:100',
                hint: 'Example: Email Configuration, Social Media Settings, etc.'
            );
        }

        if (!$key) {
            $key = $this->generateKeyFromName($name);
        }

        $icon = text(
            label: 'Enter the icon for the setting',
            default: $icon,
            validate: 'string',
            hint: 'Example: COG, BUILDING, EMAIL, etc.'
        );

        $order = text(
            label: 'Enter the order of the setting in the menu',
            default: $order,
            validate: 'integer|min:1',
            hint: 'Lower number appears first in the menu'
        );

        spin(
            callback: fn() => $this->generateSettingModule($name, $key, $icon, $order),
            message: 'Generating setting module for ' . $name . '...',
        );

        $this->info('Setting module generated successfully!');
        $this->info('Module location: app/Cb/Settings/' . Str::studly($name));
        $this->info('Service provider will be automatically registered by CRUDBooster');
    }

    private function generateKeyFromName(string $name): string
    {
        return Str::slug($name, '-');
    }

    private function generateSettingModule(string $name, string $key, string $icon, int $order): void
    {
        $moduleName = Str::studly($name);
        $modulePath = app_path('Cb/Settings/' . $moduleName);
        $namespace = 'App\\Cb\\Settings\\' . $moduleName;

        // Create directory structure
        $this->createDirectoryStructure($modulePath);

        // Generate service provider
        $this->generateServiceProvider($modulePath, $namespace, $name, $key, $icon, $order);

        // Generate Livewire component
        $this->generateLivewireComponent($modulePath, $namespace, $name, $key);

        // Generate view
        $this->generateView($modulePath, $name, $key);

        // Generate helper
        $this->generateHelper($modulePath, $namespace, $name, $key);
    }

    private function createDirectoryStructure(string $modulePath): void
    {
        $filesystem = new Filesystem();
        
        $directories = [
            $modulePath,
            $modulePath . '/Livewire',
            $modulePath . '/views',
            $modulePath . '/Helpers',
        ];

        foreach ($directories as $directory) {
            if (!$filesystem->exists($directory)) {
                $filesystem->makeDirectory($directory, 0755, true);
            }
        }
    }

    private function generateServiceProvider(string $modulePath, string $namespace, string $name, string $key, string $icon, int $order): void
    {
        $className = $this->getClassName($name);
        
        $serviceProviderContent = <<<PHP
<?php

namespace {$namespace};

use CrudBooster\Components\Icon\Icon;
use CrudBooster\Modules\Setting\CbSettingRegistrar;
use {$namespace}\Helpers\\{$className}Property;
use {$namespace}\Livewire\\{$className}Setting;
use CrudBooster\Modules\Setting\Services\CbSettingService;
use Illuminate\Support\ServiceProvider;

class Cb{$className}SettingServiceProvider extends ServiceProvider
{
    private \$key = '{$key}';
    
    public function boot()
    {
        \$this->loadViewsFrom(__DIR__ . '/views', 'cb.{$key}-setting');
        
        CbSettingRegistrar::add(\$this->key, [
            'label' => '{$name}',
            'icon' => Icon::{$icon},
            'clazz' => {$className}Setting::class,
            'order' => {$order}
        ]);
    }

    public function register()
    {
        require_once __DIR__ . '/Helpers/Common.php';
        
        \$this->app->singleton({$className}Property::class, function () {
            \$settingCache = CbSettingService::get(\$this->key);
            return new {$className}Property(\$settingCache);
        });
    }
}
PHP;

        file_put_contents($modulePath . '/Cb' . $className . 'SettingServiceProvider.php', $serviceProviderContent);
    }

    private function generateLivewireComponent(string $modulePath, string $namespace, string $name, string $key): void
    {
        $className = $this->getClassName($name);
        
        $livewireContent = <<<PHP
<?php

namespace {$namespace}\Livewire;

use CrudBooster\Modules\Setting\CbBaseSetting;

class {$className}Setting extends CbBaseSetting
{
    public \$key = '{$key}';
    
    public function render()
    {
        return view('cb.{$key}-setting::{$key}');
    }
}
PHP;

        file_put_contents($modulePath . '/Livewire/' . $className . 'Setting.php', $livewireContent);
    }

    private function generateView(string $modulePath, string $name, string $key): void
    {
        $viewContent = <<<BLADE
<div>
    <h1 class="text-2xl mb-10 flex justify-start items-center gap-2">{!! \CrudBooster\Components\Icon\Icon::COG !!} {$name}</h1>
    <div class="frame">
        <div class="frame-title">
            {$name}
        </div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label for="setting_value">Setting Value</label>
                    <input type="text" wire:model="form.setting_value" class="form-control w-full lg:!w-1/2" placeholder="Enter setting value">
                    <div class="form-help">
                        This is a sample setting field. You can modify this form according to your needs.
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea wire:model="form.description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="is_active">Active</label>
                    <select wire:model="form.is_active" class="form-control w-full lg:!w-1/3">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                
                <div class="flex flex-row justify-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
BLADE;

        file_put_contents($modulePath . '/views/' . $key . '.blade.php', $viewContent);
    }

    private function generateHelper(string $modulePath, string $namespace, string $name, string $key): void
    {
        $className = $this->getClassName($name);
        
        $helperContent = <<<PHP
<?php

namespace {$namespace}\Helpers;

class {$className}Property
{
    public \$setting_value;
    public \$description;
    public \$is_active;

    public function __construct(array \$data = [])
    {
        \$this->setting_value = \$data['setting_value'] ?? '';
        \$this->description = \$data['description'] ?? '';
        \$this->is_active = \$data['is_active'] ?? 1;
    }
}
PHP;

        file_put_contents($modulePath . '/Helpers/' . $className . 'Property.php', $helperContent);

        // Generate Common.php helper
        $commonContent = <<<PHP
<?php

if (!function_exists('get{$className}Setting')) {
    function get{$className}Setting()
    {
        return app(\App\Cb\Settings\\{$className}\Helpers\\{$className}Property::class);
    }
}
PHP;

        file_put_contents($modulePath . '/Helpers/Common.php', $commonContent);
    }



    private function getClassName(string $name): string
    {
        return Str::studly($name);
    }
} 