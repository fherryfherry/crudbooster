<?php

namespace CrudBooster\Livewire\ColumnBuilder;

use CrudBooster\Attributes\OnBrowseColumnRendering;
use CrudBooster\Attributes\WithAttributeCaller;
use Illuminate\Database\Eloquent\Builder;

trait WithColumnBuilder
{
    use WithAttributeCaller;

    protected $_browseColumns = []; // For internal use include all closure
    public $browseColumns = []; // For liveware use not include closure
    public $browseDraggable = false;
    public $browseDraggableSortingColumn = null;
    public $browseDraggableParentColumn = null;

    /**
     * This method is used to get the browse column with closure or not,
     * since livewire can't pass closure to view
     * @param $includeClosure
     * @return array|mixed|mixed[]
     */
    protected function __getBrowseColumn($includeClosure = true)
    {
        if($includeClosure) return $this->_browseColumns;

        return collect($this->_browseColumns)->map(function ($column) {
            // clean callable
            $column = array_filter($column, function ($value) {
                return !($value instanceof \Closure);
            });
            // clean relation joinClause
            if (isset($column['relation'])) unset($column['relation']['joinClause']);
            if (isset($column['filter_options'])) unset($column['filter_options']);
            return $column;
        })->toArray();
    }

    /**
     * This method is used to construct the browse columns with key map, for makeColumns
     * @param array $columns
     * @return array
     */
    private function constructBrowseColumnsWithKeyMap(array $columns): array
    {
        $browseColumns = [];
        foreach ($columns as $column) {
            $columnMap = $column->get();
            $key = isset($columnMap['relation']) ? $columnMap['relation']['key'] : $columnMap['key'];
            $browseColumns[$key] = $columnMap;
        }
        return $browseColumns;
    }

    /**
     * This method is used to make the columns
     *
     * @param array $columns
     * @return $this
     */
    public function makeColumns(array $columns)
    {
        $this->_browseColumns = $this->constructBrowseColumnsWithKeyMap($columns);
        $this->browseColumns = $this->__getBrowseColumn(false);
        return $this;
    }

    /**
     * This method is used to make the column draggable
     * @param string $orderField
     * @param string|null $parentField
     * @return void
     */
    public function draggable(string $orderField, ?string $parentField = null)
    {
        $this->browseDraggable = (bool) $orderField;
        $this->browseDraggableSortingColumn = $orderField;
        $this->browseDraggableParentColumn = $parentField;
        $this->sortField = $orderField ?? (new $this->modelName)->getKeyName();
        $this->sortDirection = $orderField ? 'asc' : $this->sortDirection;
        // Make sure the first list is parent
        $this->hookQuery(function (Builder $query) use ($parentField) {
            $query->whereNull($parentField);
        });
    }

    /**
     * This method is used to update the order of the draggable row
     * @param $updatedIds
     * @return void
     */
    public function updateDraggableOrder($updatedIds): void
    {
        collect($updatedIds)->each(function ($value, $index) {
            $update = $this->modelService::findById($value['key']);
            $update->menu_order = $value['index'];
            $update->save();
        });
        $this->callOnDragged($updatedIds);
        $this->showAlertMessage('Ordering data has been updated', 'success', 30);
    }

    /**
     * This function is used to call all methods contained in OnBrowseColumnRendering attribute
     * To transform the column value
     * @param $model
     * @param $row
     * @param $column
     * @return mixed
     */
    #[OnBrowseColumnRendering]
    public function __transformHandleColumn($model, $row, $column)
    {
        if(isset($column['transform']) && is_callable($column['transform'])) {
            $row->{$column['key']} = $column['transform']($row->{$column['key']});
        }
        // transform with row
        if(isset($column['transformWithRow']) && is_callable($column['transformWithRow'])) {
            $row->{$column['key']} = $column['transformWithRow']($row);
        }
        return $row;
    }
}
