<?php

namespace CrudBooster\Tests\Unit;

use CrudBooster\Components\ActionButton\ActionButton;
use CrudBooster\Components\ActionButton\ActionButtonOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionButtonSubModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_action_button_options_can_be_cleared()
    {
        // Arrange
        $actionButtonOptions = app(ActionButtonOptions::class);
        
        // Add some action buttons
        $actionButtonOptions->setOption('Test Button 1', [
            'label' => 'Test Button 1',
            'url' => 'test1',
            'icon' => 'test-icon-1',
            'class' => 'btn btn-primary',
            'visible' => true
        ]);
        
        $actionButtonOptions->setOption('Test Button 2', [
            'label' => 'Test Button 2',
            'url' => 'test2',
            'icon' => 'test-icon-2',
            'class' => 'btn btn-success',
            'visible' => true
        ]);

        // Assert buttons are added
        $this->assertCount(2, $actionButtonOptions->getOptions());
        $this->assertArrayHasKey('Test Button 1', $actionButtonOptions->getOptions());
        $this->assertArrayHasKey('Test Button 2', $actionButtonOptions->getOptions());

        // Act
        $actionButtonOptions->clearOptions();

        // Assert options are cleared
        $this->assertCount(0, $actionButtonOptions->getOptions());
        $this->assertEmpty($actionButtonOptions->getOptions());
    }

    public function test_action_button_static_clear_options_method()
    {
        // Arrange
        $actionButtonOptions = app(ActionButtonOptions::class);
        
        // Add some action buttons
        $actionButtonOptions->setOption('Test Button', [
            'label' => 'Test Button',
            'url' => 'test',
            'icon' => 'test-icon',
            'class' => 'btn btn-primary',
            'visible' => true
        ]);

        // Assert button is added
        $this->assertCount(1, $actionButtonOptions->getOptions());

        // Act
        ActionButton::__clearOptions();

        // Assert options are cleared
        $this->assertCount(0, $actionButtonOptions->getOptions());
    }

    public function test_action_button_can_be_added_after_clear()
    {
        // Arrange
        $actionButtonOptions = app(ActionButtonOptions::class);
        
        // Add initial button
        $actionButtonOptions->setOption('Initial Button', [
            'label' => 'Initial Button',
            'url' => 'initial',
            'icon' => 'initial-icon',
            'class' => 'btn btn-primary',
            'visible' => true
        ]);

        // Assert initial button exists
        $this->assertCount(1, $actionButtonOptions->getOptions());

        // Act - Clear options
        ActionButton::__clearOptions();

        // Assert options are cleared
        $this->assertCount(0, $actionButtonOptions->getOptions());

        // Act - Add new button
        new ActionButton([
            'label' => 'New Button',
            'url' => 'new',
            'icon' => 'new-icon',
            'class' => 'btn btn-success',
            'visible' => true
        ]);

        // Assert new button is added
        $this->assertCount(1, $actionButtonOptions->getOptions());
        $this->assertArrayHasKey('New Button', $actionButtonOptions->getOptions());
        $this->assertEquals('New Button', $actionButtonOptions->getOptions()['New Button']['label']);
    }

    public function test_sub_module_isolation_from_parent_module()
    {
        // Arrange - Simulate parent module adding action buttons
        $parentButton = new ActionButton([
            'label' => 'Parent Button',
            'url' => 'parent',
            'icon' => 'parent-icon',
            'class' => 'btn btn-primary',
            'visible' => true
        ]);

        $actionButtonOptions = app(ActionButtonOptions::class);
        $this->assertCount(1, $actionButtonOptions->getOptions());
        $this->assertArrayHasKey('Parent Button', $actionButtonOptions->getOptions());

        // Act - Simulate sub module clearing and adding its own buttons
        ActionButton::__clearOptions();
        
        $subButton = new ActionButton([
            'label' => 'Sub Button',
            'url' => 'sub',
            'icon' => 'sub-icon',
            'class' => 'btn btn-success',
            'visible' => true
        ]);

        // Assert only sub module button exists
        $this->assertCount(1, $actionButtonOptions->getOptions());
        $this->assertArrayHasKey('Sub Button', $actionButtonOptions->getOptions());
        $this->assertArrayNotHasKey('Parent Button', $actionButtonOptions->getOptions());
    }

    public function test_action_button_options_singleton_behavior()
    {
        // Arrange
        $actionButtonOptions1 = app(ActionButtonOptions::class);
        $actionButtonOptions2 = app(ActionButtonOptions::class);

        // Assert both instances are the same (singleton)
        $this->assertSame($actionButtonOptions1, $actionButtonOptions2);

        // Add button to first instance
        $actionButtonOptions1->setOption('Test Button', [
            'label' => 'Test Button',
            'url' => 'test',
            'icon' => 'test-icon',
            'class' => 'btn btn-primary',
            'visible' => true
        ]);

        // Assert button is available in second instance
        $this->assertArrayHasKey('Test Button', $actionButtonOptions2->getOptions());
    }

    public function test_action_button_constructor_behavior()
    {
        // Arrange
        $actionButtonOptions = app(ActionButtonOptions::class);
        
        // Clear any existing options
        ActionButton::__clearOptions();
        
        // Act - Create new action button
        $button = new ActionButton([
            'label' => 'Test Button',
            'url' => 'test-url',
            'icon' => 'test-icon',
            'class' => 'btn btn-primary',
            'visible' => true
        ]);

        // Assert button is stored in options
        $this->assertCount(1, $actionButtonOptions->getOptions());
        $this->assertArrayHasKey('Test Button', $actionButtonOptions->getOptions());
        
        $storedButton = $actionButtonOptions->getOptions()['Test Button'];
        $this->assertEquals('Test Button', $storedButton['label']);
        $this->assertEquals('test-url', $storedButton['url']);
        $this->assertEquals('test-icon', $storedButton['icon']);
        $this->assertEquals('btn btn-primary', $storedButton['class']);
        $this->assertTrue($storedButton['visible']);
    }
} 