<?php

namespace CrudBooster\Attributes;

class OnDraggedReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnDragged::class);
    }
}
