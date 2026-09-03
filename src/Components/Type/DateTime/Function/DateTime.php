<?php

namespace CrudBooster\Components\Type\DateTime\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class DateTime extends TypeOptionAbstract
{
    /**
     * Set the date format
     * @param string $format E.g: Y-m-d H:i:s
     * @return $this
     */
    public function format(string $format): static
    {
        $this->option['format'] = $format;
        return $this;
    }
    /**
     * Set display timezone for form and detail rendering
     * @param string $timezone E.g: Asia/Jakarta
     * @return $this
     */
    public function toTimezone(string $timezone): static
    {
        $this->option['timezone'] = $timezone;
        return $this;
    }
}
