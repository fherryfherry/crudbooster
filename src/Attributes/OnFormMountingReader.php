<?php

namespace CrudBooster\Attributes;

class OnFormMountingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormMounting::class);
    }
}