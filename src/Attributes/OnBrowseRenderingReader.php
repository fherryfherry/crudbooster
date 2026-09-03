<?php

namespace CrudBooster\Attributes;

class OnBrowseRenderingReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnBrowseRendering::class);
    }
}