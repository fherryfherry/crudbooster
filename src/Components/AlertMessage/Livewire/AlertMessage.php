<?php

namespace CrudBooster\Components\AlertMessage\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class AlertMessage extends Component
{
    public $type; // success, error, warning, info
    public $message;
    public $position; // TOP_CENTER, TOP_RIGHT, BOTTOM_RIGHT

    public function render()
    {
        return view('cb.alert::alert_template',[
            'type' => $this->type,
            'message' => $this->message,
            'position' => $this->position ?? 'TOP_RIGHT',
        ]);
    }

    #[On('showAlertMessage')]
    public function showAlertMessage($message, $type = 'success', $position = 'TOP_RIGHT')
    {
        $this->type = $type;
        $this->message = $message;
        $this->position = $position;
    }

    #[On('closeAlertMessage')]
    public function closeAlertMessage()
    {
        $this->type = null;
        $this->message = null;
        $this->position = null;
    }
}
