<?php

namespace CrudBooster\Attributes;

class OnDataDeletingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnDataDeleting::class);
    }
}
