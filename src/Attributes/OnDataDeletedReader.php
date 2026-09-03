<?php

namespace CrudBooster\Attributes;

class OnDataDeletedReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnDataDeleted::class);
    }
}
