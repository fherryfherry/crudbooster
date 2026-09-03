<?php

namespace CrudBooster\Components\Type\TextArea\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class TextArea extends TypeOptionAbstract
{
    public function heightRow(int $height = 5): self
    {
        $this->option = array_merge($this->option, [
            'heightRow' => $height,
        ]);
        return $this;
    }
}
