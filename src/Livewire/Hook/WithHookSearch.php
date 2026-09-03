<?php

namespace CrudBooster\Livewire\Hook;

use Illuminate\Database\Eloquent\Builder;

trait WithHookSearch
{
    protected array $hookSearch = [];

    /**
     * To manipulate search query before fetching data
     * @param \Closure $query
     * @return void
     */
    public function hookSearch(\Closure $query): void
    {
        if($this->hookSearch && count($this->hookSearch) > 0) {
            $this->hookSearch[] = $query;
        } else {
            $this->hookSearch = [$query];
        }
    }

    protected function __getHookSearch()
    {
        return $this->hookSearch;
    }
} 