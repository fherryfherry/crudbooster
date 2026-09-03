<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use Illuminate\Support\Facades\DB;

trait WithDbSchema
{

    /**
     * To determine if a column is auto increment
     * @param $table
     * @param $column
     * @return bool
     */
    private function isAutoIncrement($table, $column)
    {
        $autoIncrement = null;
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $autoIncrement = DB::select("SHOW COLUMNS FROM $table WHERE Extra = 'auto_increment' AND Field = '$column'");
                break;
            case 'sqlite':
                $autoIncrement = DB::select("PRAGMA table_info($table)");
                $autoIncrement = array_filter($autoIncrement, function ($col) use ($column) {
                    return $col->name == $column && $col->pk == 1;
                });
                break;
            case 'pgsql':
                $autoIncrement = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = '$table' AND column_default LIKE 'nextval%' AND column_name = '$column'");
                break;
            case 'sqlsrv':
                $autoIncrement = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMNPROPERTY(object_id(TABLE_NAME), COLUMN_NAME, 'IsIdentity') = 1 AND COLUMN_NAME = '$column'");
                break;
        }

        return !empty($autoIncrement);
    }

    /**
     * To get the length of a column
     * @param $table
     * @param $column
     * @return array|string|null
     */
    private function getColumnLength($table, $column)
    {
        $length = null;
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $columnInfo = DB::select("SHOW COLUMNS FROM $table WHERE Field = '$column'");
                $type = $columnInfo[0]->Type;
                preg_match('/\((\d+)\)/', $type, $matches);
                $length = $matches[1] ?? null;
                break;
            case 'sqlite':
                $columns = DB::select("PRAGMA table_info($table)");
                foreach ($columns as $col) {
                    if ($col->name == $column) {
                        if (preg_match('/\((\d+)\)/', $col->type, $matches)) {
                            $length = $matches[1];
                        }
                        break;
                    }
                }
                break;
            case 'pgsql':
                $length = DB::select("SELECT character_maximum_length FROM information_schema.columns WHERE table_name = '$table' AND column_name = '$column'");
                break;
            case 'sqlsrv':
                $length = DB::select("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'");
                break;
        }

        return $length;
    }

    /**
     * Get default value from column
     * @param $table
     * @param $column
     * @return array|null
     */
    private function getColumnDefault($table, $column) {
        $default = null;
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $columnInfo = DB::select("SHOW COLUMNS FROM $table WHERE Field = '$column'");
                $default = $columnInfo[0]->Default;
                break;
            case 'sqlite':
                $columns = DB::select("PRAGMA table_info($table)");
                foreach ($columns as $col) {
                    if ($col->name == $column) {
                        $default = $col->dflt_value;
                        break;
                    }
                }
                break;
            case 'pgsql':
                $default = DB::select("SELECT column_default FROM information_schema.columns WHERE table_name = '$table' AND column_name = '$column'");
                break;
            case 'sqlsrv':
                $default = DB::select("SELECT COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'");
                break;
        }

        return $default;
    }

    /**
     * Determine column is unique
     */
    private function isUnique($table, $column)
    {
        $unique = null;
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $fetchMeta = DB::select("SHOW COLUMNS FROM $table WHERE Field = '$column'");
                $unique = $fetchMeta[0]->Key == 'UNI';
                break;
            case 'sqlite':
                $columns = DB::select("PRAGMA table_info($table)");
                foreach ($columns as $col) {
                    if ($col->name == $column) {
                        $unique = $col->pk == 1;
                        break;
                    }
                }
                break;
            case 'pgsql':
                $unique = DB::select("SELECT column_default FROM information_schema.columns WHERE table_name = '$table' AND column_name = '$column'");
                break;
            case 'sqlsrv':
                $unique = DB::select("SELECT COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'");
                break;
        }

        return $unique;
    }

    /**
     * Determine column is nullable
     */
    private function isNullable($table, $column)
    {
        $nullable = null;
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $fetchMeta = DB::select("SHOW COLUMNS FROM $table WHERE Field = '$column'");
                $nullable = $fetchMeta[0]->Null == 'YES';
                break;
            case 'sqlite':
                $columns = DB::select("PRAGMA table_info($table)");
                foreach ($columns as $col) {
                    if ($col->name == $column) {
                        $nullable = $col->notnull == 0;
                        break;
                    }
                }
                break;
            case 'pgsql':
                $nullable = DB::select("SELECT is_nullable FROM information_schema.columns WHERE table_name = '$table' AND column_name = '$column'");
                break;
            case 'sqlsrv':
                $nullable = DB::select("SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'");
                break;
        }

        return $nullable;
    }

}
