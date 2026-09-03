<?php

namespace CrudBooster\Modules\ModuleBuilder\Builder;

use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;

class ModuleBuilderCommon
{
    public function __construct(public array $form)
    {}

    public function fieldGroup()
    {
        $table = $this->form['table_name'] ?? $this->form['table'] ?? null;
        if(!$table) return [];

        // Add table from form and table from relationship to array
        $result = [['key' => $this->form['table_name'] ?? $this->form['table'], 'table' => $this->form['table_name'] ?? $this->form['table']]];
        foreach ($this->form['relationships'] ?? [] as $relationship) {
            if (isset($relationship['key'])) {
                $result[] = [
                    'key' => $relationship['key'],
                    'table' => $relationship['tableFirst']
                ];
            }
        }

        $groupResult = [];
        foreach ($result as $item) {
            if (($this->form['table_name'] ?? '') == $item['table']) {
                $fields = collect($this->form['schema'])->pluck('name')->toArray();
            } else {
                $fields = Schema::getColumnListing($item['table']);
            }
            $groupResult[] = [
                'table' => $item['key'],
                'fields' => $fields
            ];
        }

        return $groupResult;
    }
}
