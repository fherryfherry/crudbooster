<?php

namespace CrudBooster\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventBrowseRendering
{
    use Dispatchable, SerializesModels;

    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }
}
