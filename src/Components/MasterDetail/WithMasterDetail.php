<?php

namespace CrudBooster\Components\MasterDetail;

use CrudBooster\Attributes\OnBrowseHydrate;
use CrudBooster\Attributes\OnBrowseMounting;
use CrudBooster\Attributes\OnFormHydrate;
use CrudBooster\Attributes\OnFormMounted;
use CrudBooster\Attributes\OnFormSaving;
use CrudBooster\Attributes\OnPropertyUpdated;
use Log;

trait WithMasterDetail
{
    public $subModules = [];
    public function addSubModule(array $subModules): void
    {
        // validate subModules should be SubModule instance on each array
        foreach ($subModules as $i=>$subModule) {
            if (!$subModule instanceof SubModule) {
                throw new \Exception("SubModule should be instance of SubModule");
            }
            $subModules[$i] = $subModule->__getData();
        }

        $this->subModules = $subModules;
    }

    #[OnBrowseMounting]
    #[OnBrowseHydrate]
    public function __masterDetailBrowseFilter(): void
    {
        if($this->foreignKey && $this->foreignKeyFilter && method_exists($this, 'hookQuery')) {
            $this->hookQuery(function ($query){
                if (!str_contains($this->foreignKey, '.')) {
                    $foreignKey = $this->modelService::new()->getTable() . '.' . $this->foreignKey;
                } else {
                    $foreignKey = $this->foreignKey;
                }
                $query->where($foreignKey, $this->foreignKeyFilter);
            });
        }
    }

    #[OnFormSaving]
    public function __masterDetailFormSave(): void
    {
        if($this->foreignKey && $this->foreignKeyFilter) {
            $this->formData[$this->foreignKey] = $this->foreignKeyFilter;
        }
    }

    #[OnFormSaving]
    public function __masterDetailAutoRedirectDetail()
    {
        if(count($this->subModules) > 0) {
            $this->redirectDetailOnSave = true;
        }
    }

    #[OnFormMounted(order: 100)]
    #[OnFormHydrate(order: 100)]
    #[OnPropertyUpdated(order: 100)]
    public function __masterDetailHideForeignKeyField($model = null): void
    {
        if(isset($this->formColumns) && $this->foreignKey) {
            foreach ($this->formColumns as $i => $column) {
                if (is_array($column) && !isset($column['label'])) {
                    foreach ($column as $s => $subColumn) {
                        if($subColumn['key'] == $this->foreignKey) {
                            $this->formColumns[$i][$s]['visible'] = false;
                        }
                    }
                } else {
                    if($column['key'] == $this->foreignKey) {
                        $this->formColumns[$i]['visible'] = false;
                    }
                }
            }
        }
    }
}
