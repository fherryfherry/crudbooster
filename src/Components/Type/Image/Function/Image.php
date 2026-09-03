<?php

namespace CrudBooster\Components\Type\Image\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class Image extends TypeOptionAbstract
{
    /**
     * To allow multiple image uploads
     * @return $this
     */
    public function multiple()
    {
        $this->option['multiple'] = true;
        return $this;
    }
}
