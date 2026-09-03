<?php

namespace CrudBooster\Attributes;

class OnFormSavingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormSaving::class);
    }
}