<?php

namespace CrudBooster\Modules\QueryBuilder\Services;

use CrudBooster\Domain\Services\BaseService;
use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CbQueryBuilderService extends BaseService
{
    protected static string $model = CbQueryBuilder::class;
    protected static int $limit = 10;

    public static function getDataWithOutputTypeCount()
    {
        return self::query()->where('config->aggregationType', '!=', 'ARRAY')->orWhereJsonContains('config->builderMode','QUERY_RAW')->get();
    }
    public static function getDataWithOutputTypeArray()
    {
        return self::query()->whereJsonContains('config->aggregationType', 'ARRAY')->orWhereJsonContains('config->builderMode','QUERY_RAW')->get();
    }

    private static function isUnsafeCode($phpCode) {
        $dangerousFunctions = [
            'eval', 'exec', 'passthru', 'shell_exec', 'system', 'proc_open',
            'popen', 'pcntl_exec', 'create_function', 'assert', 'dl', 'ini_set',
            'unserialize', 'curl_exec', 'curl_multi_exec', 'file_', 'fopen',
            'fsockopen', 'pfsockopen', 'stream_socket_', 'putenv', 'mail',
            'header_remove', 'mb_parse_str', 'proc_', 'openlog', 'syslog'
        ];

        $patterns = [
            '/\b('.implode('|', $dangerousFunctions).')\s*\(/i',
            '/`.*`/',
            '/<\?=?.*(?:php)?.*\?>/i',
            '/preg_replace\s*\(.*[^\\\\]\/(e|E)\'/',
            '/php:\/\/(input|stdin|fd|expect|glob|data|zip|phar)/i',
            '/(O:|R:|C:)+[0-9]+:/',
            '/\\x[0-9a-f]{2}/i',
            '/\\\\[rnfv\\\\]/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $phpCode)) {
                return true;
            }
        }

        return false;
    }

    public static function runQuery(array $config, $limit = null, callable $callback = null)
    {
        $rawQuery = $config['rawQuery'] ?? null;
        if ($rawQuery) {
            $query = DB::table(DB::raw("({$rawQuery}) as subquery"));
            if ($callback) {
                $callback($query);
            }
            return $query->get();
        }
        $model = $config['modelName'];
        $selectColumns = $config['selectColumns'];
        $conditionGroups = $config['conditionGroups'];
        $relationships = $config['relationships'];
        $orderByColumn = $config['orderByColumn'];
        $orderByDirection = $config['orderByDirection'];
        $groupByColumns = $config['groupByColumns'];
        $havingConditions = $config['havingConditions'];
        $aggregationType = $config['aggregationType'];
        $aggregationColumn = $config['aggregationColumn'];

        $query = DB::table((new $model())->getTable());

        // Apply relationships
        foreach ($relationships as $relationship) {
            if ($relationship['first_table'] && $relationship['first_field'] && $relationship['operator'] && $relationship['second_table'] && $relationship['second_field']) {
                $query->join($relationship['second_table'], "{$relationship['first_table']}.{$relationship['first_field']}", $relationship['operator'], "{$relationship['second_table']}.{$relationship['second_field']}");
            }
        }

        // Apply condition groups
        foreach ($conditionGroups as $group) {
            if (!empty($group['conditions'])) {
                $query->where(function ($q) use ($group) {
                    foreach ($group['conditions'] as $condition) {
                        $conditionValue = $condition['value'];
                        if (preg_match('/@php\s*(.*?)\s*@endphp/', $conditionValue, $matches)) {
                            $phpCode = $matches[1];
                            if (static::isUnsafeCode($phpCode)) {
                                throw new \Exception('Unsafe characters detected in PHP code.');
                            }
                            $conditionValue = @eval('return ' . $phpCode . ';');
                            if ($conditionValue === false) {
                                throw new \Exception('Error evaluating PHP code in condition value.');
                            }
                            Log::debug('Evaluated PHP code in condition value: ' . $phpCode . ' => ' . $conditionValue);
                        } else {
                            Log::debug('Condition value: ' . $conditionValue);
                        }
                        if ($condition['field'] && $condition['operator'] && $conditionValue) {
                            if ($condition['type'] === 'orWhere') {
                                if ($condition['operator'] === 'IS NULL') {
                                    $q->orWhereNull($condition['field']);
                                } elseif ($condition['operator'] === 'IS NOT NULL') {
                                    $q->orWhereNotNull($condition['field']);
                                } elseif ($condition['operator'] === 'BETWEEN') {
                                    $q->orWhereBetween($condition['field'], explode(',', $conditionValue));
                                } elseif ($condition['operator'] === 'NOT BETWEEN') {
                                    $q->orWhereNotBetween($condition['field'], explode(',', $conditionValue));
                                } elseif ($condition['operator'] === 'IN') {
                                    $q->orWhereIn($condition['field'], explode(',', $conditionValue));
                                } elseif ($condition['operator'] === 'NOT IN') {
                                    $q->orWhereNotIn($condition['field'], explode(',', $conditionValue));
                                } else {
                                    $q->orWhere($condition['field'], $condition['operator'], $conditionValue);
                                }
                            } else {
                                if ($condition['operator'] === 'IS NULL') {
                                    $q->whereNull($condition['field']);
                                } elseif ($condition['operator'] === 'IS NOT NULL') {
                                    $q->whereNotNull($condition['field']);
                                } elseif ($condition['operator'] === 'BETWEEN') {
                                    $q->whereBetween($condition['field'], explode(',', $conditionValue));
                                } elseif ($condition['operator'] === 'NOT BETWEEN') {
                                    $q->whereNotBetween($condition['field'], explode(',', $conditionValue));
                                } elseif ($condition['operator'] === 'IN') {
                                    $q->whereIn($condition['field'], explode(',', $conditionValue));
                                } elseif ($condition['operator'] === 'NOT IN') {
                                    $q->whereNotIn($condition['field'], explode(',', $conditionValue));
                                } else {
                                    $q->where($condition['field'], $condition['operator'], $conditionValue);
                                }
                            }
                        }
                    }
                });
            }
        }

        // Apply group by
        if (!empty($groupByColumns)) {
            $query->groupBy($groupByColumns);
        }

        // Apply having conditions
        foreach ($havingConditions as $having) {
            if ($having['field'] && $having['operator'] && $having['value']) {
                $query->having($having['field'], $having['operator'], $having['value']);
            }
        }

        // Apply aggregation
        if ($aggregationType != 'ARRAY' && $aggregationColumn) {
            switch ($aggregationType) {
                case 'SUM':
                    $query->select(DB::raw("SUM({$aggregationColumn}) as ag10gregate_result"));
                    break;
                case 'AVG':
                    $query->select(DB::raw("AVG({$aggregationColumn}) as aggregate_result"));
                    break;
                case 'MIN':
                    $query->select(DB::raw("MIN({$aggregationColumn}) as aggregate_result"));
                    break;
                case 'MAX':
                    $query->select(DB::raw("MAX({$aggregationColumn}) as aggregate_result"));
                    break;
                case 'COUNT':
                    $query->select(DB::raw("COUNT({$aggregationColumn}) as aggregate_result"));
                    break;
            }
        } else {
            $query->select($selectColumns);
        }

        // Apply order by
        if ($orderByColumn) {
            $query->orderBy($orderByColumn, $orderByDirection);
        }

        // Execute the query and return results
        return $query->take($limit ?: self::$limit)->get();
    }
}
