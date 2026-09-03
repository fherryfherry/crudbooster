<?php

namespace App\Cb\Modules\Example\Livewire;

use CrudBooster\Livewire\BaseFormComponent;
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Attributes\OnFormSaving;
use CrudBooster\Attributes\OnFormSaved;
use App\Models\User;

/**
 * Example Form Component showing correct usage of OnFormSaving and OnFormSaved hooks
 * 
 * This example demonstrates:
 * 1. Correct method signatures with ($model, $data, $id) parameters
 * 2. How to access form data and modify it before saving
 * 3. How to perform actions after data is saved
 * 4. How to handle both create and update scenarios
 */
class ExampleForm extends BaseFormComponent
{
    public $pageTitle = "Example Form";
    protected $modelService = UserService::class;
    protected $modelName = User::class;

    public function init()
    {
        $this->makeForm([
            Form::add(label: "Name", key: "name", type: "text"),
            Form::add(label: "Email", key: "email", type: "text"),
            Form::add(label: "Status", key: "status", type: "select")
                ->option(Select::option()->dataset("active|Active\ninactive|Inactive")),
        ]);
    }

    /**
     * OnFormSaving Hook - Called before form data is saved
     * 
     * @param string $model The model class (e.g., User::class)
     * @param array $data Array of form data to be saved
     * @param mixed $id Primary key value (null for new records, existing ID for updates)
     */
    #[OnFormSaving]
    public function onFormSaving($model, $data, $id = null)
    {
        // Example 1: Modify form data before saving
        if (isset($this->formData['name'])) {
            $this->formData['name'] = ucwords(strtolower($this->formData['name']));
        }

        // Example 2: Set default values for new records
        if ($id === null) {
            $this->formData['created_at'] = now();
        }

        // Example 3: Validate additional business logic
        if (isset($this->formData['email']) && !filter_var($this->formData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email format');
        }

        // Example 4: Log the operation
        \Log::info('Form saving', [
            'model' => $model,
            'data' => $data,
            'id' => $id,
            'is_new' => $id === null
        ]);
    }

    /**
     * OnFormSaved Hook - Called after form data is successfully saved
     * 
     * @param string $model The model class (e.g., User::class)
     * @param array $data Array of saved form data
     * @param mixed $id Primary key value of the saved record
     */
    #[OnFormSaved]
    public function onFormSaved($model, $data, $id)
    {
        // Example 1: Send notification after save
        $this->sendNotification($data);

        // Example 2: Update related records
        $this->updateRelatedRecords($id, $data);

        // Example 3: Clear cache
        cache()->forget("user_{$id}");

        // Example 4: Log the successful save
        \Log::info('Form saved successfully', [
            'model' => $model,
            'data' => $data,
            'id' => $id
        ]);

        // Example 5: Trigger additional actions
        $this->triggerPostSaveActions($id, $data);
    }

    /**
     * Example helper method for sending notifications
     */
    private function sendNotification($data)
    {
        // Implementation for sending notification
        // e.g., email, SMS, push notification, etc.
    }

    /**
     * Example helper method for updating related records
     */
    private function updateRelatedRecords($id, $data)
    {
        // Implementation for updating related records
        // e.g., update user profile, sync with external systems, etc.
    }

    /**
     * Example helper method for post-save actions
     */
    private function triggerPostSaveActions($id, $data)
    {
        // Implementation for additional post-save actions
        // e.g., webhooks, analytics tracking, etc.
    }
}
