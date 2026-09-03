<?php

namespace CrudBooster\Livewire\FormBuilder;

interface FormContract
{
    public function get();

    public static function add($label,
                               $key,
                               $type = 'text',
                               $validation = null,
                               $placeholder = null,
                               $helpText = null,
                               $readonly = false,
                               $bindValue = null,
                               $option = []): Form;
}