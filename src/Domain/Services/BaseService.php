<?php

namespace CrudBooster\Domain\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use function class_uses_recursive;

class BaseService implements ServiceContract
{
    protected static array $primaryFields = ['id', 'created_at', 'updated_at', 'deleted_at', 'email_verified_at', 'remember_token'];
    protected static string $model;

    public static function getFields(): array
    {
        $cacheKey = 'fields_' . (self::new())->getTable();
        return cache()->remember($cacheKey, now()->addSeconds(10), function () {
            return DB::getSchemaBuilder()->getColumnListing((self::new())->getTable());
        });
    }

    public static function getFieldExceptPrimary(): array
    {
        return array_filter(static::getFields(), fn($field) => !in_array($field, static::$primaryFields));
    }

    public static function import(array $row)
    {
        $pk = static::getPrimaryKey();

        // reflection the current model class, then check if it contain use HasUuids
        $reflection = new \ReflectionClass(static::$model);
        $traits = $reflection->getTraits();
        $hasUuids = false;
        foreach ($traits as $trait) {
            if ($trait->getName() === 'Illuminate\Database\Eloquent\Concerns\HasUuids') {
                $hasUuids = true;
                break;
            }
        }

        if ($hasUuids) {
            $row[$pk] = Str::uuid()->toString();
        }

        return new static::$model($row);
    }

    public static function getPrimaryKey()
    {
        return (new static::$model())->getKeyName();
    }

    public static function new(): Model
    {
        return new static::$model();
    }

    public static function query(): Builder
    {
        /** @var Model $model */
        $model = static::$model;
        $query = $model::query();
        
        // Automatically filter out soft-deleted records if model uses SoftDeletes trait
        if (static::usesSoftDeletes()) {
            $table = static::new()->getTable();
            $query->whereNull($table . '.deleted_at');
        }
        
        return $query;
    }

    /**
     * Check if the model uses SoftDeletes trait
     * @return bool
     */
    private static function usesSoftDeletes(): bool
    {
        $modelClass = static::$model;
        $traits = class_uses_recursive($modelClass);
        
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', $traits);
    }

    private static function selectAliasRelationField($table, $key)
    {
        // Return a stable alias that matches relation['key'].
        // If $key already contains a dot (e.g. 'categories.name'), use it as-is.
        // Otherwise, prefix with table (e.g. 'categories' + 'name' => 'categories.name').
        return strpos($key, '.') !== false ? $key : ($table . '.' . $key);
    }

