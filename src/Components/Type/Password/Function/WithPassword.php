<?php

namespace CrudBooster\Components\Type\Password\Function;

use CrudBooster\Attributes\OnFormGetData;

trait WithPassword
{
    #[OnFormGetData]
    public function __passwordFormGetData(): void
    {
        $this->formColumnsCallbackOnType('password', function ($column) {
            $this->formData[$column['key']] = null;
        });
    }
}
