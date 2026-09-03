<?php

namespace CrudBooster\Attributes;

class OnPropertyUpdatedReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnPropertyUpdated::class);
    }
}