<?php

namespace CrudBooster\Attributes;

class OnBrowseQueryCreatingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnBrowseQueryCreating::class);
    }
}