    public static function handleRelationQuery(Builder $query, $columns)
    {
        foreach ($columns as $index => $column) {
            if (isset($column['relation'])) {
                $relation = $column['relation'];
                if ($relation['relationType'] == 'ONE_TO_MANY_JOIN_CLAUSE') {
                    $query->join($relation['table'], $relation['joinClause']);
                    $query->addSelect($relation['table'] . '.' . $relation['displayKey'] . ' as ' . static::selectAliasRelationField($relation['table'], $relation['key']));
                } else if ($relation['relationType'] == 'ONE_TO_MANY_NESTED') {
                    foreach ($relation['relations'] as $relate) {
                        $query->join($relate['table'] . ' as ' . $relate['key'], $relate['key'] . '.' . $relate['first'], $relate['operator'], $relate['second'], $relate['type'], $relate['where']);
                    }
                    $lastRelation = collect($relation['relations'])->last();
                    $query->addSelect($lastRelation['key'] . '.' . $relation['displayKey'] . ' as ' . static::selectAliasRelationField($lastRelation['key'], $relation['key']));
                } else if ($relation['relationType'] == 'MANY_TO_MANY') {
                    if (DB::getDriverName() === 'sqlite') {
                        $subQuery = DB::table($relation['table'])
                            ->select($relation['table'] . '.' . $relation['primaryKey'], $relation['table'] . '.' . $relation['firstFk'])
                            ->addSelect(DB::raw(sprintf('GROUP_CONCAT(%s.%s, "%s") as %s',
                                $relation['displayTable'],
                                $relation['displayKey'],
                                $relation['displayDelimiter'],
                                $relation['key']
                            )))
                            ->join($relation['displayTable'], $relation['displayTable'] . '.' . $relation['displayPrimaryKey'], '=', $relation['table'] . '.' . $relation['secondFk'])
                            ->groupBy($relation['table'] . '.' . $relation['firstFk']);

                        $query->joinSub($subQuery, $relation['table'], function ($join) use ($relation) {
                            $join->on($relation['table'] . '.' . $relation['firstFk'], '=', static::new()->getTable() . '.' . static::new()->getKeyName());
                        });
                        $query->addSelect($relation['table'] . '.' . $relation['key'] . ' as ' . static::selectAliasRelationField($relation['table'], $relation['key']));
                    } else {
                        $subRaw = sprintf(
                            '(SELECT %s(%s.%s %s) FROM %s INNER JOIN %s ON %s.%s = %s.%s WHERE %s.%s = %s.%s) as %s',
                            DB::getDriverName() === 'pgsql' || DB::getDriverName() === 'sqlsrv' ? 'STRING_AGG' : 'GROUP_CONCAT',
                            $relation['displayTable'],
                            $relation['displayKey'],
                            DB::getDriverName() === 'pgsql' || DB::getDriverName() === 'sqlsrv' ? sprintf(', \'%s\'', $relation['displayDelimiter']) : sprintf(' SEPARATOR "%s"', $relation['displayDelimiter']),
                            $relation['table'],
                            $relation['displayTable'],
                            $relation['displayTable'],
                            $relation['displayPrimaryKey'],
                            $relation['table'],
                            $relation['secondFk'],
                            $relation['table'],
                            $relation['firstFk'],
                            static::new()->getTable(),
                            static::new()->getKeyName(),
                            static::selectAliasRelationField($relation['table'], $relation['displayKey'])
                        );
                        $query->addSelect(DB::raw($subRaw));
                    }
                } else if ($relation['relationType'] == 'ONE_TO_MANY') {
                    $alias = $relation['table'] . $index;
                    $query->join($relation['table'] . ' as ' . $alias, $alias . '.' . $relation['primaryKey'], '=', static::new()->getTable() . '.' . $column['key'], 'left');
                    $query->addSelect($alias . '.' . $relation['displayKey'] . ' as ' . static::selectAliasRelationField($alias, $relation['key']));
                }
            }
        }
    }

