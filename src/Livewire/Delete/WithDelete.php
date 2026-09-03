<?php

namespace CrudBooster\Livewire\Delete;

use CrudBooster\Attributes\WithAttributeCaller;

trait WithDelete
{
    use WithAttributeCaller;

    public $deleteId;
    public function deleteConfirmation($uuid): void
    {
        $this->deleteId = $uuid;
        $this->showConfirmMessage('Delete Confirmation', 'Are you sure you want to delete this data?', 'delete','Yes');
    }
    public function delete(): void
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $browsePath = $this->browsePath ?? 'dashboard';
            $this->redirectIntended(getCmsUrl($browsePath), navigate: true);
            return;
        }

        // Validation
        if(!$this->deleteId) {
            $this->showAlertMessage('No data selected');
            return;
        }
        // Get the data
        $data = $this->modelService::findById($this->deleteId);

        // Dispatch event before delete
        $this->callOnDataDeleting($this->modelName, $data, $this->deleteId);

        // Delete process
        $this->modelService::deleteById($this->deleteId);

        // Dispatch event after delete
        $this->callOnDataDeleted($this->modelName, $data, $this->deleteId);

        // Remove uuid from selectedIds
        $this->selectedIds = array_diff($this->selectedIds, [$this->deleteId]);
        $this->confirmMessageClose();
        $this->showAlertMessage('The data has been deleted successfully');
    }
}
