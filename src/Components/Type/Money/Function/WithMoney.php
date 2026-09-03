<?php

namespace CrudBooster\Components\Type\Money\Function;

use CrudBooster\Attributes\OnFormGetData;
use CrudBooster\Attributes\OnFormSaving;

trait WithMoney
{
    #[OnFormSaving]
    public function __moneyFormSaving($model, $data, $uuid = null): void
    {
        foreach ($this->getFormColumns(true) as $column) {
            if(isset($column['type']) && $column['type'] == 'money') {
                $this->formData[$column['key']] = str_replace(',', '', $this->formData[$column['key']]);
            }
        }
    }

    #[OnFormGetData]
    public function __moneyFormGetData($model, $data, $uuid = null): void
    {
        foreach ($this->getFormColumns(true) as $column) {
            if(isset($column['type']) && $column['type'] == 'money') {
                $value = preg_replace('/\D/', '', $this->formData[$column['key']]);
                $this->formData[$column['key']] = number_format((int) $value??0);
            }
        }
    }
}
