<?php

namespace CrudBooster\Modules\ApiBuilder;

use CrudBooster\Modules\ApiBuilder\Http\ApiBuilderController;
use CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderList;
use CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderCreate;
use CrudBooster\Modules\Menu\Models\CBMenu;
use CrudBooster\Modules\Menu\Services\CBMenuService;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CbApiBuilderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'cb.api-builder');
        $this->loadTranslationsFrom(__DIR__ . '/Lang', 'cb');
        $this->loadRoutesFrom(__DIR__ . '/router.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Lang' => resource_path('lang'),
            ], 'cb-lang');
        }

        $this->registerDynamicApiRoutes();

        try {
            if (Schema::hasTable('cb_menus')) {
                $this->ensureApiBuilderMenu();
            }
        } catch (\Throwable) {
            // Skip menu registration before database bootstrap is ready.
        }

        ModuleRegistrar::registerModule(
            key: 'api-builder',
            name: 'API Builder',
            browseModuleClass: ApiBuilderList::class,
            formModuleClass: ApiBuilderCreate::class,
            serviceProvider: self::class,
            additional: [
                'permissionAvailable' => RolePermission::all(),
            ],
        );
    }

    private function registerDynamicApiRoutes(): void
    {
        ApiBuilderController::registerDynamicRoutes();
    }

    private function ensureApiBuilderMenu(): void
    {
        $desiredOrder = $this->resolveMenuOrder();
        $existing = CBMenu::query()
            ->where('menu_type', 'MODULE')
            ->where('menu_value', 'api-builder')
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
                'icon' => 'BOLT',
                'parent_id' => null,
                'name' => 'API Builder',
                'menu_type' => 'MODULE',
                'menu_value' => 'api-builder',
                'menu_order' => $desiredOrder,
                'tag' => 'Tools',
                'is_dashboard' => 0,
            ]);

            return;
        }

        foreach ($existing as $duplicate) {
            $duplicate->delete();
        }

        $menu->update([
            'icon' => 'BOLT',
            'parent_id' => null,
            'name' => 'API Builder',
            'menu_type' => 'MODULE',
            'menu_value' => 'api-builder',
            'menu_order' => $desiredOrder,
            'tag' => 'Tools',
            'is_dashboard' => 0,
        ]);

        cache()->forget('cb_menu_all');
    }

    private function resolveMenuOrder(): int
    {
        $queryBuilderOrder = CBMenu::query()
            ->where('menu_type', 'MODULE')
            ->where('menu_value', 'query-builder')
            ->value('menu_order');

        if ($queryBuilderOrder) {
            return (int) $queryBuilderOrder + 1;
        }

        $pageBuilderOrder = CBMenu::query()
            ->where('menu_type', 'MODULE')
            ->where('menu_value', 'page-builder')
            ->value('menu_order');

        if ($pageBuilderOrder) {
            return (int) $pageBuilderOrder;
        }

        return (int) (CBMenu::query()->max('menu_order') ?? 0) + 1;
    }
}