    public static function handleSearchQuery(Builder $query, $columns, $search)
    {
        $selects = $query->getQuery()->getColumns();
        // Get real table columns for virtual column detection
        $realColumns = static::getFields();
        $query->where(function (Builder $query) use ($columns, $search, $selects, $realColumns) {
            foreach ($columns as $column) {
                if (!$column['searchable']) continue;
                // Skip if not a real column and not a relation (virtual column)
                $colKey = $column['key'];
                $colKeyOnly = str_contains($colKey, '.') ? explode('.', $colKey)[1] : $colKey;
                if (!isset($column['relation']) && !in_array($colKeyOnly, $realColumns)) continue;
                if (isset($column['relation'])) {
                    $relation = $column['relation'];
                    if ($relation['relationType'] == 'MANY_TO_MANY' && DB::getDriverName() !== 'sqlite') {
                        $subRaw = sprintf(
                            '(SELECT count(*) FROM %s INNER JOIN %s ON %s.%s = %s.%s WHERE %s.%s = %s.%s AND %s.%s like "%%%s%%")',
                            $relation['table'],
                            $relation['displayTable'],
                            $relation['displayTable'],
                            $relation['displayPrimaryKey'],
                            $relation['table'],
                            $relation['secondFk'],
                            $relation['table'],
                            $relation['firstFk'],
                            static::new()->getTable(),
                            static::new()->getKeyName(),
                            $relation['displayTable'],
                            $relation['displayKey'],
                            $search
                        );
                        $query->orWhere(DB::raw($subRaw), '>', 0);
                    } else if ($relation['relationType'] == 'MANY_TO_MANY' && DB::getDriverName() === 'sqlite') {
                        $fieldToSearch = $relation['table'] . '.' . $relation['displayKey'];
                        $query->orWhere(function ($query) use ($fieldToSearch, $search) {
                            $query->where($fieldToSearch, 'like', '%' . $search . '%');
                            // search by split word
                            $words = explode(' ', $search);
                            foreach ($words as $word) {
                                $query->orWhere($fieldToSearch, 'like', '%' . $word . '%');
                            }
                        });
                    } else if ($relation['relationType'] == 'ONE_TO_MANY_JOIN_CLAUSE') {
                        $fieldToSearch = $relation['table'] . '.' . $relation['displayKey'];
                        $query->orWhere(function ($query) use ($fieldToSearch, $search) {
                            $query->where($fieldToSearch, 'like', '%' . $search . '%');
                            // search by split word
                            $words = explode(' ', $search);
                            foreach ($words as $word) {
                                $query->orWhere($fieldToSearch, 'like', '%' . $word . '%');
                            }
                        });
                    } else if ($relation['relationType'] == 'ONE_TO_MANY_NESTED') {
                        $lastRelation = collect($relation['relations'])->last();
                        $fieldToSearch = $lastRelation['key'] . '.' . $relation['displayKey'];
                        $query->orWhere(function ($query) use ($fieldToSearch, $search) {
                            $query->where($fieldToSearch, 'like', '%' . $search . '%');
                            // search by split word
                            $words = explode(' ', $search);
                            foreach ($words as $word) {
                                $query->orWhere($fieldToSearch, 'like', '%' . $word . '%');
                            }
                        });
                    } else if ($relation['relationType'] == 'ONE_TO_MANY') {
                        $alias = $relation['table'] . '_' . $column['key'];
                        $fieldToSearch = $alias . '.' . $relation['displayKey'];
                        $query->orWhere(function ($query) use ($fieldToSearch, $search) {
                            $query->where($fieldToSearch, 'like', '%' . $search . '%');
                            // search by split word
                            $words = explode(' ', $search);
                            foreach ($words as $word) {
                                $query->orWhere($fieldToSearch, 'like', '%' . $word . '%');
                            }
                        });
                    }
                } else {
                    $originalSelectMaps = array_map(function ($column) {
                        if(is_string($column)) {
                            return [
                                'original_column' => explode(' as ', $column)[0],
                                'alias_column' => explode(' as ', $column)[1] ?? explode(' as ', $column)[0]
                            ];
                        }
                    }, $selects ?? []);

                    if(!str_contains($column['key'], '.')){
                        $fieldToSearch = collect($originalSelectMaps)->where('alias_column', $column['key'])->first()['original_column'] ?? self::new()->getTable() . '.' . $column['key'];
                    } else {
                        $fieldToSearch = $column['key'];
                    }
                    $query->orWhere(function ($query) use ($fieldToSearch, $search) {
                        $query->where($fieldToSearch, 'like', '%' . $search . '%');
                        // search by split word
                        $words = explode(' ', $search);
                        foreach ($words as $word) {
                            $query->orWhere($fieldToSearch, 'like', '%' . $word . '%');
                        }
                    });
                }
            }
        });
    }

