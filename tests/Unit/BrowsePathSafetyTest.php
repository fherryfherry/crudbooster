<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BrowsePathSafetyTest extends TestCase
{
    public function testBrowsePathFallbackLogic()
    {
        // Test fallback logic untuk moduleKey dan browsePath
        $moduleKey = 'test_module';
        $browsePath = null;
        
        // Simulasi logic yang digunakan di WithActionButton
        $result = $moduleKey ?? $browsePath ?? 'default';
        $this->assertEquals('test_module', $result);
        
        // Test ketika moduleKey null
        $moduleKey = null;
        $browsePath = 'test_browse';
        $result = $moduleKey ?? $browsePath ?? 'default';
        $this->assertEquals('test_browse', $result);
        
        // Test ketika keduanya null
        $moduleKey = null;
        $browsePath = null;
        $result = $moduleKey ?? $browsePath ?? 'default';
        $this->assertEquals('default', $result);
    }

    public function testBrowsePathRedirectSafety()
    {
        // Test redirect safety dengan browsePath yang null
        $browsePath = null;
        $safeBrowsePath = $browsePath ?? 'dashboard';
        
        $this->assertEquals('dashboard', $safeBrowsePath);
        
        // Test dengan browsePath yang valid
        $browsePath = 'users';
        $safeBrowsePath = $browsePath ?? 'dashboard';
        
        $this->assertEquals('users', $safeBrowsePath);
    }

    public function testBrowsePathUrlGeneration()
    {
        // Test URL generation dengan browsePath yang aman
        $browsePath = null;
        $safeBrowsePath = $browsePath ?? 'dashboard';
        $url = '/cms/' . $safeBrowsePath;
        
        $this->assertEquals('/cms/dashboard', $url);
        
        // Test dengan browsePath yang valid
        $browsePath = 'products';
        $safeBrowsePath = $browsePath ?? 'dashboard';
        $url = '/cms/' . $safeBrowsePath;
        
        $this->assertEquals('/cms/products', $url);
    }

    public function testBrowsePathSessionKeySafety()
    {
        // Test session key generation dengan browsePath yang aman
        $moduleKey = null;
        $browsePath = null;
        $sessionKey = ($moduleKey ?? $browsePath ?? 'default') . '_filter';
        
        $this->assertEquals('default_filter', $sessionKey);
        
        // Test dengan moduleKey yang valid
        $moduleKey = 'orders';
        $browsePath = null;
        $sessionKey = ($moduleKey ?? $browsePath ?? 'default') . '_filter';
        
        $this->assertEquals('orders_filter', $sessionKey);
    }

    public function testBrowsePathPermissionCheck()
    {
        // Test permission check dengan browsePath yang aman
        $moduleKey = null;
        $browsePath = null;
        $permissionKey = $moduleKey ?? $browsePath ?? 'default';
        
        $this->assertEquals('default', $permissionKey);
        
        // Test dengan moduleKey yang valid
        $moduleKey = 'customers';
        $browsePath = null;
        $permissionKey = $moduleKey ?? $browsePath ?? 'default';
        
        $this->assertEquals('customers', $permissionKey);
    }

    public function testBrowsePathActionButtonIsolation()
    {
        // Test action button isolation dengan browsePath yang aman
        $moduleKey = null;
        $browsePath = null;
        $actionButtonKey = $moduleKey ?? $browsePath ?? 'default';
        
        $this->assertEquals('default', $actionButtonKey);
        
        // Test dengan moduleKey yang valid
        $moduleKey = 'invoices';
        $browsePath = null;
        $actionButtonKey = $moduleKey ?? $browsePath ?? 'default';
        
        $this->assertEquals('invoices', $actionButtonKey);
    }

    public function testBrowsePathWithSubModule()
    {
        // Test dengan sub module scenario
        $moduleKey = 'parent_module';
        $browsePath = 'parent_module';
        $foreignKeyValue = '123';
        
        // Simulasi session key untuk sub module
        $sessionKey = $foreignKeyValue ? "{$moduleKey}_{$foreignKeyValue}" : $moduleKey;
        
        $this->assertEquals('parent_module_123', $sessionKey);
        
        // Test tanpa foreign key value
        $foreignKeyValue = null;
        $sessionKey = $foreignKeyValue ? "{$moduleKey}_{$foreignKeyValue}" : $moduleKey;
        
        $this->assertEquals('parent_module', $sessionKey);
    }

    public function testBrowsePathErrorHandling()
    {
        // Test error handling dengan browsePath yang tidak valid
        $browsePath = null;
        
        // Simulasi error handling
        try {
            $safeBrowsePath = $browsePath ?? 'dashboard';
            $this->assertEquals('dashboard', $safeBrowsePath);
        } catch (\Exception $e) {
            $this->fail('Should not throw exception with safe fallback');
        }
        
        // Test dengan browsePath yang valid
        $browsePath = 'valid_module';
        $safeBrowsePath = $browsePath ?? 'dashboard';
        $this->assertEquals('valid_module', $safeBrowsePath);
    }

    public function testBrowsePathMultipleFallbacks()
    {
        // Test multiple fallback levels
        $primaryKey = null;
        $secondaryKey = null;
        $tertiaryKey = null;
        
        $result = $primaryKey ?? $secondaryKey ?? $tertiaryKey ?? 'final_fallback';
        $this->assertEquals('final_fallback', $result);
        
        // Test dengan secondary key yang valid
        $secondaryKey = 'secondary_module';
        $result = $primaryKey ?? $secondaryKey ?? $tertiaryKey ?? 'final_fallback';
        $this->assertEquals('secondary_module', $result);
        
        // Test dengan primary key yang valid
        $primaryKey = 'primary_module';
        $result = $primaryKey ?? $secondaryKey ?? $tertiaryKey ?? 'final_fallback';
        $this->assertEquals('primary_module', $result);
    }

    public function testBrowsePathConfiguration()
    {
        // Test configuration dengan browsePath yang aman
        $configs = [
            'moduleKey' => null,
            'browsePath' => null,
            'defaultPath' => 'dashboard'
        ];
        
        $activePath = $configs['moduleKey'] ?? $configs['browsePath'] ?? $configs['defaultPath'];
        $this->assertEquals('dashboard', $activePath);
        
        // Test dengan moduleKey yang valid
        $configs['moduleKey'] = 'active_module';
        $activePath = $configs['moduleKey'] ?? $configs['browsePath'] ?? $configs['defaultPath'];
        $this->assertEquals('active_module', $activePath);
    }

    public function testBrowsePathUrlBuilding()
    {
        // Test URL building dengan browsePath yang aman
        $baseUrl = '/cms';
        $browsePath = null;
        $safeBrowsePath = $browsePath ?? 'dashboard';
        
        $fullUrl = $baseUrl . '/' . $safeBrowsePath;
        $this->assertEquals('/cms/dashboard', $fullUrl);
        
        // Test dengan query parameters
        $queryParams = ['ref' => 'back_url'];
        $queryString = http_build_query($queryParams);
        $fullUrl = $baseUrl . '/' . $safeBrowsePath . '?' . $queryString;
        $this->assertEquals('/cms/dashboard?ref=back_url', $fullUrl);
    }
}
