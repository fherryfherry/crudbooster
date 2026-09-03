<?php

namespace CrudBooster\Components\Type\Checkbox\Function;

use CrudBooster\Attributes\OnFormGetData;
use CrudBooster\Attributes\OnFormMounted;

trait WithCheckbox
{
    private function checkboxInitData(): void
    {
        // This for regular crud, need to set default value for checkbox
        $this->formColumnsCallbackOnType('checkbox', function ($column) {
            try {
                $this->formData[$column['key']] = is_string($this->formData[$column['key']]) ? explode("|",$this->formData[$column['key']]) : $this->formData[$column['key']] ?? [];
            } catch (\Exception $e) {
                $this->formData[$column['key']] = [];
            }
        });
    }
    #[OnFormMounted]
    public function checkboxMount($model): void
    {
        $this->checkboxInitData();
    }

    #[OnFormGetData]
    public function checkboxGetData($model, $data, $uuid = null): void
    {
        $this->checkboxInitData();
    }

}
