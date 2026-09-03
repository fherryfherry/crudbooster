<?php

namespace CrudBooster\Livewire\ColumnBuilder;

trait WithTransformCaller
{
    public static function __callMethod($enum)
    {
        return static::{$enum}();
    }
}