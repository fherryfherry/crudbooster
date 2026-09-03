<?php

namespace CrudBooster\Modules\Role\Enum;

enum RolePermission
{
    case CREATE;
    case READ;
    case UPDATE;
    case DELETE;

    public static function all(): array
    {
        return [
            self::CREATE,
            self::READ,
            self::UPDATE,
            self::DELETE,
        ];
    }
}
