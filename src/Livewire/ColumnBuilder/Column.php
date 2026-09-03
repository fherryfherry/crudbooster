<?php

namespace CrudBooster\Livewire\ColumnBuilder;

use Closure;

class Column
{
    // Filter type constants
    public const FILTER_CONTAINS = 'contains';
    public const FILTER_GREATER_THAN = '>';
    public const FILTER_GREATER_EQUAL = '>=';
    public const FILTER_LESS_THAN = '<';
    public const FILTER_LESS_EQUAL = '<=';
    public const FILTER_DATE_RANGE = 'date_range';
    public const FILTER_SELECT_ENUM = 'select_enum';
    public const FILTER_SELECT_QUERY = 'select_query';

    private array $columns;

    public function __construct($column)
    {
        $this->columns = $column;
    }

    /**
     * Set filter type for the column
     * @param string $filterType
     * @param array $options Additional options for the filter
     * @return $this
     */
    public function filterType(string $filterType, array $options = []): static
    {
        if (!$this->columns) return $this;
        
        $this->columns['filter_type'] = $filterType;
        $this->columns['filter_options'] = $options;
        return $this;
    }

    /**
     * Set filter as contains (string input)
     * @return $this
     */
    public function filterContains(): static
    {
        return $this->filterType(self::FILTER_CONTAINS);
    }

    /**
     * Set filter as greater than (number input)
     * @return $this
     */
    public function filterGreaterThan(): static
    {
        return $this->filterType(self::FILTER_GREATER_THAN);
    }

    /**
     * Set filter as greater than or equal (number input)
     * @return $this
     */
    public function filterGreaterEqual(): static
    {
        return $this->filterType(self::FILTER_GREATER_EQUAL);
    }

    /**
     * Set filter as less than (number input)
     * @return $this
     */
    public function filterLessThan(): static
    {
        return $this->filterType(self::FILTER_LESS_THAN);
    }

    /**
     * Set filter as less than or equal (number input)
     * @return $this
     */
    public function filterLessEqual(): static
    {
        return $this->filterType(self::FILTER_LESS_EQUAL);
    }

    /**
     * Set filter as date range (date range picker)
     * @return $this
     */
    public function filterDateRange(): static
    {
        return $this->filterType(self::FILTER_DATE_RANGE);
    }

    /**
     * Set filter as select enum (select dropdown with predefined options)
     * @param array $options Array of options [value => label]
     * @return $this
     */
    public function filterSelectEnum(array $options): static
    {
        return $this->filterType(self::FILTER_SELECT_ENUM, ['options' => $options]);
    }

    /**
     * Set filter as select query (select dropdown with dynamic options from query)
     * @param Closure $queryClosure Closure that returns a query builder with options
     * @param Closure|null $searchClosure Optional closure for custom search logic when filter is applied
     * @return $this
     */
    public function filterSelectQuery(Closure $queryClosure, ?Closure $searchClosure = null): static
    {
        $options = [
            'query_closure' => $queryClosure
        ];
        
        if ($searchClosure !== null) {
            $options['search_closure'] = $searchClosure;
        }
        
        return $this->filterType(self::FILTER_SELECT_QUERY, $options);
    }

    /**
     * To make a relation with nested
     * @param array $relations
     * @param string $displayKey
     * @return $this
     */
    public function relationWithNested(array $relations, $displayKey): Column
    {
        $relationArray = array_map(fn($relation) => $relation->get(), $relations);
        $lastRelation = collect($relationArray)->last();
        $this->columns['relation'] = [
            'relationType' => 'ONE_TO_MANY_NESTED',
            'relations' => $relationArray,
            'displayKey' => $displayKey,
            'key' => $lastRelation['table'] . '.' . $displayKey
        ];
        return $this;
    }

    /**
     * To join with another table
     * @param string $model
     * @param Closure|string $displayOrJoinClause
     * @return $this
     */
    public function relation(string $model, Closure|string $displayOrJoinClause): Column
    {
        $this->columns['relation'] = [
            'relationType' => $displayOrJoinClause instanceof Closure ? 'ONE_TO_MANY_JOIN_CLAUSE' : 'ONE_TO_MANY',
            'table' => (new $model)->getTable(),
            'model' => $model,
            'joinClause' => $displayOrJoinClause instanceof Closure ? $displayOrJoinClause : null,
            'primaryKey' => (new $model)->getKeyName(),
            'displayKey' => $displayOrJoinClause,
            'key' => (new $model)->getTable() . '.' . $displayOrJoinClause
        ];
        return $this;
    }

