<?php

namespace CrudBooster\Components\FilterSorting;

trait WithFilterSorting
{
    public $search;
    public $filter = [];
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public function sortBy($field): void
    {
        // check sortable
        $column = collect($this->__getBrowseColumn(false))->firstWhere('key', $field);
        if (!$column || !$column['sortable']) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        
        // Save to session for export
        if (method_exists($this, 'saveToSession')) {
            $this->saveToSession();
        }
    }

    /**
     * Initialize filter values for date range filters
     */
    public function updatedFilter($value, $key): void
    {
        $field = str_replace('filter.', '', $key);
        $field = str_replace('.value', '', $field);
        $field = str_replace('__', '.', $field);
        
        $column = collect($this->__getBrowseColumn(false))->firstWhere('key', $field);
        
        if ($column && isset($column['filter_type']) && $column['filter_type'] === 'date_range') {
            // Initialize date range structure if not exists
            if (!isset($this->filter[$field]['value'])) {
                $this->filter[$field]['value'] = ['start' => '', 'end' => ''];
            }
        }
        
        // Save to session for export
        if (method_exists($this, 'saveToSession')) {
            $this->saveToSession();
        }
    }
}
