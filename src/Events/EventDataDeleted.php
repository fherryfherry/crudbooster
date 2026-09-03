<?php

namespace CrudBooster\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventDataDeleted
{
    use Dispatchable, SerializesModels;

    public $model;
    public $data;
    public $id;

    public function __construct($model, $data, $id = null)
    {
        $this->model = $model;
        $this->data = $data;
        $this->id = $id;
    }
}