    /**
     * To make a relation many to many
     * @param string $modelMany
     * @param string $firstFk
     * @param string $secondFk
     * @param string $displayModel
     * @param string $displayColumn
     * @param string $displayDelimiter
     * @return $this
     */
    public function relationMany(string $modelMany, string $firstFk, string $secondFk, string $displayModel, string $displayColumn, string $displayDelimiter = ', '): Column
    {
        $this->columns['relation'] = [
            'relationType' => 'MANY_TO_MANY',
            'table' => (new $modelMany)->getTable(),
            'model' => $modelMany,
            'primaryKey' => (new $modelMany)->getKeyName(),
            'firstFk' => $firstFk,
            'secondFk' => $secondFk,
            'displayModel' => $displayModel,
            'displayTable' => (new $displayModel)->getTable(),
            'displayPrimaryKey' => (new $displayModel)->getKeyName(),
            'displayKey' => $displayColumn, // This is the column to display
            'displayDelimiter' => $displayDelimiter,
            'key' => (new $modelMany)->getTable() . '.' . $displayColumn // This is the key for display in view
        ];
        return $this;
    }

    /**
     * To transform the column value
     * @param string|Closure $transform (string: method name, Closure: callback)
     * @return $this
     */
    public function transform(string|Closure $transform): static
    {
        if (!$this->columns) return $this;
        if (is_string($transform)) {
            $transform = Transform::__callMethod($transform);
        }
        $this->columns['transform'] = $transform;
        return $this;
    }

    /**
     * To transform the column value with row
     * @param $row
     * @return $this
     */
    public function transformWithRow($row): static
    {
        if (!$this->columns) return $this;
        if (is_string($row)) {
            $row = Transform::__callMethod($row);
        }
        $this->columns['transformWithRow'] = $row;
        return $this;
    }

    /**
     * Transform value hanya jika value sesuai yang diharapkan
     * @param mixed $whenValue
     * @param callable $callback
     * @return $this
     */
    public function transformWhen($whenValue, callable $callback): static
    {
        $prevTransform = $this->columns['transform'] ?? null;
        $this->columns['transform'] = function ($value) use ($whenValue, $callback, $prevTransform) {
            if ((string)$value === (string)$whenValue) {
                return $callback($value);
            }
            if ($prevTransform) {
                return $prevTransform($value);
            }
            return $value;
        };
        return $this;
    }

    /**
     * To make column badgeable with custom mapping
     * @param array $badgeMap Array of value => badge_type mapping
     * @return $this
     */
    public function badgeable(array $badgeMap): static
    {
        if (!$this->columns) return $this;
        
        $this->columns['is_html'] = true;
        return $this->transform(function ($value) use ($badgeMap) {
            if (!$value) return '-';
            
            $badgeType = $badgeMap[$value] ?? 'secondary';
            $badgeStyle = $this->getBadgeClass($badgeType);
            
            return '<span style="' . $badgeStyle . '">' . $value . '</span>';
        });
    }

    public function badgeableSuccess($whenValue, $transformedValue = null): static
    {
        $this->columns['is_html'] = true;
        return $this->transformWhen($whenValue, function($value) use ($transformedValue) {
            $displayValue = $transformedValue ?? $value;
            $badgeStyle = $this->getBadgeClass('success');
            return '<span style="' . $badgeStyle . '">' . $displayValue . '</span>';
        });
    }

    public function badgeableDanger($whenValue, $transformedValue = null): static
    {
        $this->columns['is_html'] = true;
        return $this->transformWhen($whenValue, function($value) use ($transformedValue) {
            $displayValue = $transformedValue ?? $value;
            $badgeStyle = $this->getBadgeClass('danger');
            return '<span style="' . $badgeStyle . '">' . $displayValue . '</span>';
        });
    }

    /**
     * To make column badgeable with warning badge for specific value
     * @param string $whenValue Original value to match
     * @param string|null $transformedValue Optional transformed value to display
     * @return $this
     */
    public function badgeableWarning(string $whenValue, ?string $transformedValue = null): static
    {
        if (!$this->columns) return $this;
        
        $this->columns['is_html'] = true;
        return $this->transform(function ($value) use ($whenValue, $transformedValue) {
            if (!$value) return '-';
            
            if ($value == $whenValue) {
                $displayValue = $transformedValue ?? $value;
                $badgeStyle = $this->getBadgeClass('warning');
                return '<span style="' . $badgeStyle . '">' . $displayValue . '</span>';
            }
            
            return $value;
        });
    }

    /**
     * To make column badgeable with info badge for specific value
     * @param string $whenValue Original value to match
     * @param string|null $transformedValue Optional transformed value to display
     * @return $this
     */
    public function badgeableInfo(string $whenValue, ?string $transformedValue = null): static
    {
        if (!$this->columns) return $this;
        
        $this->columns['is_html'] = true;
        return $this->transform(function ($value) use ($whenValue, $transformedValue) {
            if (!$value) return '-';
            
            if ($value == $whenValue) {
                $displayValue = $transformedValue ?? $value;
                $badgeStyle = $this->getBadgeClass('info');
                return '<span style="' . $badgeStyle . '">' . $displayValue . '</span>';
            }
            
            return $value;
        });
    }

