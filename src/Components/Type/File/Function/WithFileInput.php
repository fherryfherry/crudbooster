<?php

namespace CrudBooster\Components\Type\File\Function;

use CrudBooster\Attributes\OnFormDehydrate;
use CrudBooster\Attributes\OnFormSaving;
use CrudBooster\Attributes\OnFormValidated;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Helpers\CbUploader;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

/**
 * @method formColumnsCallbackOnType(string $string, \Closure $param)
 */
trait WithFileInput
{
    use WithFileUploads;
    use WithConfirmMessage;

    private array $fileColumns = [];
    public ?string $__fileDeleteKey = null;
    public ?string $__fileDeleteId = null;

    /**
     * This function will be called before saving the form
     * If it is a string, it will be removed from the form data
     * To prevent validation error
     * @param $model
     * @param $data
     * @param null $id
     * @return void
     */
    #[OnFormSaving]
    public function __onFileSaving($model, $data, $id = null): void
    {
        $this->formColumnsCallbackOnType('file', function ($column) {
            $value = $this->formData[$column['key']] ?? null;
            if($value && !is_object($value)) {
                unset($this->formData[$column['key']]);
                $this->fileColumns[$column['key']] = $value;
            }
        });
    }

    /**
     * This function is called after the form is validated
     * It will store the file to the storage
     * @param $model
     * @param $data
     * @param null $id
     * @return void
     */
    #[OnFormValidated]
    public function __onFileSavingAfterValidated($model, $data, $id = null): void
    {
        $this->formColumnsCallbackOnType('file', function ($column) {
            $value = $this->formData[$column['key']] ?? null;
            if($value && is_object($value) && !is_string($value)) {
                // Determine disk per column option (fallback to default filesystem if not set)
                $disk = $column['option']['disk'] ?? config('filesystems.default');
                $this->formData[$column['key']] = CbUploader::uploadFromLivewire($value, $disk);
            }
        });
    }

    #[OnFormDehydrate]
    public function __onFileSavingDehydrate(): void
    {
        $this->formColumnsCallbackOnType('file', function ($column) {
            // If no new file uploaded and we have existing file, restore it
            $value = $this->formData[$column['key']] ?? null;
            if ((!$value || (is_string($value) && empty($value))) && isset($this->fileColumns[$column['key']])) {
                $this->formData[$column['key']] = $this->fileColumns[$column['key']];
            }
        });
    }

    /**
     * Handle for removing file, to show confirmation message
     * @param $columnKey
     * @return void
     */
    public function removeFile($columnKey) {
        $this->__fileDeleteKey = $columnKey;
        $this->__fileDeleteId = $this->formId;
        $this->showConfirmMessage("Delete Confirmation", "Are you sure you want to remove this file?", "removeFileConfirmed");

    }

    /**
     * Handle for removing file confirmed
     * @return void
     */
    public function removeFileConfirmed(): void
    {
        // Determine existing file path and disk per column
        $key = $this->__fileDeleteKey;
        $existingPath = $this->formData[$key] ?? ($this->fileColumns[$key] ?? null);
        $diskUsed = null;
        $this->formColumnsCallbackOnType('file', function ($column) use (&$diskUsed, $key) {
            if (($column['key'] ?? null) === $key) {
                $diskUsed = $column['option']['disk'] ?? null;
            }
        });
        $disk = $diskUsed ?? (config('cb.storage_disk') ?? config('filesystems.default'));

        // Delete physical file if exists
        if ($existingPath && is_string($existingPath)) {
            try {
                if (Storage::disk($disk)->exists($existingPath)) {
                    Storage::disk($disk)->delete($existingPath);
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to delete file: {$existingPath}", [
                    'error' => $e->getMessage(),
                    'disk' => $disk,
                    'key' => $key,
                ]);
            }
        }

        // Clear the value in database and UI state
        $this->modelService::updateWithData($this->__fileDeleteId, [$this->__fileDeleteKey => null]);
        $this->confirmMessageClose();
        $this->showAlertMessage("File removed successfully", "SUCCESS");
        $this->formData[$this->__fileDeleteKey] = null;
    }

    /**
     * Modify validation rules for file fields that have existing files
     * @param array $validationRules
     * @return array
     */
    public function modifyFileValidationRules(array $validationRules): array
    {
        $this->formColumnsCallbackOnType('file', function ($column) use (&$validationRules) {
            $fieldKey = 'formData.' . $column['key'];
            if (isset($validationRules[$fieldKey]) && isset($this->fileColumns[$column['key']])) {
                $rules = $validationRules[$fieldKey];
                // Remove 'required' rule if file already exists
                if (str_contains($rules, 'required')) {
                    $rules = str_replace('required|', '', $rules);
                    $rules = str_replace('|required', '', $rules);
                    $rules = str_replace('required', '', $rules);
                    $rules = trim($rules, '|');
                    
                    if (empty($rules)) {
                        unset($validationRules[$fieldKey]);
                    } else {
                        $validationRules[$fieldKey] = $rules;
                    }
                }
            }
        });
        
        return $validationRules;
    }
}
