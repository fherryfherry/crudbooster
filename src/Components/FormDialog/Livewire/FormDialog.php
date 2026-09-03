<?php

namespace CrudBooster\Components\FormDialog\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class FormDialog extends Component
{
    public $module;
    public $foreignKey;
    public $foreignKeyValue;

    #[Reactive]
    public $formDialogShow;

    #[Reactive]
    public $formDialogId;

    public function mount($module, $foreignKey, $foreignKeyValue)
    {
        $this->module = $module;
        $this->foreignKey = $foreignKey;
        $this->foreignKeyValue = $foreignKeyValue;
    }

    public function closeForm()
    {
        $this->dispatch('closeFormDialog');
    }

    public function render() {
        return view('cb.components::FormDialog.views.form_dialog');
    }
}
