<?php

/**
 * Example usage of Checkbox transformLabel functionality
 * 
 * This example demonstrates how to use the transformLabel feature
 * in CRUDBooster checkbox components.
 * 
 * NOTE: This implementation uses the same approach as the select component
 * for consistency across CRUDBooster components.
 */

use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class CheckboxTransformLabelExample
{
    public function basicExample()
    {
        // Basic transformation - convert labels to uppercase
        return Form::add(label: 'Status', key: 'status', type: 'checkbox')
            ->option(Checkbox::option()
                ->dataset([
                    ['key' => 'active', 'label' => 'active'],
                    ['key' => 'inactive', 'label' => 'inactive'],
                    ['key' => 'pending', 'label' => 'pending']
                ])
                ->transformLabel(function($label, $key, $row) {
                    return strtoupper($label);
                }));
    }

    public function withModelExample()
    {
        // With model and transformation - add ID to label
        return Form::add(label: 'Roles', key: 'roles', type: 'checkbox')
            ->option(Checkbox::option()
                ->model(\App\Cb\Modules\Roles::class, 'id', 'name')
                ->transformLabel(function($label, $key, $row) {
                    return ucfirst($label) . ' (ID: ' . $key . ')';
                }));
    }

    public function withModelFieldsExample()
    {
        // With model and transformation using additional fields
        return Form::add(label: 'Users', key: 'users', type: 'checkbox')
            ->option(Checkbox::option()
                ->model(\App\Models\User::class, 'id', 'name')
                ->transformLabel(function($label, $key, $row) {
                    // Now you can access all User model fields via $row
                    $email = $row->email ?? '';
                    $status = $row->status ?? 'unknown';
                    $created_at = $row->created_at ?? '';
                    
                    $statusIcon = $status === 'active' ? '🟢' : '🔴';
                    return $statusIcon . ' ' . ucfirst($label) . ' (' . $email . ')';
                }));
    }

    public function complexModelExample()
    {
        // Complex transformation using multiple model fields
        return Form::add(label: 'Products', key: 'products', type: 'checkbox')
            ->option(Checkbox::option()
                ->model(\App\Models\Product::class, 'id', 'name')
                ->transformLabel(function($label, $key, $row) {
                    // Access product fields
                    $price = $row->price ?? 0;
                    $category = $row->category ?? '';
                    $stock = $row->stock ?? 0;
                    
                    $stockStatus = $stock > 0 ? '📦' : '❌';
                    $priceFormatted = number_format($price, 2);
                    
                    return $stockStatus . ' ' . ucfirst($label) . ' - $' . $priceFormatted . ' (' . $category . ')';
                }));
    }

    public function complexExample()
    {
        // Complex transformation - add icons and formatting
        return Form::add(label: 'Permissions', key: 'permissions', type: 'checkbox')
            ->option(Checkbox::option()
                ->dataset([
                    ['key' => 'read', 'label' => 'read'],
                    ['key' => 'write', 'label' => 'write'],
                    ['key' => 'delete', 'label' => 'delete'],
                    ['key' => 'admin', 'label' => 'admin']
                ])
                ->transformLabel(function($label, $key, $row) {
                    $icons = [
                        'read' => '👁️',
                        'write' => '✏️',
                        'delete' => '🗑️',
                        'admin' => '👑'
                    ];
                    return ($icons[$key] ?? '📋') . ' ' . ucfirst($label);
                }));
    }

    public function conditionalExample()
    {
        // Conditional transformation based on key value
        return Form::add(label: 'Features', key: 'features', type: 'checkbox')
            ->option(Checkbox::option()
                ->dataset([
                    ['key' => 'premium', 'label' => 'premium'],
                    ['key' => 'basic', 'label' => 'basic'],
                    ['key' => 'enterprise', 'label' => 'enterprise']
                ])
                ->transformLabel(function($label, $key, $row) {
                    if ($key === 'premium') {
                        return '⭐ ' . ucfirst($label) . ' (Recommended)';
                    } elseif ($key === 'enterprise') {
                        return '🚀 ' . ucfirst($label) . ' (Advanced)';
                    }
                    return '📦 ' . ucfirst($label);
                }));
    }

    public function stringCodeExample()
    {
        // Using string code instead of closure (for settings)
        return Form::add(label: 'Categories', key: 'categories', type: 'checkbox')
            ->option(Checkbox::option()
                ->dataset([
                    ['key' => 'tech', 'label' => 'technology'],
                    ['key' => 'sport', 'label' => 'sports'],
                    ['key' => 'news', 'label' => 'news']
                ])
                ->transformLabel("return '📰 ' . ucfirst(\$label);"));
    }

    public function safeStringExamples()
    {
        // Examples of string transformations (for settings form)
        $examples = [
            // Basic string functions
            'return strtoupper($label);' => 'Convert to uppercase',
            'return strtolower($label);' => 'Convert to lowercase', 
            'return ucfirst($label);' => 'Capitalize first letter',
            'return ucwords($label);' => 'Capitalize each word',
            
            // String concatenation
            'return "📋 " . $label;' => 'Add prefix icon',
            'return $label . " (ID: " . $key . ")";' => 'Add ID suffix',
            
            // Conditional transformations
            'return $key === "active" ? "🟢 " . ucfirst($label) : "🔴 " . ucfirst($label);' => 'Conditional icons',
        ];

        return $examples;
    }
}

/**
 * Usage in a form component:
 * 
 * class UserForm extends BaseFormComponent
 * {
 *     public function init(): void
 *     {
 *         $example = new CheckboxTransformLabelExample();
 *         
 *         $this->makeForm([
 *             $example->basicExample(),
 *             $example->withModelExample(),
 *             $example->complexExample(),
 *             $example->conditionalExample(),
 *             $example->stringCodeExample(),
 *         ]);
 *     }
 * }
 */ 