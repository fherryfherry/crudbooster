<?php

namespace CrudBooster\Attributes;

class OnBrowseMountingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnBrowseMounting::class);
    }
}
