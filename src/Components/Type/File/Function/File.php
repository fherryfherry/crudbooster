<?php

namespace CrudBooster\Components\Type\File\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class File extends TypeOptionAbstract
{
    /**
     * Set the accept types for the file input
     * @param string $acceptTypes
     * @return $this
     */
    public function accept(string $acceptTypes): self
    {
        $this->option['accept'] = $acceptTypes;
        return $this;
    }

    /**
     * Set the storage disk for this file input (e.g., 's3', 'public')
     * @param string $disk
     * @return $this
     */
    public function disk(string $disk): self
    {
        $this->option['disk'] = $disk;
        return $this;
    }
}
