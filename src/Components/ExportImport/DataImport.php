<?php

namespace CrudBooster\Components\ExportImport;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class DataImport implements ToModel, WithHeadingRow, WithBatchInserts, WithSkipDuplicates
{
    public $modelService;
    public function __construct($modelService)
    {
        $this->modelService = $modelService;
    }

    public function model(array $row)
    {
        return $this->modelService::import($row);
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
