<?php

namespace CrudBooster\Domain\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface ServiceContract
{
    public static function getFieldExceptPrimary(): array;
    public static function import(array $row);
    public static function getFields();
    public static function new(): Model;

    public static function query(): Builder;

    public static function findById($id);

    public static function getList();

    public static function getDetail($id);

    public static function create($data);

    public static function updateWithData($id, $data);

    public static function deleteData($id);

    public static function deleteById($id);

    public static function deleteByIds(array $ids);

    public static function countData();

    public static function find($id);

    public static function getPaginate(
        $filter = [],
        $search = null,
        $sortBy = null,
        $sortType = "asc",
        $perPage = 10,
        $columns = [],
        array $hookQuery = []
    );
}
