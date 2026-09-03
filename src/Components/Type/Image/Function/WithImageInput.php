<?php

namespace CrudBooster\Components\Type\Image\Function;

use CrudBooster\Attributes\OnFormDehydrate;
use CrudBooster\Attributes\OnFormHydrate;
use CrudBooster\Attributes\OnFormSaving;
use CrudBooster\Attributes\OnFormValidated;
use CrudBooster\Attributes\OnPropertyUpdated;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Helpers\CbUploader;
use Livewire\WithFileUploads;

/**
 * Trait WithImageInput
 * 
 * Handles image input functionality for CrudBooster forms including:
 * - Image upload and storage
 * - Image data preservation during validation errors
 * - Image removal and confirmation
 * - Multiple image support
 * 
 * @package CrudBooster\Components\Type\Image\Function
 */
trait WithImageInput
{
    use WithFileUploads;
    use WithConfirmMessage;

    // ========================================
    // PROPERTIES
    // ========================================
    
    /**
     * Stores image data temporarily to prevent loss during validation
     * @var array
     */
    private array $imageColumns = [];
    
    /**
     * Image deletion confirmation properties
     */
    public ?string $__imageDeleteKey = null;
    public ?string $__imageDeleteId = null;
    public ?string $__imageDeleteUrl = null;

    // ========================================
    // FORM LIFECYCLE HOOKS
    // ========================================
    
    /**
     * Called before form saving to preserve image data
     * Removes string values from form data to prevent validation errors
     * 
     * @param mixed $model The model being saved
     * @param array $data Form data
     * @param string|null $uuid Optional UUID
     * @return void
     */
    #[OnFormSaving]
    public function __onImageSaving($model, $data, $uuid = null): void
    {
        $this->formColumnsCallbackOnType('image', function ($column) {
            $value = $this->formData[$column['key']] ?? null;
            $isMultiple = $column['option']['multiple'] ?? false;
            
            if ($value) {
                if (is_array($value)) {
                    $this->imageColumns[$column['key']] = $value;
                } elseif (!is_object($value)) {
                    // For single image, save as single value or array based on multiple option
                    $this->imageColumns[$column['key']] = $isMultiple ? [$value] : $value;
                    unset($this->formData[$column['key']]);
                }
            }
        });
    }

    /**
     * Called after form validation to upload images to storage
     * Restores saved image data and processes uploads
     * 
     * @param mixed $model The model being saved
     * @param array $data Form data
     * @param string|null $uuid Optional UUID
     * @return void
     */
    #[OnFormValidated]
    public function __onImageSavingAfterValidated($model, $data, $uuid = null): void
    {
        $this->formColumnsCallbackOnType('image', function ($column) {
            $values = $this->formData[$column['key']] ?? null;
            $savedValues = $this->imageColumns[$column['key']] ?? null;
            $isMultiple = $column['option']['multiple'] ?? false;
            
            // Restore saved images if current values are empty
            if (empty($values) && !empty($savedValues)) {
                $this->formData[$column['key']] = $savedValues;
                $values = $savedValues;
            }
            
            // Process different value types
            if ($values && is_array($values)) {
                $this->processArrayImages($column, $values);
            } elseif ($values && is_object($values) && !is_string($values)) {
                $this->processSingleImageUpload($column, $values);
            } elseif ($values && is_string($values)) {
                $this->processStringImage($column, $values, $isMultiple);
            }
        });
    }

    /**
     * Called during form dehydration to preserve image data
     * Prevents image data loss during validation errors
     * 
     * @return void
     */
    #[OnFormDehydrate]
    public function __onImageSavingDehydrate(): void
    {
        $this->formColumnsCallbackOnType('image', function ($column) {
            $currentValue = $this->formData[$column['key']] ?? null;
            $savedValue = $this->imageColumns[$column['key']] ?? null;
            $isMultiple = $column['option']['multiple'] ?? false;
            
            if (empty($currentValue) && !empty($savedValue)) {
                $this->formData[$column['key']] = $savedValue;
            }
        });
    }

    /**
     * Called during form hydration to restore image data
     * Restores image data after validation errors or component refresh
     * 
     * @return void
     */
    #[OnFormHydrate]
    public function __onImageSavingHydrate(): void
    {
        $this->formColumnsCallbackOnType('image', function ($column) {
            $currentValue = $this->formData[$column['key']] ?? null;
            $savedValue = $this->imageColumns[$column['key']] ?? null;
            $isMultiple = $column['option']['multiple'] ?? false;
            
            if (empty($currentValue) && !empty($savedValue)) {
                $this->formData[$column['key']] = $savedValue;
            }
        });
    }

