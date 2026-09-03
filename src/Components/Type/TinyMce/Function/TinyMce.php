<?php

namespace CrudBooster\Components\Type\TinyMce\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class TinyMce extends TypeOptionAbstract
{

    /**
     * To set the height of the editor
     * @param int $height E.g: 300
     * @return $this
     */
    public function height(int $height): static
    {
        $this->option['height'] = $height;
        return $this;
    }
}
