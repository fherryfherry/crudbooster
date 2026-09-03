<?php

namespace CrudBooster\Components\BulkAction;

use Closure;
use CrudBooster\Modules\Role\Enum\RolePermission;

class BulkAction
{
    private static array $bulkActions = [];

    public function __construct(
        private string $label,
        private ?string $icon = null,
        private ?Closure $action = null,
        private ?string $confirmTitle = null,
        private ?string $confirmText = null,
        private RolePermission $permission = RolePermission::UPDATE
    ) {}

    public static function add(string $label)
    {
        static::$bulkActions[$label] = new self($label);
        return static::$bulkActions[$label];
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function action(Closure $action): self
    {
        $this->action = $action;
        return $this;
    }
    public function confirm(string $title, string $text): self
    {
        $this->confirmTitle = $title;
        $this->confirmText = $text;
        return $this;
    }

    public function permission(RolePermission $permission): self
    {
        $this->permission = $permission;
        return $this;
    }

    public static function __getBulkActions()
    {
        return static::$bulkActions ? array_map(fn($bulk) => $bulk->__getValues(), static::$bulkActions) : [];
    }

    public function __getValues()
    {
        return [
            'id'=> uniqid(),
            'label' => $this->label,
            'icon' => $this->icon,
            'action' => $this->action,
            'confirm_title' => $this->confirmTitle ?? 'Are you sure?',
            'confirm_text' => $this->confirmText ?? 'You won\'t be able to revert this!',
            'permission' => $this->permission,
        ];
    }
}