    public static function handleFilterQuery(Builder $query, $filter, array $columns = []): void
    {
        $columns = $columns ?? [];
        $originalSelectMaps = array_map(function ($column) {
            if(is_string($column)) {
                return [
                    'original_column' => explode(' as ', $column)[0],
                    'alias_column' => explode(' as ', $column)[1] ?? explode(' as ', $column)[0]
                ];
            }
            return $column;
        }, $columns);
        // Get real table columns for virtual column detection
        $realColumns = static::getFields();
        $operatorMapper = [
            'CONTAIN' => ['OPERATOR' => 'like', 'FORMAT' => "%%%s%%"],
            'NOT CONTAIN' => ['OPERATOR' => 'not like', 'FORMAT' => "%%%s%%"],
            '>' => ['OPERATOR' => '>', 'FORMAT' => "%s"],
            '<' => ['OPERATOR' => '<', 'FORMAT' => "%s"],
            '>=' => ['OPERATOR' => '>=', 'FORMAT' => "%s"],
            '<=' => ['OPERATOR' => '<=', 'FORMAT' => "%s"],
            '!=' => ['OPERATOR' => '!=', 'FORMAT' => "%s"],
            '=' => ['OPERATOR' => '=', 'FORMAT' => "%s"],
            'EQUAL' => ['OPERATOR' => '=', 'FORMAT' => "%s"],
            'NOT EQUAL' => ['OPERATOR' => '!=', 'FORMAT' => "%s"]
        ];
        $query->where(function (Builder $query) use ($filter, $operatorMapper, $columns, $originalSelectMaps, $realColumns) {
            foreach ($filter as $field => $value) {
                $field = str_replace('__', '.', $field);
                $column = array_column($columns, null, 'key')[$field] ?? null;
                if (!$column || !$column['filterable']) continue;
                // Check if this is a virtual column with custom search closure
                $colKey = $column['key'];
                $colKeyOnly = str_contains($colKey, '.') ? explode('.', $colKey)[1] : $colKey;
                $hasCustomSearchClosure = isset($column['filter_options']['search_closure']);
                
                // Skip if not a real column, not a relation, and no custom search closure (virtual column)
                if (!isset($column['relation']) && !in_array($colKeyOnly, $realColumns) && !$hasCustomSearchClosure) continue;
                $filterType = $column['filter_type'] ?? 'contains';
                $filterValue = $value['value'] ?? null;
                if (!$filterValue) continue;
                if (isset($column['relation'])) {
                    static::handleRelationFilter($query, $column, $filterType, $filterValue, $operatorMapper);
                } else {
                    static::handleDirectFilter($query, $column, $filterType, $filterValue, $field, $originalSelectMaps);
                }
            }
        });
    }
    
    private static function handleRelationFilter(Builder $query, $column, $filterType, $filterValue, $operatorMapper): void
    {
        $relation = $column['relation'];
        $operator = static::getOperatorForFilterType($filterType);
        $formattedValue = static::formatValueForFilterType($filterType, $filterValue);
        
        if ($relation['relationType'] == 'MANY_TO_MANY' && DB::getDriverName() !== 'sqlite') {
            $subRaw = sprintf(
                '(SELECT count(*) FROM %s INNER JOIN %s ON %s.%s = %s.%s WHERE %s.%s = %s.%s AND %s.%s %s "%s")',
                $relation['table'],
                $relation['displayTable'],
                $relation['displayTable'],
                $relation['displayPrimaryKey'],
                $relation['table'],
                $relation['secondFk'],
                $relation['table'],
                $relation['firstFk'],
                static::new()->getTable(),
                static::new()->getKeyName(),
                $relation['displayTable'],
                $relation['displayKey'],
                $operator,
                $formattedValue
            );
            $query->orWhere(DB::raw($subRaw), '>', 0);
        } else if ($relation['relationType'] == 'MANY_TO_MANY' && DB::getDriverName() === 'sqlite') {
            $query->orWhere($relation['table'] . '.' . $relation['displayKey'], $operator, $formattedValue);
        } else if ($relation['relationType'] == 'ONE_TO_MANY_JOIN_CLAUSE') {
            $query->orWhere($relation['table'] . '.' . $relation['displayKey'], $operator, $formattedValue);
        } else if ($relation['relationType'] == 'ONE_TO_MANY_NESTED') {
            $lastRelation = collect($relation['relations'])->last();
            $query->orWhere($lastRelation['key'] . '.' . $relation['displayKey'], $operator, $formattedValue);
        } else if ($relation['relationType'] == 'ONE_TO_MANY') {
            $alias = $relation['table'] . '_' . $column['key'];
            $query->orWhere($alias . '.' . $relation['displayKey'], $operator, $formattedValue);
        }
    }
    