    /**
     * To make column badgeable with primary badge for specific value
     * @param string $whenValue Original value to match
     * @param string|null $transformedValue Optional transformed value to display
     * @return $this
     */
    public function badgeablePrimary(string $whenValue, ?string $transformedValue = null): static
    {
        if (!$this->columns) return $this;
        
        $this->columns['is_html'] = true;
        return $this->transform(function ($value) use ($whenValue, $transformedValue) {
            if (!$value) return '-';
            
            if ($value == $whenValue) {
                $displayValue = $transformedValue ?? $value;
                $badgeStyle = $this->getBadgeClass('primary');
                return '<span style="' . $badgeStyle . '">' . $displayValue . '</span>';
            }
            
            return $value;
        });
    }

    /**
     * Set column as filterable or not
     * @param bool $filterable
     * @return $this
     */
    public function filterable(bool $filterable = true): static
    {
        $this->columns['filterable'] = $filterable;
        return $this;
    }

    /**
     * Set column to display value with no wrap (whitespace-nowrap)
     * @return $this
     */
    public function noWrap(): static
    {
        $this->columns['no_wrap'] = true;
        return $this;
    }

    /**
     * Get badge CSS class based on type
     * @param string $type
     * @return string
     */
    private function getBadgeClass(string $type): string
    {
        // Use inline style instead of Tailwind class for badge color
        return match ($type) {
            'success' => 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:12px;font-weight:500;background:#bbf7d0;color:#166534;',
            'danger' => 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:12px;font-weight:500;background:#fecaca;color:#991b1b;',
            'warning' => 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:12px;font-weight:500;background:#fef08a;color:#854d0e;',
            'info' => 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:12px;font-weight:500;background:#bae6fd;color:#075985;',
            'primary' => 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:12px;font-weight:500;background:#c7d2fe;color:#3730a3;',
            default => 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:12px;font-weight:500;background:#f3f4f6;color:#374151;',
        };
    }

    /**
     * Get the column
     * @return array
     */
    public function get()
    {
        if (isset($this->columns['no_wrap'])) {
            $this->columns['no_wrap'] = $this->columns['no_wrap'];
        }
        return $this->columns;
    }

    /**
     * Add a new column
     * @param string $label
     * @param string $key
     * @param null $transform
     * @param bool $searchable
     * @param bool $filterable
     * @param bool $exportable
     * @param bool $sortable
     * @return Column
     */
    public static function add(
        $label,
        $key,
        $transform = null,
        $searchable = true,
        $filterable = true,
        $exportable = true,
        $sortable = true
    ): Column {
        if (is_string($transform)) {
            $transform = Transform::__callMethod($transform);
        }

        $column = [
            'label' => $label,
            'key' => $key,
            'searchable' => $searchable,
            'filterable' => $filterable,
            'transform' => $transform,
            'exportable' => $exportable,
            'sortable' => $sortable,
            'is_html' => (bool)$transform
        ];

        return new static($column);
    }

    /**
     * To add an image column
     * @param array $config
     * @return $this
     */
    public function image(array $config = [])
    {
        $this->columns['image'] = true;
        $this->columns['imageConfig'] = $config;
        $this->columns['is_html'] = true;
        return $this->transform(function ($value) use ($config) {
            if ($value) {
                $value = getStorageUrl($value);
                $width = $config['width'] ?? 36;
                $height = $config['height'] ?? 36;
                $style = match ($config['style'] ?? '') {
                    'rounded' => 'rounded-md',
                    'circle' => 'rounded-full',
                    'square' => 'rounded-none',
                    default => 'rounded-md',
                };
                return '<div class="block text-center"><a href="javascript:" @click="thumbnailSrc = \'' . $value . '\'; openThumbnail = true"><img src="' . $value . '" alt="' . ($config['alt'] ?? '') . '" class="'.$style.'" style="width:'.$width.'px;height: '.$height.'px"></a></div>';
            } else {
                return '<div class="bg-gray-200 w-10 h-10 rounded-md"><img src="' . config('cb.no_image_browse') . '"/></div>';
            }
        });
    }


    /**
     * To add a link column
     * @param array $config
     * @return $this
     */
    public function link(array $config = []): static
    {
        $this->columns['is_html'] = true;
        return $this->transform(function ($value) use ($config) {
            if ($value) {
                return '<a href="' . $value . '" target="_blank">' . ($config['text'] ?? $value) . '</a>';
            } else {
                return '';
            }
        });
    }
}
