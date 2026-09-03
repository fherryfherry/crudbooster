<?php

namespace CrudBooster\Components\Type\JsonChecklist\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;
use InvalidArgumentException;

class JsonChecklist extends TypeOptionAbstract
{
    /**
     * To set dataset for json checklist
     * @param array $data E.g [['key'=>'key1','name'=>'Name 1'], ['key'=>'key2','name'=>'Name 2']]
     * @param string $itemLabel E.g: 'Item Label'
     * @param array $checklist E.g: ['Checklist 1','Checklist 2','Checklist 3']
     * @return JsonChecklist
     */
    public function dataset(array $data, string $itemLabel, array $checklist): JsonChecklist
    {
        // Validate key on data array
        if(!$data || count($data)==0) {
            throw new InvalidArgumentException("Invalid Argument: data array is required");
        }
        if(!isset($data[0]['key']) || !isset($data[0]['name'])) {
            throw new InvalidArgumentException("Invalid Argument: `key` and `name` key is required on data array");
        }

        $data = collect($data)->map(function ($item) use ($checklist) {
            $item['checklist'] = $item['checklist'] ?? $checklist;
            return $item;
        })->toArray();
        $this->option = array_merge($this->option,[
            'data' => $data,
            'item_label'=> $itemLabel,
            'checklist' => $checklist,
        ]);
        return $this;
    }
}
