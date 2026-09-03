<?php

namespace CrudBooster\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class OnFormGetData
{
    public function __construct(public int $order = 0) {}
}