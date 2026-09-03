<?php

namespace CrudBooster\Components\TableActionButton;

class TableActionButtonOptions
{
    private array $options = [];

    public function __construct()
    {

    }

    public function setOption(string $label, array $option): void
    {
        $this->options[$label] = $option;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
