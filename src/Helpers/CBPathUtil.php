<?php

namespace CrudBooster\Helpers;

class CBPathUtil {
    public static function getCmsPath($path = null): string {
        if($path) return config("cb.admin_path",'cms')."/".trim($path,"/");
        else return config("cb.admin_path", "cms");
    }
}