    private static function handleDirectFilter(Builder $query, $column, $filterType, $filterValue, $field, $originalSelectMaps): void
    {
        if (!str_contains($field, '.')) {
            $field = collect($originalSelectMaps)->where('alias_column', $field)->first()['original_column'] ?? self::new()->getTable() . '.' . $field;
        }
        
        // Check if there's a custom search closure for any filter type
        if (isset($column['filter_options']['search_closure'])) {
            $searchClosure = $column['filter_options']['search_closure'];
            $searchClosure($query, $filterValue, $field);
            return;
        }
        
        $operator = static::getOperatorForFilterType($filterType);
        $formattedValue = static::formatValueForFilterType($filterType, $filterValue);
        
        if ($filterType === 'date_range' && is_array($filterValue)) {
            // Handle date range filter
            if (!empty($filterValue['start'])) {
                // Add time to start date to begin from the start of the day (00:00:00)
                $startDate = $filterValue['start'] . ' 00:00:00';
                $query->where($field, '>=', $startDate);
            }
            if (!empty($filterValue['end'])) {
                // Add time to end date to include the entire day (23:59:59)
                $endDate = $filterValue['end'] . ' 23:59:59';
                $query->where($field, '<=', $endDate);
            }
        } else {
            $query->where($field, $operator, $formattedValue);
        }
    }
    
    private static function getOperatorForFilterType($filterType): string
    {
        return match ($filterType) {
            'contains' => 'like',
            '>' => '>',
            '>=' => '>=',
            '<' => '<',
            '<=' => '<=',
            'select_enum', 'select_query' => '=',
            default => 'like'
        };
    }
    
    private static function formatValueForFilterType($filterType, $value): string
    {
        if ($filterType === 'date_range') {
            // Sudah di-handle khusus di handleDirectFilter, tidak perlu format di sini
            return '';
        }
        return match ($filterType) {
            'contains' => '%' . $value . '%',
            'select_enum', 'select_query', '>', '>=', '<', '<=' => $value,
            default => '%' . $value . '%'
        };
    }

    public static function handleSortBy($rawSortBy, $columns): string
    {
        // Get primary key as default sort by
        $rawSortBy = $rawSortBy ?: static::new()->getTable().'.'.static::new()->getKeyName();
        $sortBy = array_column($columns, null, 'key')[$rawSortBy] ?? null;
        if (isset($sortBy['relation'])) {
            return "`" . $sortBy['relation']['key']. "`";
        } else {
            return $rawSortBy;
        }
    }

    public static function selectField($table, $columns)
    {
        $currentTable = static::new()->getTable();
        $fields = Schema::getColumnListing($table);
        $result = [];
        foreach ($fields as $field) {
            $result[] = $table . '.' . $field . ' as ' . $table . '.' . $field;
            if ($table == $currentTable) {
                $result[] = $table . '.' . $field . ' as ' . $field;
            }
        }

        // Add all columns
        foreach ($columns as $column) {
            if (isset($column['relation'])) {
                $relation = $column['relation'];
                if ($relation['relationType'] == 'ONE_TO_MANY_JOIN_CLAUSE') {
                    $result[] = $relation['key'] . ' as ' . $relation['key'];
                } else if ($relation['relationType'] == 'ONE_TO_MANY_NESTED') {
                    $lastRelation = collect($relation['relations'])->last();
                    $result[] = $relation['key'] . ' as ' . $lastRelation['key'];
                } else if ($relation['relationType'] == 'ONE_TO_MANY') {
                    $alias = $relation['table'] . '_' . $column['key'];
                    $result[] = $alias . '.' . $relation['key'] . ' as ' . $alias . '.' . $relation['key'];
                }
            }
        }

        return $result;
    }


    public static function hookQuery(Builder $query, array $hookQuery): void
    {
        array_walk($hookQuery, fn($value) => $value($query));

        // Clear double join query
        $query->getQuery()->joins = array_unique($query->getQuery()->joins ?? [], SORT_REGULAR);
    }

