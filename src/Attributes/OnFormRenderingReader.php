<?php

namespace CrudBooster\Attributes;

class OnFormRenderingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormRendering::class);
    }
}
