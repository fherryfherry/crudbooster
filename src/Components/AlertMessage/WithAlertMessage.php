<?php

namespace CrudBooster\Components\AlertMessage;

use CrudBooster\Attributes\OnBrowseMounting;

trait WithAlertMessage
{
    /**
     * @param string $message
     * @param string $type
     * @param string $position
     * @return void
     */
    public function showAlertMessage(string $message, string $type = "success", string $position = 'TOP_RIGHT'): void
    {
        session()->flash("message", $message);
        session()->flash("messageType", $type);
        session()->flash("messagePosition", $position);
        $this->dispatch('showAlertMessage', $message, $type, $position);
    }


    #[OnBrowseMounting]
    public function __alertMessageBrowseMounting()
    {
        if(session()->has("message")) {
            $message = session("message");
            $type = session("messageType", "success");
            $position = session("messagePosition", "TOP_RIGHT");
            $this->dispatch('showAlertMessage', $message, $type, $position);
        }
    }
}
