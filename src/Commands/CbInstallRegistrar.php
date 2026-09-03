<?php

namespace CrudBooster\Commands;

class CbInstallRegistrar
{
    public static $data = [];

    // add
    public static function add($key, callable $callback)
    {
        $row = [];
        $row['key'] = $key;
        $row['callback'] = $callback;
        self::$data[$key] = $row;
    }

    // getAll
    public static function __getAll()
    {
        return self::$data ?? [];
    }
}