    /**
     * Called when any property is updated to preserve image data
     * Handles image data preservation during validation errors
     * 
     * @return void
     */
    #[OnPropertyUpdated]
    public function __onImagePropertyUpdated(): void
    {
        $this->formColumnsCallbackOnType('image', function ($column) {
            $currentValue = $this->formData[$column['key']] ?? null;
            $savedValue = $this->imageColumns[$column['key']] ?? null;
            $isMultiple = $column['option']['multiple'] ?? false;
            
            // Preserve existing data if current value becomes empty
            if (empty($currentValue) && !empty($savedValue)) {
                $this->formData[$column['key']] = $savedValue;
            }
            
            // Save new image value for future preservation
            if (!empty($currentValue) && !is_object($currentValue)) {
                $this->imageColumns[$column['key']] = $currentValue;
            }
        });
    }

    /**
     * Called when validation error occurs to preserve image data
     * Invoked from BaseFormComponent when ValidationException is caught
     * 
     * @return void
     */
    public function __onImageSavingAfterValidationError(): void
    {
        $this->formColumnsCallbackOnType('image', function ($column) {
            $savedValue = $this->imageColumns[$column['key']] ?? null;
            $isMultiple = $column['option']['multiple'] ?? false;
            
            if (!empty($savedValue)) {
                $this->formData[$column['key']] = $savedValue;
            }
        });
    }

    // ========================================
    // IMAGE REMOVAL METHODS
    // ========================================
    
    /**
     * Initiate image removal with confirmation dialog
     * 
     * @param string $columnKey The column key for the image
     * @param string $imageUrl The URL of the image to remove
     * @return void
     */
    public function removeImage($columnKey, $imageUrl): void
    {
        $this->__imageDeleteKey = $columnKey;
        $this->__imageDeleteId = $this->formId;
        $this->__imageDeleteUrl = $imageUrl;
        $this->showConfirmMessage("Delete Confirmation", "Are you sure you want to remove this image?", "removeImageConfirmed");
    }

    /**
     * Remove temporary image from form data
     * 
     * @param string $columnKey The column key for the image
     * @param int|null $index Optional index for array images
     * @return void
     */
    public function removeTempImage($columnKey, $index = null): void
    {
        if ($index !== null) {
            $this->formData[$columnKey][$index] = null;
            // Reindex array to remove null values
            $this->formData[$columnKey] = array_values(array_filter($this->formData[$columnKey]));
        } else {
            $this->formData[$columnKey] = null;
        }
    }

    /**
     * Confirm and execute image removal
     * Called after user confirms deletion
     * 
     * @return void
     */
    public function removeImageConfirmed(): void
    {
        $data = $this->modelService::findById($this->__imageDeleteId);
        if (!$data) {
            $this->showAlertMessage("Data not found", "ERROR");
            return;
        }
        
        if ($data->{$this->__imageDeleteKey} && is_array($data->{$this->__imageDeleteKey})) {
            // Remove specific image from array
            $data->{$this->__imageDeleteKey} = array_values(array_filter($data->{$this->__imageDeleteKey}, function($image) {
                return $image !== $this->__imageDeleteUrl;
            }));
            $data->save();
            $this->formData[$this->__imageDeleteKey] = array_values($data->{$this->__imageDeleteKey});
        } else {
            // Remove single image
            $this->modelService::updateWithData($this->__imageDeleteId, [$this->__imageDeleteKey => null]);
            $this->formData[$this->__imageDeleteKey] = null;
        }
        
        $this->confirmMessageClose();
        $this->showAlertMessage("Image removed successfully", "SUCCESS");
    }
    
    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================
    
    /**
     * Process array of images (multiple image upload)
     * 
     * @param array $column Column configuration
     * @param array $values Array of image values
     * @return void
     */
    private function processArrayImages($column, $values): void
    {
        $uploadedPaths = [];
        foreach ($values as $value) {
            if (is_string($value) || is_object($value)) {
                $uploadedPaths[] = is_string($value) ? $value : CbUploader::uploadFromLivewire($value);
            }
        }

        // Filter only string values (uploaded paths)
        $uploadedPaths = array_values(array_filter($uploadedPaths, function($value) {
            return is_string($value);
        }));

        $this->formData[$column['key']] = array_merge($this->formData[$column['key']] ?? [], $uploadedPaths);
        $this->formData[$column['key']] = array_values(array_filter($this->formData[$column['key']], function($value) {
            return is_string($value);
        }));
        $this->formData[$column['key']] = array_values(array_unique($this->formData[$column['key']]));
    }
    
    /**
     * Process single image upload (object)
     * 
     * @param array $column Column configuration
     * @param mixed $value Image object to upload
     * @return void
     */
    private function processSingleImageUpload($column, $value): void
    {
        $this->formData[$column['key']] = CbUploader::uploadFromLivewire($value);
    }
    
    /**
     * Process string image value (already uploaded)
     * 
     * @param array $column Column configuration
     * @param string $value Image string value
     * @param bool $isMultiple Whether multiple images are allowed
     * @return void
     */
    private function processStringImage($column, $value, $isMultiple): void
    {
        $this->formData[$column['key']] = $isMultiple ? [$value] : $value;
    }
}
