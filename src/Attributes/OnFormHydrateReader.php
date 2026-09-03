<?php

namespace CrudBooster\Attributes;

class OnFormHydrateReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormHydrate::class);
    }
}