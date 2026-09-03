<?php

namespace CrudBooster\Attributes;

class OnFormGettingDataReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormGettingData::class);
    }
}
