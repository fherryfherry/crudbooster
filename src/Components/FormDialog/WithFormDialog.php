<?php

namespace CrudBooster\Components\FormDialog;

use Livewire\Attributes\On;

trait WithFormDialog
{
    public bool $formDialog = false;
    public ?string $formDialogShow = null;
    public ?string $formDialogId = null;

    public function openFormCreate()
    {
        $this->formDialogShow = 'CREATE';
    }

    public function openFormDetail($id)
    {
        $this->formDialogShow = 'DETAIL';
        $this->formDialogId = $id;
    }

    public function openFormEdit($id)
    {
        $this->formDialogShow = 'EDIT';
        $this->formDialogId = $id;
    }

    public function closeFormDialog()
    {
        $this->formDialogShow = null;
        $this->formDialogId = null;
    }

    #[On('closeFormDialog')]
    public function __listenCloseForm()
    {
        $this->closeFormDialog();
    }

}
