<?php

if(!function_exists('convertSqlTypeToLaravelType')) {
    function convertSqlTypeToLaravelType($type): string
    {
        $type = strtolower($type);
        $typeMap = [
            'char' => 'string',
            'varchar' => 'string',
            'text' => 'text',
            'mediumtext' => 'mediumText',
            'longtext' => 'longText',
            'tinyint' => 'tinyInteger',
            'smallint' => 'smallInteger',
            'mediumint' => 'mediumInteger',
            'int' => 'integer',
            'bigint' => 'bigInteger',
            'float' => 'float',
            'double' => 'double',
            'decimal' => 'decimal',
            'date' => 'date',
            'time' => 'time',
            'datetime' => 'dateTime',
            'timestamp' => 'timestamp',
            'year' => 'year',
            'binary' => 'binary',
            'varbinary' => 'binary',
            'blob' => 'binary',
            'tinyblob' => 'binary',
            'mediumblob' => 'binary',
            'longblob' => 'binary',
            'enum' => 'enum',
            'set' => 'set',
            'json' => 'json',
            'jsonb' => 'json',
            'geometry' => 'geometry',
            'point' => 'point',
            'linestring' => 'linestring',
            'polygon' => 'polygon',
            'multipoint' => 'multipoint',
            'multilinestring' => 'multilinestring',
            'multipolygon' => 'multipolygon',
            'geometrycollection' => 'geometrycollection',
            'ulid' => 'string',
            'foreignUlid' => 'string',
        ];
        return $typeMap[$type] ?? 'string';
    }
}

// function check similarity laravel migration type to sql type
if(!function_exists('convertMigrationTypeToSql')) {
    function convertMigrationTypeToSql($type): string
    {
        $type = strtolower($type);
        $typeMap = [
            'uuid' => 'char',
            'string' => 'varchar',
            'text' => 'text',
            'mediumtext' => 'mediumtext',
            'longtext' => 'longtext',
            'tinyinteger' => 'tinyint',
            'smallinteger' => 'smallint',
            'mediuminteger' => 'mediumint',
            'integer' => 'int',
            'biginteger' => 'bigint',
            'float' => 'float',
            'double' => 'double',
            'decimal' => 'decimal',
            'date' => 'date',
            'time' => 'time',
            'datetime' => 'datetime',
            'timestamp' => 'timestamp',
            'year' => 'year',
            'binary' => 'binary',
            'enum' => 'enum',
            'set' => 'set',
            'json' => 'json',
            'geometry' => 'geometry',
            'point' => 'point',
            'linestring' => 'linestring',
            'polygon' => 'polygon',
            'multipoint' => 'multipoint',
            'multilinestring' => 'multilinestring',
            'multipolygon' => 'multipolygon',
            'geometrycollection' => 'geometrycollection',
            'ulid' => 'varchar',
            'foreignUlid' => 'varchar',
        ];
        return $typeMap[$type] ?? 'varchar';
    }
}
