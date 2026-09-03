<?php

namespace CrudBooster\Modules\Role;

use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use CrudBooster\Modules\Role\Livewire\Role;
use CrudBooster\Modules\Role\Livewire\RoleForm;
use CrudBooster\Modules\User\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CBRoleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/router.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        Gate::define('is_super_admin', function ($user) {
            $user = User::query()->find($user->id);
            foreach ($user->roles as $r) {
                if($r->name == config('cb.super_admin_role')) return true;
            }
            return false;
        });
        Gate::define('browse', function ($user, string $moduleKey) {
            $user = User::query()->find($user->id);
            foreach ($user->roles as $r) {
                if($r->name == config('cb.super_admin_role')) return true;
                return $r->permissions[$moduleKey]['browse'] ?? false;
            }
            return false;
        });
        Gate::define('create', function ($user, string $moduleKey) {
            $user = User::query()->find($user->id);
            foreach ($user->roles as $r) {
                if($r->name == config('cb.super_admin_role')) return true;
                return $r->permissions[$moduleKey]['create'] ?? false;
            }
            return false;
        });
        Gate::define('read', function ($user, string $moduleKey) {
            $user = User::query()->find($user->id);
            foreach ($user->roles as $r) {
                if($r->name == config('cb.super_admin_role')) return true;
                return $r->permissions[$moduleKey]['read'] ?? false;
            }
            return false;
        });
        Gate::define('update', function ($user, string $moduleKey) {
            $user = User::query()->find($user->id);
            foreach ($user->roles as $r) {
                if($r->name == config('cb.super_admin_role')) return true;
                return $r->permissions[$moduleKey]['update'] ?? false;
            }
            return false;
        });
        Gate::define('delete', function ($user, string $moduleKey) {
            $user = User::query()->find($user->id);
            foreach ($user->roles as $r) {
                if($r->name == config('cb.super_admin_role')) return true;
                return $r->permissions[$moduleKey]['delete'] ?? false;
            }
            return false;
        });

        ModuleRegistrar::registerModule(
            key: 'role',
            name: 'Role Management',
            browseModuleClass: Role::class,
            formModuleClass: RoleForm::class,
            serviceProvider: self::class,
            additional: [
                'permissionAvailable' => RolePermission::all(),
            ]);
    }
}
