<?php

namespace CrudBooster\Components\Type\Summernote\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class Summernote extends TypeOptionAbstract
{
    /**
     * Set the editor height
     * @param int $height Height in pixels
     * @return $this
     */
    public function height(int $height): static
    {
        $this->option['height'] = $height;
        return $this;
    }

    /**
     * Enable auto-reformat for pasted content
     * @param bool $enabled
     * @return $this
     */
    public function autoReformat(bool $enabled = true): static
    {
        $this->option['auto_reformat'] = $enabled ? 'true' : 'false';
        return $this;
    }
} 