<?php

namespace CrudBooster\Modules\AuditLog;

use CrudBooster\Events\EventDataDeleted;
use CrudBooster\Events\EventDataDeleting;
use CrudBooster\Events\EventFormSaved;
use CrudBooster\Events\EventFormSaving;
use CrudBooster\Modules\AuditLog\Listeners\AuditEventSubscriber;
use CrudBooster\Modules\AuditLog\Livewire\AuditLogList;
use CrudBooster\Modules\Menu\Models\CBMenu;
use CrudBooster\Modules\Menu\Services\CBMenuService;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use CrudBooster\Modules\Auth\Events\LoginAttemptFailed;
use CrudBooster\Modules\Auth\Events\LoginAttemptSuccess;
use CrudBooster\Modules\Auth\Events\LogoutSuccess;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CbAuditLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.audit-log');
        $this->loadTranslationsFrom(__DIR__ . '/Lang', 'cb');
        $this->loadRoutesFrom(__DIR__ . '/router.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Lang' => resource_path('lang'),
            ], 'cb-lang');
        }

        $this->registerEventSubscriber();

        try {
            if (Schema::hasTable('cb_menus')) {
                $this->ensureAuditLogMenu();
            }
        } catch (\Throwable) {
            // Ignore bootstrap timing issue before DB is ready.
        }

        ModuleRegistrar::registerModule(
            key: 'audit-log',
            name: 'Audit Log',
            browseModuleClass: AuditLogList::class,
            formModuleClass: AuditLogList::class,
            serviceProvider: self::class,
            additional: [
                'permissionAvailable' => RolePermission::all(),
            ]
        );
    }

    private function registerEventSubscriber(): void
    {
        Event::listen(EventFormSaving::class, [AuditEventSubscriber::class, 'onFormSaving']);
        Event::listen(EventFormSaved::class, [AuditEventSubscriber::class, 'onFormSaved']);
        Event::listen(EventDataDeleting::class, [AuditEventSubscriber::class, 'onDataDeleting']);
        Event::listen(EventDataDeleted::class, [AuditEventSubscriber::class, 'onDataDeleted']);
        Event::listen(LoginAttemptSuccess::class, [AuditEventSubscriber::class, 'onLoginSuccess']);
        Event::listen(LoginAttemptFailed::class, [AuditEventSubscriber::class, 'onLoginFailed']);
        Event::listen(LogoutSuccess::class, [AuditEventSubscriber::class, 'onLogoutSuccess']);
    }

    private function ensureAuditLogMenu(): void
    {
        $desiredOrder = $this->resolveMenuOrder();
        $existing = CBMenu::query()
            ->where('menu_type', 'MODULE')
            ->where('menu_value', 'audit-log')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $menu = $existing->shift();

        if (! $menu) {
            CBMenu::query()
                ->where('tag', 'Tools')
                ->where('menu_order', '>=', $desiredOrder)
                ->increment('menu_order');

            CBMenuService::createIfNotExists([
                'icon' => 'BAR',
                'parent_id' => null,
                'name' => 'Audit Log',
                'menu_type' => 'MODULE',
                'menu_value' => 'audit-log',
                'menu_order' => $desiredOrder,
                'tag' => 'Tools',
                'is_dashboard' => 0,
            ]);

            cache()->forget('cb_menu_all');

            return;
        }

        foreach ($existing as $duplicate) {
            $duplicate->delete();
        }

        $menu->update([
            'icon' => 'BAR',
            'parent_id' => null,
            'name' => 'Audit Log',
            'menu_type' => 'MODULE',
            'menu_value' => 'audit-log',
            'menu_order' => $desiredOrder,
            'tag' => 'Tools',
            'is_dashboard' => 0,
        ]);

        cache()->forget('cb_menu_all');
    }

    private function resolveMenuOrder(): int
    {
        $apiBuilderOrder = CBMenu::query()
            ->where('menu_type', 'MODULE')
            ->where('menu_value', 'api-builder')
            ->value('menu_order');

        if ($apiBuilderOrder) {
            return (int) $apiBuilderOrder + 1;
        }

        return (int) (CBMenu::query()->max('menu_order') ?? 0) + 1;
    }
}
