<?php
if(!function_exists('confirmMessageTag')) {
    /**
     * To create alert message tag
     * @param $title
     * @param $message
     * @param string|array $action
     * @param string $confirmButtonText
     * @param string $confirmButtonColor
     * @return string
     */
    function confirmMessageTag($title, $message, string|array $action, string $confirmButtonText = 'Ok', string $confirmButtonColor = 'danger'): string
    {
        return view('cb.components::ConfirmMessage.view.message', ['message' => $message, 'title'=>$title, 'action'=>$action, 'confirmButtonText'=>$confirmButtonText, 'confirmButtonColor'=> $confirmButtonColor])->render();
    }
}
