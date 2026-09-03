<?php

namespace CrudBooster\Livewire\ColumnBuilder;

class Relation
{
    private array $relations;

    public function __construct($relationItem) {
        $this->relations = $relationItem;
    }
    public function get()
    {
        return $this->relations;
    }
    public function select(array $columns)
    {
        $this->relations['select'] = $columns;
        return $this;
    }
    public static function add($key, $table, $first, $operator, $second, $type = 'left', $where = false): Relation
    {
        $relation = [
            'key' => $key,
            'table' => $table,
            'first'=> $first,
            'operator' => $operator,
            'second'=> $second,
            'type'=> $type,
            'where'=> $where
        ];

        return new static($relation);
    }

}