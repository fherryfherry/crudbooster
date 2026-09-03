<?php

namespace CrudBooster\Attributes;

class OnFormInitReader
{
    public static function getMethods($clazz): array
    {
        return AttrReader::getOrderedMethods($clazz, OnFormInit::class);
    }
}