<?php

namespace CrudBooster\Components\BulkAction;

use AWS\CRT\Log;
use Closure;
use CrudBooster\Attributes\OnFormInit;
use CrudBooster\Modules\Role\Enum\RolePermission;
use Illuminate\Support\Facades\Gate;

trait WithBulkAction
{
    public $bulkActions = [];
    protected $_bulkActions = [];
    public $selectedIds = [];
    protected $__hideCheckboxWhen;

    private function hideBulkActionOnEmpty(): void
    {
        if (empty($this->bulkActions)) {
            $this->buttonBulkAction = false;
        }
    }

    /**
     * To developer set hide checkbox condition
     * @param Closure|string $columnName
     * @param null $equalValue
     * @return void
     */
    public function hideCheckboxWhen(Closure|string $columnName, $equalValue = null): void
    {
        if($columnName instanceof Closure) {
            $this->__hideCheckboxWhen = $columnName;
            return;
        }

        $this->__hideCheckboxWhen = function ($row) use ($columnName, $equalValue) {
            return $row->{$columnName} == $equalValue;
        };
    }

    protected function __getHideCheckboxWhen($row): bool
    {
        return call_user_func($this->__hideCheckboxWhen, $row);
    }

    /**
     * Toggle select all checkbox
     * @param $isChecked
     * @return void
     */
    public function triggerSelectAll($isChecked): void
    {
        if($isChecked) {
            $primaryKey = $this->modelService::getPrimaryKey();
            $this->selectedIds = $this->getPaginate(1000, $this->browseColumns)->pluck($primaryKey)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    /**
     * To add bulk action button
     * @param $label
     * @param string $icon
     * @param callable $action
     * @param $confirmTitle
     * @param $confirmText
     * @param RolePermission $permission
     * @return void
     */
    public function addBulkAction($label, string $icon, callable $action, $confirmTitle = null, $confirmText = null, RolePermission $permission = RolePermission::UPDATE): void
    {
        $this->_bulkActions[$label] = [
            'id' => md5($label),
            'label' => $label,
            'icon' => $icon,
            'action' => $action,
            'confirm_title' => $confirmTitle ?? 'Are you sure?',
            'confirm_text' => $confirmText ?? 'You won\'t be able to revert this!',
            'permission' => $permission,
        ];
    }

    private function makeBulkAction(): void
    {
        $this->bulkActions = collect($this->_bulkActions)
            ->filter(fn($bulk) => $bulk['visible'])
            ->map(function ($bulk) {
            return [
                'id' => $bulk['id'],
                'label' => $bulk['label'],
                'icon' => $bulk['icon'],
                'confirm_title' => $bulk['confirm_title'],
                'confirm_text' => $bulk['confirm_text'],
            ];
        })->toArray();
    }

    #[OnFormInit]
    public function __bulkActionOnFormInit(): void
    {
        // Merge with BulkAction static model
        $this->_bulkActions = array_merge($this->_bulkActions, BulkAction::__getBulkActions());

        collect($this->_bulkActions)->each(function ($bulk) {
            // if user is super admin
            if(Gate::check('is_super_admin')) {
                $this->_bulkActions[$bulk['label']]['visible'] = true;
            } else {
                if ($bulk['permission'] == RolePermission::CREATE && auth()->user()->can('create', $this->module['key'])) {
                    $this->_bulkActions[$bulk['label']]['visible'] = true;
                } elseif ($bulk['permission'] == RolePermission::READ && auth()->user()->can('read', $this->module['key'])) {
                    $this->_bulkActions[$bulk['label']]['visible'] = true;
                } elseif ($bulk['permission'] == RolePermission::UPDATE && auth()->user()->can('update', $this->module['key'])) {
                    $this->_bulkActions[$bulk['label']]['visible'] = true;
                } elseif ($bulk['permission'] == RolePermission::DELETE && auth()->user()->can('delete', $this->module['key'])) {
                    $this->_bulkActions[$bulk['label']]['visible'] = true;
                } else {
                    $this->_bulkActions[$bulk['label']]['visible'] = false;
                }
            }
        });
        $this->makeBulkAction();
    }

    /**
     * To process bulk action
     * @param $id
     * @return void
     */
    public function bulkActionProcess($id)
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $browsePath = $this->browsePath ?? 'dashboard';
            $this->redirectIntended(getCmsUrl($browsePath), navigate: true);
            return;
        }

        $this->init();
        // Validate selectedIds
        if (empty($this->selectedIds)) {
            $this->showAlertMessage( 'Please select at least one item', 'warning');
            $this->dispatch('closeModalBulkActionConfirm');
        } else {
            \Illuminate\Support\Facades\Log::debug("Bulk action process", [
                'selectedIds' => $this->selectedIds,
                'bulkActionId' => $id,
            ]);
            $bulk = collect($this->_bulkActions)->firstWhere('id', $id);
            if ($bulk) {
                call_user_func($bulk['action'], $this->selectedIds);
                $this->showAlertMessage( 'Bulk process `'.$bulk['label'].'` is completed','success');
                $this->dispatch('closeModalBulkActionConfirm');
            } else {
                $this->showAlertMessage('Bulk action not found','warning');
                $this->dispatch('closeModalBulkActionConfirm');
            }
        }
    }
}
