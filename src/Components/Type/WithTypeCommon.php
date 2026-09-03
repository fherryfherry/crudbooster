<?php

namespace CrudBooster\Components\Type;

use CrudBooster\Attributes\OnFormDehydrate;
use CrudBooster\Attributes\OnFormGetData;
use CrudBooster\Attributes\OnFormSaving;
use CrudBooster\Components\Type\Checkbox\Function\WithCheckbox;
use CrudBooster\Components\Type\DateTime\Function\WithDateTime;
use CrudBooster\Components\Type\File\Function\WithFileInput;
use CrudBooster\Components\Type\Image\Function\WithImageInput;
use CrudBooster\Components\Type\JsonChecklist\Function\WithJsonChecklist;
use CrudBooster\Components\Type\Money\Function\WithMoney;
use CrudBooster\Components\Type\Password\Function\WithPassword;
use CrudBooster\Components\Type\SelectChips\Function\WithSelectChips;

trait WithTypeCommon
{
    use WithSelectChips, WithImageInput, WithFileInput, WithJsonChecklist, WithCheckbox, WithDateTime, WithMoney, WithPassword;

    protected $typeOptions = ['uppercase', 'lowercase', 'noSpace', 'numeric', 'nonNumeric', 'numberFormat', 'phoneFormat'];

    /**
     * This method is used to intercept the form data before saving regarding type needs
     * @return void
     */
    #[OnFormSaving]
    public function typeInterceptFormData($model, $data, $uuid = null)
    {
        // This method is used to intercept the form data before saving regarding type needs
        foreach ($this->getFormColumns(true) as $column) {
            foreach ($this->typeOptions as $typeOption) {
                if (isset($column['option']) && is_array($column['option']) && ($column['option'][$typeOption] ?? false)) {
                    if (isset($this->formData[$column['key']])) {
                        $this->formData[$column['key']] = $column['option'][$typeOption]['onSave']($this->formData[$column['key']]);
                    }
                }
            }
        }
    }

    #[OnFormGetData]
    public function typeInterceptGetData($model, $data, $uuid = null)
    {
        $this->typeGetData();
    }

    #[OnFormDehydrate]
    public function typeDehydrate()
    {
        $this->typeGetData();
    }

    private function typeGetData()
    {
        // This method is used to intercept the form data before showing regarding type needs
        foreach ($this->getFormColumns(true) as $column) {
            foreach ($this->typeOptions as $typeOption) {
                if (isset($column['option']) && is_array($column['option']) && ($column['option'][$typeOption] ?? false)) {
                    if (isset($this->formData[$column['key']])) {
                        $this->formData[$column['key']] = $column['option'][$typeOption]['onView']($this->formData[$column['key']]);
                    }
                }
            }
        }
    }
}
