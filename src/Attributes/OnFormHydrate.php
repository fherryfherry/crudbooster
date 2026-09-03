<?php

namespace CrudBooster\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class OnFormHydrate
{
    public function __construct(public int $order = 0) {}
}