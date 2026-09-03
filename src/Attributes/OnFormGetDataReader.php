<?php

namespace CrudBooster\Attributes;

class OnFormGetDataReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormGetData::class);
    }
}