    public static function hookSearch(Builder $query, array $hookSearch, $search): void
    {
        array_walk($hookSearch, fn($value) => $value($query, $search));
    }

    public static function getPaginate($filter = [],
        $search = null,
        $sortBy = null,
        $sortType = "desc",
        $perPage = 10,
        $columns = [],
        array $hookQuery = [],
        array $hookSearch = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $result = static::query()
            ->addSelect(static::selectField(static::new()->getTable(), $columns))
            ->when($hookQuery, fn(Builder $query) => static::hookQuery($query, $hookQuery))
            ->when(isset($columns), fn(Builder $query) => static::handleRelationQuery($query, $columns))
            ->when(isset($search) && empty($hookSearch), fn(Builder $query) => static::handleSearchQuery($query, $columns, $search))
            ->when(isset($search) && !empty($hookSearch), fn(Builder $query) => static::hookSearch($query, $hookSearch, $search))
            ->when(isset($filter), fn(Builder $query) => static::handleFilterQuery($query, $filter, $columns))
            ->orderByRaw(static::handleSortBy($sortBy, $columns) . " " . $sortType)
            ->paginate($perPage);
        return $result;
    }

    public static function countData()
    {
        return static::query()->count();
    }

    public static function find($id)
    {
        return static::query()->find($id);
    }

    public static function findById($id)
    {
        return static::find($id);
    }

    public static function getList()
    {
        return static::query()->get();
    }

    public static function getDetail($id)
    {
        return static::query()->find($id);
    }

    public static function create($data)
    {
        return static::query()->create($data);
    }

    public static function updateWithData($id, $data)
    {
        return static::query()->find($id)->update($data);
    }

    public static function deleteData($id)
    {
        return static::query()->find($id)->delete();
    }

    public static function deleteById($id)
    {
        return static::query()->find($id)->delete();
    }

    public static function deleteByIds(array $ids)
    {
        return static::query()->whereIn(static::getPrimaryKey(), $ids)->delete();
    }

    /**
     * Get data for export (PDF, Excel, CSV)
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function getExportData($request): array
    {
        $filter = $request->input('filter', []);
        $search = $request->input('search');
        $sortBy = $request->input('sortBy');
        $sortType = $request->input('sortType', 'asc');
        $columns = $request->input('columns', []);
        $hookQuery = $request->input('hookQuery', []);
        $hookSearch = $request->input('hookSearch', []);

        // Get all data without pagination for export
        $query = static::query();
        
        // Handle relations first
        if (!empty($columns)) {
            static::handleRelationQuery($query, $columns);
        }
        
        // Apply filters
        if (!empty($filter)) {
            static::handleFilterQuery($query, $filter, $columns);
        }
        
        // Apply search
        if (!empty($search)) {
            static::handleSearchQuery($query, $columns, $search);
        }
        
        // Apply sorting
        if (!empty($sortBy)) {
            $sortField = static::handleSortBy($sortBy, $columns);
            $query->orderBy($sortField, $sortType);
        } else {
            // Default: order by primary key desc
            $query->orderBy(static::getPrimaryKey(), 'desc');
        }
        
        // Apply hooks
        static::hookQuery($query, $hookQuery);
        static::hookSearch($query, $hookSearch, $search);
        
        // Select fields (include aliases for base table and relations)
        $query->addSelect(static::selectField(static::new()->getTable(), $columns));

        // Get all data
        $data = $query->get();

        // Transform data for export
        return $data->map(function($row) {
            return $row->toArray();
        })->toArray();
    }

    /**
     * Get columns configuration for export
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function getExportColumns($request): array
    {
        $columns = $request->input('columns', []);
        
        if (empty($columns)) {
            // Default columns if none provided
            $fields = static::getFieldExceptPrimary();
            $columns = [];
            foreach ($fields as $field) {
                $columns[] = [
                    'key' => $field,
                    'label' => ucfirst(str_replace('_', ' ', $field)),
                    'searchable' => true,
                    'sortable' => true,
                    'filterable' => true,
                    'relation' => null
                ];
            }
        }
        
        return $columns;
    }
}
