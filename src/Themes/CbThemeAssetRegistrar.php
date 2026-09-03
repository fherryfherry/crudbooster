<?php

namespace CrudBooster\Themes;

class CbThemeAssetRegistrar
{
    private static $dataJs = [];
    private static $dataCss = [];

    // add
    public static function addCss($cssUrl)
    {
        self::$dataCss[] = $cssUrl;
        // filter
        self::$dataCss = array_unique(self::$dataCss, SORT_REGULAR);
    }

    public static function addJs($jsUrl)
    {
        self::$dataJs[] = $jsUrl;
        // filter
        self::$dataJs = array_unique(self::$dataJs, SORT_REGULAR);
    }

    public static function __getDataJs()
    {
        return self::$dataJs;
    }
    public static function __getDataCss()
    {
        return self::$dataCss;
    }

}
