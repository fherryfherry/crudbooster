<?php

namespace CrudBooster\Components\TableActionButton;

use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;

trait WithTableActionButton
{
    use WithConfirmMessage;
    /**
     * Add table action button
     * @param string $label
     * @param string|null $iconSvg
     * @param string|null $url
     * @return TableActionButton
     */
    public function addTableActionButton(string $label, ?string $iconSvg = null, ?string $url = null) {
        $action = null;
        if($url) {
            $action = function() use ($url) {
                return redirect($url);
            };
        }
        return new TableActionButton([
            'id'=> hash('md5', $label),
            'label' => $label,
            'icon' => $iconSvg,
            'url' => $url,
            'action' => $action,
            'class' => 'btn btn-primary',
            'templateMode' => 'ICON_ONLY'
        ]);
    }

    public function __doActionTableActionButton($id, $confirmed = false)
    {
        $actionButtons = TableActionButton::__getOption();
        $button = collect($actionButtons)->where('id', $id)->first();
        if(!$button) {
            abort(404);
        }

        if(!$confirmed && isset($button['confirmation'])) {
            $this->showConfirmMessage($button['confirmation']['title'], $button['confirmation']['message'], "__doActionTableActionButton('$id', true)");
            return;
        }

        $callback = $button['action'];
        call_user_func($callback);
        $this->confirmMessageClose();
    }
}
