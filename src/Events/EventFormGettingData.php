<?php

namespace CrudBooster\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventFormGettingData
{
    use Dispatchable, SerializesModels;

    public $model;
    public $id;

    public function __construct($model, $id = null)
    {
        $this->model = $model;
        $this->id = $id;
    }
}
