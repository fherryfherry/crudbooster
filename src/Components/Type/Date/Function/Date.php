<?php

namespace CrudBooster\Components\Type\Date\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class Date extends TypeOptionAbstract
{
    /**
     * Set the date format
     * @param string $format E.g: Y-m-d
     * @return $this
     */
    public function format(string $format): static
    {
        $this->option['format'] = $format;
        return $this;
    }
}
