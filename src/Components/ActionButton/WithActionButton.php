<?php

namespace CrudBooster\Components\ActionButton;

use Closure;
use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\Icon\Icon;
use CrudBooster\Modules\Role\Enum\PermissionValue;
use CrudBooster\Modules\Role\Enum\RolePermission;
use Log;

trait WithActionButton
{
    use WithAlertMessage;

    /**
     * To add action button
     * @param string|array $label
     * @param Closure|string $urlOrActionCallback
     * @param string|null $icon
     * @return ActionButton
     */
    public function addActionButton(
        string|array $label,
        Closure|string $urlOrActionCallback,
        ?string $icon = null
    ): ActionButton {
        $permission = null;
        if (is_array($label) && isset($label['permission'])) {
            if (is_array($label['permission'])) {
                $permission = collect($label['permission'])->map(fn($p) => PermissionValue::valueOf($p))->toArray();
            } else {
                $permission = PermissionValue::valueOf($label['permission']);
            }
        }

        return new ActionButton([
            'label' => is_array($label) ? $label['label'] : $label,
            'url' => is_array($label) ? $label['url'] : $urlOrActionCallback,
            'icon' => is_array($label) ? Icon::valueOf($label['icon']) : Icon::valueOf($icon),
            'class' => is_array($label) ? $label['class'] : 'btn btn-primary',
            'target' => is_array($label) ? $label['target'] : '_self',
            'confirm' => is_array($label) ? $label['confirm'] : false,
            'templateMode' => is_array($label) ? $label['templateMode'] : 'ICON_ONLY',
            'permission' => $permission,
            'visible' => true
        ]);
    }

    private function actionButtonOverriding($row, $withCallback = false): array
    {
        // Get action buttons filtered by current module key with safe fallback
        $moduleKey = $this->moduleKey ?? $this->browsePath ?? 'default';

        // Get buttons for current module only
        $buttons = ActionButton::__getOption();

        // Check permission on each button
        $buttons = $this->permissionFilter($buttons, $this->module);
        // Callback visible
        $buttons = collect($buttons)->map(function ($button) use ($row) {
            if ($button['visible'] instanceof Closure || is_callable($button['visible'])) {
                $button['visible'] = $button['visible']($row);
            }
            return $button;
        })->toArray();

        // Filter only visible
        $buttons = collect($buttons)->toArray();
        $result = [];
        foreach ($buttons as $button) {
            if ($button['url'] instanceof Closure || is_callable($button['url'])) {
                $button['callback'] = ($withCallback) ? $button['url'] : null;
                $button['url'] = "javascript:";
                $button['is_callable'] = true;
                $button['callable_name'] = md5($button['label']);
            } else {
                $button['url'] = preg_replace_callback('/\{(\w+)}/', function ($matches) use ($row) {
                    return $row->{$matches[1]};
                }, $button['url']);
            }
            $result[] = $button;
        }
        return $result;
    }

    public function __doActionButtonConfirm($callableName, $id)
    {
        $this->showConfirmMessage('Action Confirmation', 'Are you sure you want to execute this action?', '__doActionButton("' . $callableName . '","' . $id . '")', 'Yes');
    }

    public function __doActionButtonCallback($callableName, $id, $confirm = false)
    {
        if (config('cb.demo_mode')) {
            Log::debug("__doActionButtonCallback :: demo mode");
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $browsePath = $this->browsePath ?? 'dashboard';
            $this->redirectIntended(getCmsUrl($browsePath), navigate: true);
            return;
        }

        $row = $this->modelService::findById($id);
        $button = collect($this->actionButtonOverriding($row, true))->firstWhere('callable_name', $callableName);

        if (!$button) {
            Log::debug("__doActionButtonCallback :: button not found :: " . $callableName . ", id = " . $id);
            $this->showAlertMessage('Button not found!', 'danger');
            $browsePath = $this->browsePath ?? 'dashboard';
            $this->redirect(getCmsUrl($browsePath), navigate: true);
            return;
        }

        if ($button['confirm'] && !$confirm) {
            $this->showConfirmMessage('Action Confirmation', 'Are you sure you want to `' . $button['label'] . '`?', '__doActionButtonCallback("' . $callableName . '","' . $id . '", true)', 'Yes');
            return;
        }

        $this->confirmMessageClose();

        // execute callback
        call_user_func($button['callback'], $row);
    }

    public function __doActionButtonRedirect($url, $withConfirmation = false, $buttonLabel = '', $confirm = false): void
    {
        if ($withConfirmation && !$confirm) {
            $this->showConfirmMessage('Action Confirmation', 'Are you sure you want to `' . $buttonLabel . '`?', '__doActionButtonRedirect("' . $url . '", true, "' . $buttonLabel . '", true)', 'Yes');
            return;
        }
        $this->redirect($url, navigate: true);
    }

    private function permissionFilter(array $buttons, array $module): array
    {
        return collect($buttons)->map(function ($button) use ($module) {
            // if permission is not set, then return as is
            if (!isset($button['permission'])) {
                return $button;
            }

            $permission = is_array($button['permission']) ? $button['permission'] : [$button['permission']];
            foreach ($permission as $p) {
                if (isset($button['permission'])) {
                    if ($p == RolePermission::CREATE && auth()->user()->can('create', $module['key'])) {
                        $button['visible'] = true;
                    } elseif ($p == RolePermission::READ && auth()->user()->can('read', $module['key'])) {
                        $button['visible'] = true;
                    } elseif ($p == RolePermission::UPDATE && auth()->user()->can('update', $module['key'])) {
                        $button['visible'] = true;
                    } elseif ($p == RolePermission::DELETE && auth()->user()->can('delete', $module['key'])) {
                        $button['visible'] = true;
                    } else {
                        $button['visible'] = false;
                    }
                }
            }

            return $button;
        })->values()->toArray();
    }

    // #[OnBrowseRendering]
    public function renderingWithActionButton(): void
    {
        if (!isset($this->result)) return;
        if (!empty($this->buttonAction) && isset($this->result)) {
            $this->result->getCollection()->transform(function ($row) {
                $row->__actionButton = $this->actionButtonOverriding($row);
                // filter visible
                $row->__actionButton = collect($row->__actionButton)->filter(fn($button) => $button['visible'])->toArray();
                return $row;
            });
        }
    }
}
