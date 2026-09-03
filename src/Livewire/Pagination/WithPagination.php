<?php

namespace CrudBooster\Livewire\Pagination;

use Livewire\WithPagination as livewireWithPagination;

trait WithPagination
{
    use livewireWithPagination;
    public $perPage = 10;
}