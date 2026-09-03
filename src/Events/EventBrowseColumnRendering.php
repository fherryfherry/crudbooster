<?php

namespace CrudBooster\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventBrowseColumnRendering
{
    use Dispatchable, SerializesModels;

    public $model;
    public $rowData;
    public $column;

    public function __construct($model, $rowData, $column)
    {
        $this->model = $model;
        $this->rowData = $rowData;
        $this->column = $column;
    }
}
