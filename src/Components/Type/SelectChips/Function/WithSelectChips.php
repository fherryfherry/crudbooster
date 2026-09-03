<?php

namespace CrudBooster\Components\Type\SelectChips\Function;

use CrudBooster\Attributes\OnFormGetData;
use CrudBooster\Attributes\OnFormMounted;
use CrudBooster\Attributes\OnFormSaved;

trait WithSelectChips
{
    /**
     * Listen when form mounted
     * Initialize select chips with empty array since we use entangle in livewire
     * @param $model
     * @return void
     */
    #[OnFormMounted]
    public function selectChips($model): void
    {
        $this->formColumnsCallbackOnType('selectChips', function ($column) {
            $this->formData[$column['key']] = $this->formData[$column['key']] ?? [];
        });
        // Ignore save
        $this->selectChipsIgnoreDefaultSave();
    }

    /**
     * Select chips ignore default save
     * Since the key is not in the main table, we need to ignore it
     * @return void
     */
    private function selectChipsIgnoreDefaultSave(): void
    {
        $ignoreFields = [];
        $this->formColumnsCallbackOnType('selectChips', function ($column) use (&$ignoreFields) {
            $ignoreFields[] = $column['key'];
        });
        if ($ignoreFields) {
            $this->ignoreSave = array_merge($this->ignoreSave, $ignoreFields);
        }
    }

    /**
     * Listen when form saved
     * After form saved then save select chips data
     * @param $uuid
     * @return void
     */
    #[OnFormSaved]
    public function selectChipsListenPostFormSave($model, $data, $uuid = null): void
    {
        $this->formColumnsCallbackOnType('selectChips', function ($column) use ($uuid) {
            $this->selectChipsSaveProcess($uuid, $column);
        });
    }

    /**
     * Get data for select chips
     * Populate the current data for select chips
     * @return void
     */
    #[OnFormGetData]
    public function selectChipsLoadData($model, $data, $uuid = null): void
    {
        $this->formColumnsCallbackOnType('selectChips', function ($column) use ($uuid) {
            // And these bellow will be executed when formUuid is not empty means we are editing existing data
            $currentModelData = $this->modelService::findById($uuid);
            $currentModelPk = (new $this->modelName())->getKeyName();
            $selectedIds = $column['option']['model']::where($column['option']['firstForeignKey'], $currentModelData->{$currentModelPk})
                ->pluck($column['option']['secondForeignKey'])->toArray();
            $displayModelPk = (new $column['option']['displayModel']())->getKeyName();
            $displayData = $column['option']['displayModel']::whereIn($displayModelPk, $selectedIds)->get();
            $displayColumn = $column['option']['displayColumn'];
            $this->formData[$column['key']] = $displayData->map(function ($item) use ($displayModelPk, $displayColumn) {
                return ['key' => $item->{$displayModelPk}, 'label' => $item->{$displayColumn}];
            })->toArray();
        });
    }

    /**
     * Save select chips data function process
     * @param $uuid
     * @param array $column
     * @return void
     */
    protected function selectChipsSaveProcess($uuid, array $column): void
    {
        // get main table primary key from model
        $primaryKey = (new $this->modelName())->getKeyName();
        $service = $this->modelService::findById($uuid);

        // Remove chips that not selected
        $this->selectChipsRemoveUnused($column, $service, $primaryKey);

        foreach ($this->formData[$column['key']] as $selectChip) {
            if (!$targetModel = $column['option']['model']::where($column['option']['firstForeignKey'], $service->{$primaryKey})
                ->where($column['option']['secondForeignKey'], $selectChip['key'])->first()) {
                $targetModel = new $column['option']['model']();
            }
            $targetModel->{$column['option']['firstForeignKey']} = $service->{$primaryKey};
            $targetModel->{$column['option']['secondForeignKey']} = $selectChip['key'];
            $targetModel->save();
        }
    }

    /**
     * Remove unused chips
     * @param $column
     * @param $service
     * @param $primaryKey
     * @return void
     */
    private function selectChipsRemoveUnused($column, $service, $primaryKey): void
    {
        $this->formData[$column['key']] = $this->formData[$column['key']] ?? [];
        $existingChips = $column['option']['model']::where($column['option']['firstForeignKey'], $service->{$primaryKey})->pluck($column['option']['secondForeignKey'])->toArray();
        $selectedChips = array_column($this->formData[$column['key']], 'key');
        $chipsToDelete = array_diff($existingChips, $selectedChips);
        if (!empty($chipsToDelete)) {
            $column['option']['model']::where($column['option']['firstForeignKey'], $service->{$primaryKey})
                ->whereIn($column['option']['secondForeignKey'], $chipsToDelete)
                ->delete();
        }
    }
}
