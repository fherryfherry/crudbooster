<?php

namespace CrudBooster\Attributes;

class OnFormValidatedReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormValidated::class);
    }
}