<?php

namespace CrudBooster\Attributes;

class OnBrowseHydrateReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnBrowseHydrate::class);
    }
}
