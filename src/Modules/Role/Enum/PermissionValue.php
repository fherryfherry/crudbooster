<?php

namespace CrudBooster\Modules\Role\Enum;

class PermissionValue
{
    public static function valueOf(string $key): ?RolePermission
    {
        return match (strtoupper($key)) {
            'CREATE' => RolePermission::CREATE,
            'READ' => RolePermission::READ,
            'UPDATE' => RolePermission::UPDATE,
            'DELETE' => RolePermission::DELETE,
            default => null,
        };
    }
}
