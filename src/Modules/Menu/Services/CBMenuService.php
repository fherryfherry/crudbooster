<?php

namespace CrudBooster\Modules\Menu\Services;

use CrudBooster\Domain\Services\BaseService;
use CrudBooster\Modules\Menu\Models\CBMenu;
use CrudBooster\Modules\ModuleRegistrar;
use Illuminate\Support\Facades\Gate;
use Str;

class CBMenuService extends BaseService
{
    protected static string $model = CBMenu::class;
    protected static int $menuCacheDuration = 0;

    public static function createIfNotExists($data)
    {
        $menu = static::$model::where('name', $data['name'])
            ->where('menu_value', $data['menu_value'])
            ->first();
        if (!$menu) {
            $data['id'] = Str::uuid()->toString();
            $menu = static::create($data);
        }
        cache()->forget('cb_menu_all');
        return $menu;
    }

    /**
     * Get all tags
     * @return \Illuminate\Support\Collection
     */
    public static function tagList()
    {
        return static::query()->select('tag')->distinct()
            ->orderBy('tag', 'asc')
            ->get()->pluck('tag');
    }

    public static function getDashboardMenu()
    {
        $menu = static::query()
            ->where('is_dashboard', 1)
            ->first();
        if ($menu) {
            $menu = static::setMenuProperties($menu);
            $menu->child = static::getChildMenus($menu->id);
            return $menu;
        } else {
            return null;
        }
    }

    /**
     * Get all menus
     * @param $tag
     * @return mixed
     */
    public static function all($tag = null)
    {
        if (!$result = cache()->get('cb_menu_all')) {
            $result = static::query()
                ->when($tag != null, function ($query) use ($tag) {
                    return $query->where('tag', $tag);
                })
                ->when($tag == null, function ($query) {
                    return $query->whereNull('tag');
                })
                ->whereNull('parent_id')
                ->orderBy('menu_order')
                ->get();
            cache()->put('cb_menu_all', $result, static::$menuCacheDuration);
        }

        return $result->map(function ($item) {
            $item = static::setMenuProperties($item);
            $item->child = static::getChildMenus($item->id);
            return $item;
        });
    }

    public static function getAllFilteredPermission($tag = null)
    {
        $menus = static::all($tag);
        return $menus->filter(function ($menu) {
            return $menu->permission_allowed && static::isMenuAllowed($menu);
        });
    }

    private static function setMenuProperties($item)
    {
        $item->menu_url = "";
        if ($item->menu_type == 'MODULE') {
            $path = ModuleRegistrar::getModules()[$item->menu_value]['mainPath'] ?? '/';
            $item->menu_url = getCmsUrl($path);
            $item->wire_navigation = true;
            $item->menu_path = $path;
            $item->permission_allowed = Gate::check('read', $path);
        } else if ($item->menu_type == 'PAGE_BUILDER') {
            $item->menu_url = getCmsUrl('p/' . $item->menu_value);
            $item->wire_navigation = true;
            $item->permission_allowed = true;
        } else {
            $item->menu_url = $item->menu_value;
            $item->wire_navigation = false;
            $item->permission_allowed = true;
        }
        return $item;
    }

    private static function getChildMenus($parentId)
    {
        return static::query()
            ->where('parent_id', $parentId)
            ->orderBy('menu_order', 'asc')
            ->get()
            ->map(function ($child) {
                $child = static::setMenuProperties($child);
                $child->child = static::getChildMenus($child->id);
                return $child;
            });
    }

    private static function isMenuAllowed($menu)
    {
        return true;
    }
}
