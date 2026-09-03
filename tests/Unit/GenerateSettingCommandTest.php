<?php

namespace CrudBooster\Tests\Unit;

use CrudBooster\Commands\GenerateSettingCommand;
use CrudBooster\Tests\BaseTestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class GenerateSettingCommandTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clean up any existing test files
        $this->cleanupTestFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestFiles();
        parent::tearDown();
    }

    private function cleanupTestFiles(): void
    {
        $testPath = app_path('Cb/Settings/TestSetting');
        if (File::exists($testPath)) {
            File::deleteDirectory($testPath);
        }
    }

    public function test_generate_setting_command_creates_correct_structure()
    {
        $command = new GenerateSettingCommand();
        
        // Test the command signature
        $this->assertEquals('cb:setting', $command->getName());
        
        // Test that the command has the correct options
        $signature = $command->getSignature();
        $this->assertStringContainsString('--name=', $signature);
        $this->assertStringContainsString('--key=', $signature);
        $this->assertStringContainsString('--icon=', $signature);
        $this->assertStringContainsString('--order=', $signature);
    }

    public function test_generate_key_from_name()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('generateKeyFromName');
        $method->setAccessible(true);

        $this->assertEquals('email-configuration', $method->invoke($command, 'Email Configuration'));
        $this->assertEquals('social-media-settings', $method->invoke($command, 'Social Media Settings'));
        $this->assertEquals('payment-gateway', $method->invoke($command, 'Payment Gateway'));
    }

    public function test_get_class_name()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getClassName');
        $method->setAccessible(true);

        $this->assertEquals('EmailConfiguration', $method->invoke($command, 'Email Configuration'));
        $this->assertEquals('SocialMediaSettings', $method->invoke($command, 'Social Media Settings'));
        $this->assertEquals('PaymentGateway', $method->invoke($command, 'Payment Gateway'));
    }

    public function test_generate_setting_module_creates_files()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('generateSettingModule');
        $method->setAccessible(true);

        // Generate a test setting module
        $method->invoke($command, 'Test Setting', 'test-setting', 'SETTINGS', 1);

        $testPath = app_path('Cb/Settings/TestSetting');
        
        // Check if directory structure was created
        $this->assertTrue(File::exists($testPath));
        $this->assertTrue(File::exists($testPath . '/Livewire'));
        $this->assertTrue(File::exists($testPath . '/views'));
        $this->assertTrue(File::exists($testPath . '/Helpers'));

        // Check if service provider was created
        $this->assertTrue(File::exists($testPath . '/CbTestSettingSettingServiceProvider.php'));

        // Check if Livewire component was created
        $this->assertTrue(File::exists($testPath . '/Livewire/TestSettingSetting.php'));

        // Check if view was created
        $this->assertTrue(File::exists($testPath . '/views/test-setting.blade.php'));

        // Check if helpers were created
        $this->assertTrue(File::exists($testPath . '/Helpers/TestSettingProperty.php'));
        $this->assertTrue(File::exists($testPath . '/Helpers/Common.php'));
    }

    public function test_generated_service_provider_has_correct_content()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('generateSettingModule');
        $method->setAccessible(true);

        // Generate a test setting module
        $method->invoke($command, 'Test Setting', 'test-setting', 'SETTINGS', 1);

        $serviceProviderPath = app_path('Cb/Settings/TestSetting/CbTestSettingSettingServiceProvider.php');
        $content = File::get($serviceProviderPath);

        // Check if the content contains expected elements
        $this->assertStringContainsString('namespace App\\Cb\\Settings\\TestSetting;', $content);
        $this->assertStringContainsString('class CbTestSettingSettingServiceProvider', $content);
        $this->assertStringContainsString("'key' => 'test-setting'", $content);
        $this->assertStringContainsString("'label' => 'Test Setting'", $content);
        $this->assertStringContainsString("Icon::SETTINGS", $content);
    }

    public function test_generated_livewire_component_has_correct_content()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('generateSettingModule');
        $method->setAccessible(true);

        // Generate a test setting module
        $method->invoke($command, 'Test Setting', 'test-setting', 'SETTINGS', 1);

        $livewirePath = app_path('Cb/Settings/TestSetting/Livewire/TestSettingSetting.php');
        $content = File::get($livewirePath);

        // Check if the content contains expected elements
        $this->assertStringContainsString('namespace App\\Cb\\Settings\\TestSetting\\Livewire;', $content);
        $this->assertStringContainsString('class TestSettingSetting extends CbBaseSetting', $content);
        $this->assertStringContainsString("public \$key = 'test-setting';", $content);
        $this->assertStringContainsString("return view('cb.test-setting-setting::test-setting');", $content);
    }

    public function test_generated_view_has_correct_content()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('generateSettingModule');
        $method->setAccessible(true);

        // Generate a test setting module
        $method->invoke($command, 'Test Setting', 'test-setting', 'SETTINGS', 1);

        $viewPath = app_path('Cb/Settings/TestSetting/views/test-setting.blade.php');
        $content = File::get($viewPath);

        // Check if the content contains expected elements
        $this->assertStringContainsString('Test Setting', $content);
        $this->assertStringContainsString('wire:submit.prevent="save"', $content);
        $this->assertStringContainsString('wire:model="form.setting_value"', $content);
        $this->assertStringContainsString('wire:model="form.description"', $content);
        $this->assertStringContainsString('wire:model="form.is_active"', $content);
    }

    public function test_generated_property_helper_has_correct_content()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('generateSettingModule');
        $method->setAccessible(true);

        // Generate a test setting module
        $method->invoke($command, 'Test Setting', 'test-setting', 'SETTINGS', 1);

        $propertyPath = app_path('Cb/Settings/TestSetting/Helpers/TestSettingProperty.php');
        $content = File::get($propertyPath);

        // Check if the content contains expected elements
        $this->assertStringContainsString('namespace App\\Cb\\Settings\\TestSetting\\Helpers;', $content);
        $this->assertStringContainsString('class TestSettingProperty', $content);
        $this->assertStringContainsString('public $setting_value;', $content);
        $this->assertStringContainsString('public $description;', $content);
        $this->assertStringContainsString('public $is_active;', $content);
    }

    public function test_generated_common_helper_has_correct_content()
    {
        $command = new GenerateSettingCommand();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('generateSettingModule');
        $method->setAccessible(true);

        // Generate a test setting module
        $method->invoke($command, 'Test Setting', 'test-setting', 'SETTINGS', 1);

        $commonPath = app_path('Cb/Settings/TestSetting/Helpers/Common.php');
        $content = File::get($commonPath);

        // Check if the content contains expected elements
        $this->assertStringContainsString('getTestSettingSetting', $content);
        $this->assertStringContainsString('TestSettingProperty::class', $content);
    }
} 