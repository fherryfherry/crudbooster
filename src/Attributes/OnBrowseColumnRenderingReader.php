<?php

namespace CrudBooster\Attributes;

class OnBrowseColumnRenderingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnBrowseColumnRendering::class);
    }
}