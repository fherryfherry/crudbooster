<?php

namespace CrudBooster\Attributes;

class OnFormDehydrateReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormDehydrate::class);
    }
}