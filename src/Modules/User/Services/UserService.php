<?php

namespace CrudBooster\Modules\User\Services;

use CrudBooster\Domain\Services\BaseService;
use CrudBooster\Modules\User\Models\User;

class UserService extends BaseService
{
    protected static string $model = User::class;

    public static function createUser($name, $email, $password, $position, $phone)
    {
        $user = static::new();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->position = $position;
        $user->phone = $phone;
        $user->save();
        return $user;
    }

    public static function findByEmail($email)
    {
        return static::query()->where("email", $email)->first();
    }
}
