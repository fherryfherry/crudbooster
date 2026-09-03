<?php

use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;
use CrudBooster\Components\Type\Radio\Function\Radio;

/**
 * Example: Select Option Model with Callback Query and $id Parameter
 * 
 * This example demonstrates how to use the new $id parameter in callback queries
 * for Select, Checkbox, and Radio components. The $id parameter contains the
 * current form data ID that is being edited.
 */

class UserForm extends BaseFormComponent
{
    public $pageTitle = "Users";
    protected $modelService = UserService::class;
    protected $modelName = User::class;

    public function init(): void
    {
        $this->makeForm([
            // Basic user fields
            Form::add(label: 'Name', key: 'name', type: 'text'),
            Form::add(label: 'Email', key: 'email', type: 'email'),
            
            // Select with callback using $id parameter
            Form::add(label: 'Department', key: 'department_id', type: 'select')
                ->option(Select::option()
                    ->model(Department::class, 'id', 'name', function($query, $id = null) {
                        // Filter departments based on current user's department
                        if ($id) {
                            $currentUser = User::find($id);
                            if ($currentUser && $currentUser->role === 'manager') {
                                // Managers can see all departments
                                $query->where('status', 'active');
                            } else {
                                // Regular users can only see their own department
                                $query->where('id', $currentUser->department_id ?? 0);
                            }
                        } else {
                            // For new users, show all active departments
                            $query->where('status', 'active');
                        }
                    })),
            
            // Checkbox with callback using $id parameter
            Form::add(label: 'Permissions', key: 'permissions', type: 'checkbox')
                ->option(Checkbox::option()
                    ->model(Permission::class, 'id', 'name', function($query, $id = null) {
                        if ($id) {
                            $currentUser = User::find($id);
                            if ($currentUser && $currentUser->role === 'admin') {
                                // Admins can assign all permissions
                                $query->where('is_active', true);
                            } else {
                                // Regular users can only assign basic permissions
                                $query->where('is_active', true)
                                      ->where('level', 'basic');
                            }
                        } else {
                            // For new users, show only basic permissions
                            $query->where('is_active', true)
                                  ->where('level', 'basic');
                        }
                    })),
            
            // Radio with callback using $id parameter
            Form::add(label: 'Role', key: 'role', type: 'radio')
                ->option(Radio::option()
                    ->model(Role::class, 'id', 'name', function($query, $id = null) {
                        if ($id) {
                            $currentUser = User::find($id);
                            if ($currentUser && $currentUser->role === 'super_admin') {
                                // Super admins can assign any role
                                $query->where('is_active', true);
                            } else {
                                // Regular admins can only assign non-admin roles
                                $query->where('is_active', true)
                                      ->where('name', '!=', 'super_admin');
                            }
                        } else {
                            // For new users, show only basic roles
                            $query->where('is_active', true)
                                  ->whereIn('name', ['user', 'editor']);
                        }
                    })),
        ]);
    }
}

/**
 * Example: Complex Nested Relationship with $id Parameter
 */
class OrderForm extends BaseFormComponent
{
    public $pageTitle = "Orders";
    protected $modelService = OrderService::class;
    protected $modelName = Order::class;

    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Customer', key: 'customer_id', type: 'select')
                ->option(Select::option()
                    ->model(Customer::class, 'id', 'name', function($query, $id = null) {
                        if ($id) {
                            $currentOrder = Order::find($id);
                            if ($currentOrder && $currentOrder->status === 'completed') {
                                // For completed orders, show only the current customer
                                $query->where('id', $currentOrder->customer_id);
                            } else {
                                // For other orders, show all active customers
                                $query->where('status', 'active');
                            }
                        } else {
                            // For new orders, show all active customers
                            $query->where('status', 'active');
                        }
                    })),
            
            Form::add(label: 'Products', key: 'product_id', type: 'select')
                ->option(Select::option()
                    ->model(Product::class, 'id', 'name', function($query, $id = null) {
                        if ($id) {
                            $currentOrder = Order::find($id);
                            if ($currentOrder) {
                                // Filter products based on customer's preferences
                                $customer = $currentOrder->customer;
                                if ($customer) {
                                    $query->where('category_id', $customer->preferred_category_id)
                                          ->where('price', '<=', $customer->max_budget)
                                          ->where('stock', '>', 0);
                                }
                            }
                        } else {
                            // For new orders, show all available products
                            $query->where('stock', '>', 0)
                                  ->where('is_active', true);
                        }
                    })),
        ]);
    }
}

/**
 * Example: Setting Form Configuration with $id Parameter
 * 
 * This shows how to configure the callback query in the setting form
 * where users can input the query as a string.
 */
class SettingFormExample
{
    public function getSelectModelQueryExample()
    {
        return [
            // Basic example
            'function($query, $id = null) { 
                if ($id) {
                    $query->where("parent_id", $id);
                }
                return $query->where("status", "active"); 
            }',
            
            // Complex example with multiple conditions
            'function($query, $id = null) { 
                if ($id) {
                    $currentRecord = App\Models\MainModel::find($id);
                    if ($currentRecord) {
                        $query->where("category_id", $currentRecord->category_id)
                              ->where("created_by", $currentRecord->created_by)
                              ->where("is_visible", true);
                    }
                } else {
                    $query->where("is_public", true);
                }
                return $query->orderBy("name"); 
            }',
            
            // Example with user permissions
            'function($query, $id = null) { 
                if ($id) {
                    $user = auth()->user();
                    if ($user->isAdmin()) {
                        $query->where("status", "active");
                    } else {
                        $query->where("created_by", $user->id)
                              ->where("status", "active");
                    }
                } else {
                    $query->where("status", "active");
                }
                return $query; 
            }',
        ];
    }
}

/**
 * Example: Backward Compatibility
 * 
 * Old callback queries without $id parameter still work perfectly
 */
class BackwardCompatibilityExample
{
    public function init(): void
    {
        $this->makeForm([
            // Old format - still works
            Form::add(label: 'Category', key: 'category_id', type: 'select')
                ->option(Select::option()
                    ->model(Category::class, 'id', 'name', function($query) {
                        // This old format still works
                        return $query->where('status', 'active');
                    })),
            
            // New format - with $id parameter
            Form::add(label: 'Subcategory', key: 'subcategory_id', type: 'select')
                ->option(Select::option()
                    ->model(Subcategory::class, 'id', 'name', function($query, $id = null) {
                        // New format with $id parameter
                        if ($id) {
                            $currentRecord = MainModel::find($id);
                            if ($currentRecord) {
                                $query->where('category_id', $currentRecord->category_id);
                            }
                        }
                        return $query->where('status', 'active');
                    })),
        ]);
    }
}

/**
 * Benefits of the new $id parameter:
 * 
 * 1. **Context-Aware Filtering**: Filter options based on the current record being edited
 * 2. **Permission-Based Options**: Show different options based on user permissions or record ownership
 * 3. **Relationship Filtering**: Filter related options based on parent record data
 * 4. **Dynamic Validation**: Apply different validation rules based on existing data
 * 5. **Backward Compatibility**: Old callback queries continue to work without modification
 * 
 * Usage Scenarios:
 * - Filter cities based on selected province
 * - Show only user's own projects in dropdown
 * - Filter permissions based on user role
 * - Show only available products based on customer preferences
 * - Filter departments based on user's access level
 */
