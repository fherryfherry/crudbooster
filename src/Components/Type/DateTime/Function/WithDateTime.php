<?php

namespace CrudBooster\Components\Type\DateTime\Function;

use CrudBooster\Attributes\OnFormGetData;
use CrudBooster\Attributes\OnFormSaving;
use Carbon\Carbon;

trait WithDateTime
{
    #[OnFormSaving]
    public function __datetimeFormSaving($model, $data, $uuid = null): void
    {
        foreach ($this->getFormColumns(true) as $column) {
            if(isset($column['type']) && $column['type'] == 'datetime') {
                // Convert display timezone back to app timezone before saving
                if (!empty($this->formData[$column['key']])) {
                    $appTz = config('app.timezone') ?? 'UTC';
                    $displayTz = $column['option']['timezone'] ?? null;
                    $inputValue = $this->formData[$column['key']];
                    if ($displayTz) {
                        $carbon = Carbon::parse($inputValue, $displayTz)->setTimezone($appTz);
                    } else {
                        $carbon = Carbon::parse($inputValue, $appTz);
                    }
                    $this->formData[$column['key']] = $carbon->format('Y-m-d H:i:s');
                }
            }
        }
    }

    #[OnFormGetData]
    public function __datetimeFormGetData($model, $data, $uuid = null): void
    {
        // Only convert for form (create/edit) view to avoid double timezone conversion on detail view
        if (isset($this->__view) && isset($this->viewForm) && $this->__view !== $this->viewForm) {
            return;
        }
        foreach ($this->getFormColumns(true) as $column) {
            if(isset($column['type']) && $column['type'] == 'datetime') {
                if (!empty($this->formData[$column['key']])) {
                    // Convert app timezone (stored) to display timezone for HTML input
                    $appTz = config('app.timezone') ?? 'UTC';
                    $displayTz = $column['option']['timezone'] ?? null;
                    $value = $this->formData[$column['key']];
                    $carbon = Carbon::parse($value, $appTz);
                    if ($displayTz) {
                        $carbon->setTimezone($displayTz);
                    }
                    // Format for datetime-local input
                    $this->formData[$column['key']] = $carbon->format('Y-m-d\TH:i');
                }
            }
        }
    }
}
