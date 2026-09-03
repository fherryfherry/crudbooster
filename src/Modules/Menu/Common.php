<?php

use CrudBooster\Modules\Menu\Services\CBMenuService;
use Illuminate\Support\Collection;

if(!function_exists('getMenus')) {
    function getMenus($tag = null) {
        return CBMenuService::getAllFilteredPermission($tag);
    }
}
if(!function_exists('getMenuTags')) {
    function getMenuTags(): Collection
    {
        return CBMenuService::tagList();
    }
}
