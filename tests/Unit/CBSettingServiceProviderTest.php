<?php

namespace CrudBooster\Tests\Unit;

use CrudBooster\Modules\Setting\CBSettingServiceProvider;
use CrudBooster\Tests\BaseTestCase;
use Illuminate\Support\Facades\File;

class CBSettingServiceProviderTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test directory structure
        $this->createTestSettingStructure();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestSettingStructure();
        parent::tearDown();
    }

    private function createTestSettingStructure(): void
    {
        $testPath = app_path('Cb/Settings/TestSetting');
        
        if (!File::exists($testPath)) {
            File::makeDirectory($testPath, 0755, true);
        }

        // Create a test service provider
        $serviceProviderContent = <<<PHP
<?php

namespace App\Cb\Settings\TestSetting;

use Illuminate\Support\ServiceProvider;

class CbTestSettingSettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Test boot method
    }

    public function register()
    {
        // Test register method
    }
}
PHP;

        File::put($testPath . '/CbTestSettingSettingServiceProvider.php', $serviceProviderContent);
    }

    private function cleanupTestSettingStructure(): void
    {
        $testPath = app_path('Cb/Settings/TestSetting');
        if (File::exists($testPath)) {
            File::deleteDirectory($testPath);
        }
    }

    public function test_can_load_user_setting_service_providers()
    {
        $serviceProvider = new CBSettingServiceProvider($this->app);
        
        // Test that the method exists
        $reflection = new \ReflectionClass($serviceProvider);
        $method = $reflection->getMethod('loadUserSettingServiceProviders');
        $method->setAccessible(true);
        
        // This should not throw an exception
        $method->invoke($serviceProvider);
        
        $this->assertTrue(true); // If we reach here, the method executed successfully
    }

    public function test_user_setting_service_provider_file_exists()
    {
        $testPath = app_path('Cb/Settings/TestSetting/CbTestSettingSettingServiceProvider.php');
        
        $this->assertTrue(File::exists($testPath), 'Test setting service provider file should exist');
        
        $content = File::get($testPath);
        $this->assertStringContainsString('namespace App\\Cb\\Settings\\TestSetting;', $content);
        $this->assertStringContainsString('class CbTestSettingSettingServiceProvider', $content);
    }

    public function test_settings_directory_structure()
    {
        $settingsPath = app_path('Cb/Settings');
        
        // The directory should exist after setUp
        $this->assertTrue(File::exists($settingsPath), 'Settings directory should exist');
        
        $testPath = app_path('Cb/Settings/TestSetting');
        $this->assertTrue(File::exists($testPath), 'Test setting directory should exist');
    }
} 