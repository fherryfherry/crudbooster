<?php

namespace CrudBooster\Components\ConfirmMessage;

trait WithConfirmMessage
{
    public $confirmTitle;
    public $confirmMessage;
    public $confirmButtonText;
    public $confirmButtonColor;
    public $confirmAction;

    /**
     * To show confirm message
     * @param string $title
     * @param string $message
     * @param string|array $confirmAction
     * @param string $confirmButtonText
     * @param string $confirmButtonColor
     * @return void
     */
    public function showConfirmMessage(string $title, string $message, string|array $confirmAction, string $confirmButtonText = 'Ok', string $confirmButtonColor = 'danger'): void
    {
        $this->confirmTitle = $title;
        $this->confirmMessage = $message;
        $this->confirmButtonText = $confirmButtonText;
        $this->confirmButtonColor = $confirmButtonColor;
        $this->confirmAction = $confirmAction;
    }

    /**
     * To close confirm message
     * @return void
     */
    public function confirmMessageClose()
    {
        $this->confirmTitle = null;
        $this->confirmMessage = null;
        $this->confirmButtonText = null;
        $this->confirmButtonColor = null;
        $this->confirmAction = null;
    }
}
