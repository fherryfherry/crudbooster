<?php

namespace CrudBooster\Modules\ModuleBuilder\Builder;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleFormConstructor
{
    protected $form = [];

    protected array $skipFields = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function __construct(string $table, ?string $name = null, bool $primaryKeyIsUuid = false)
    {
        $this->form = [
            "name" => $name ?? ucwords(str_replace('_', ' ', $table)),
            "path" => $table,
            "model" => "App\\Cb\\Modules\\" . ucwords(str_replace('_', '', $table)) . "\\Models\\" . ucwords(str_replace('_', '', $table)) . "::class",
            "table" => $table,
            "schema" => $this->getSchema($table),
            "service" => "App\\Cb\\Modules\\" . ucwords(str_replace('_', '', $table)) . "\\Services\\" . ucwords(str_replace('_', '', $table)) . "Service::class",
            "primaryKey" => $this->getPrimaryKey($table),
            "primaryKeyIsUuid" => $primaryKeyIsUuid,
            "table_name" => null,
            "button_edit" => true,
            "button_create" => true,
            "button_delete" => true,
            "button_detail" => true,
            "button_filter" => true,
            "button_import" => true,
            "browse_columns" => $this->getBrowseColumns($table),
            "formDesignList" => $this->getFormDesignList($table),
            "permission_read" => true,
            "button_export_csv" => true,
            "button_export_pdf" => true,
            "button_export_xls" => true,
            "button_search_bar" => true,
            "permission_create" => true,
            "permission_delete" => true,
            "permission_update" => true,
            "button_bulk_action" => true
        ];
    }

    protected function getSchema(string $table): array
    {
        $columns = Schema::getColumnListing($table);
        $schema = [];
        foreach ($columns as $column) {
            $type = Schema::getColumnType($table, $column);
            $schema[] = [
                "name" => $column,
                "type" => convertSqlTypeToLaravelType($type),
                "config" => []
            ];
        }
        return $schema;
    }

    protected function getPrimaryKey(string $table): string
    {
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $query = "SELECT COLUMN_NAME
                      FROM INFORMATION_SCHEMA.COLUMNS
                      WHERE TABLE_SCHEMA = ?
                      AND TABLE_NAME = ?
                      AND COLUMN_KEY = 'PRI' LIMIT 1";
                break;

            case 'pgsql':
                $query = "SELECT a.attname AS COLUMN_NAME
                      FROM pg_index i
                      JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
                      WHERE i.indrelid = ?::regclass
                      AND i.indisprimary LIMIT 1";
                break;

            case 'sqlite':
                $query = "PRAGMA table_info(?)";
                break;

            case 'sqlsrv':
                $query = "SELECT COLUMN_NAME
                      FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE TABLE_NAME = ?
                      AND CONSTRAINT_NAME IN (
                          SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                          WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = 'PRIMARY KEY'
                      )";
                break;

            default:
                throw new Exception("Database driver not supported");
        }

        $result = DB::select($query, [$table, $table]);

        if ($driver === 'sqlite') {
            foreach ($result as $column) {
                if ($column->pk == 1) {
                    return $column->name;
                }
            }
        } else {
            return $result[0]->COLUMN_NAME ?? 'id';
        }

        return 'id';
    }

    protected function getBrowseColumns(string $table): array
    {
        $columns = Schema::getColumnListing($table);
        // skip fields
        $columns = array_diff($columns, $this->skipFields);
        $browseColumns = [];
        foreach ($columns as $column) {
            $browseColumns[] = [
                "key" => "{$table}.{$column}",
                "label" => ucwords(str_replace('_', ' ', $column)),
                "sortable" => true,
                "exportable" => true,
                "filterable" => true,
                "searchable" => true
            ];
        }
        return $browseColumns;
    }
    protected function getFieldType(string $table, string $column): string
    {
        $textTypes = ['varchar', 'char', 'text', 'string'];
        $numberTypes = ['int', 'integer', 'bigint', 'smallint', 'tinyint', 'decimal', 'float', 'double'];
        $moneyTypes = ['decimal', 'float', 'double'];
        $wysiwygTypes = ['text', 'mediumtext', 'longtext'];

        $type = Schema::getColumnType($table, $column);

        if (in_array($type, $textTypes)) {
            return 'text';
        } elseif (in_array($type, $numberTypes)) {
            return 'number';
        } elseif (in_array($type, $moneyTypes)) {
            return 'money';
        } elseif (in_array($type, $wysiwygTypes)) {
            return 'trix';
        } else {
            return 'textarea';
        }
    }

    protected function getFormDesignList(string $table): array
    {
        $columns = Schema::getColumnListing($table);
        // skip fields
        $columns = array_diff($columns, $this->skipFields);
        $formDesignList = [];
        foreach ($columns as $column) {
            $type = $this->getFieldType($table, $column);
            $formDesignList[] = [
                [
                    "key" => $column,
                    "type" => $type,
                    "label" => ucwords(str_replace('_', ' ', $column)),
                    "helpText" => "Input the " . ucwords(str_replace('_', ' ', $column)) . " here",
                    "showDetail" => true,
                    "showCreate" => true,
                    "showEdit" => true,
                ]
            ];
        }
        return $formDesignList;
    }

    public static function create(string $table, ?string $name = null, bool $primaryKeyIsUuid = false)
    {
        return new static($table, $name, $primaryKeyIsUuid);
    }

    public function toArray()
    {
        return $this->form;
    }
}
