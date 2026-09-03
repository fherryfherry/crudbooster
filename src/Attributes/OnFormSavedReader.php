<?php

namespace CrudBooster\Attributes;

class OnFormSavedReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormSaved::class);
    }
}