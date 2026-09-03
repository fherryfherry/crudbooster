<?php

namespace CrudBooster\Attributes;

class OnFormMountedReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormMounted::class);
    }
}