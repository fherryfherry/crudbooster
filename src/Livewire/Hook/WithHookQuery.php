<?php

namespace CrudBooster\Livewire\Hook;

trait WithHookQuery
{
    protected array $hookQuery = [];

    /**
     * To manipulate query before fetching data
     * @param \Closure $query
     * @return void
     */
    public function hookQuery(\Closure $query): void
    {
        if($this->hookQuery && count($this->hookQuery) > 0) {
            $this->hookQuery[] = $query;
        } else {
            $this->hookQuery = [$query];
        }
    }

    protected function __getHookQuery()
    {
        return $this->hookQuery;
    }
}
