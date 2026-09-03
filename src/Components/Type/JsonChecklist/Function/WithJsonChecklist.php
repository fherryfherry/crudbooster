<?php

namespace CrudBooster\Components\Type\JsonChecklist\Function;

use Illuminate\Support\Str;

trait WithJsonChecklist
{
    public array $__jsonCheckHorizontalStates = [];
    /**
     * To tick all horizontal checklist
     * @param $columnKey
     * @param $item
     * @return void
     */
    public function __jsonCheckListTickHorizontal($columnKey, $item): void
    {
        $this->__jsonCheckHorizontalStates[$columnKey][$item] = !($this->__jsonCheckHorizontalStates[$columnKey][$item]??false);

        $targetColumn = collect($this->formColumns)->firstWhere("key", $columnKey);
        $data = collect($targetColumn['option']['data'])->firstWhere('slug', $item);
        foreach ($data['checklist'] as $check) {
            $this->formData[$columnKey][$item][ Str::slug(strtolower($check)) ] = $this->__jsonCheckHorizontalStates[$columnKey][$item];
        }
    }
}
