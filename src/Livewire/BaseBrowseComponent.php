<?php

namespace CrudBooster\Livewire;

use CrudBooster\Attributes\WithAttributeCaller;
use CrudBooster\Components\ActionButton\WithActionButton;
use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\BulkAction\WithBulkAction;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Components\ExportAction\WithExportAction;
use CrudBooster\Components\FilterSorting\WithFilterSorting;
use CrudBooster\Components\FormDialog\WithFormDialog;
use CrudBooster\Components\ImportAction\WithImportAction;
use CrudBooster\Components\MasterDetail\WithMasterDetail;
use CrudBooster\Components\TableActionButton\TableActionButton;
use CrudBooster\Components\TableActionButton\WithTableActionButton;
use CrudBooster\Livewire\ColumnBuilder\WithColumnBuilder;
use CrudBooster\Livewire\Delete\WithDelete;
use CrudBooster\Livewire\Hook\WithHookQuery;
use CrudBooster\Livewire\Hook\WithHookSearch;
use CrudBooster\Livewire\Pagination\WithPagination;
use CrudBooster\Modules\Auth\Livewire\WithLogoutAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;

abstract class BaseBrowseComponent extends BaseBrowseAbstract
{
    use WithColumnBuilder;
    use WithPagination;
    use WithAlertMessage;
    use WithConfirmMessage;
    use WithFilterSorting;
    use WithLogoutAction;
    use WithDelete;
    use WithExportAction;
    use WithImportAction;
    use WithBulkAction;
    use WithHookQuery;
    use WithActionButton;
    use WithAttributeCaller;
    use WithTableActionButton;
    use WithFormDialog;
    use WithMasterDetail;
    use WithHookSearch;

    protected array $columns = [];

    public $__view = 'cb.themes::browse';
    protected ?LengthAwarePaginator $result = null;
    public $actionButtonMode = 'inline'; // inline or threedot
    public $filterDraft = [];
    public $ref = null;
    public $foreignKeyValue = null;

    public function applyAdvancedFilter()
    {
        $this->filter = $this->filterDraft;
        $this->saveToSession();
    }

    public function resetAdvancedFilter()
    {
        $this->filterDraft = [];
        $this->filter = [];
        $this->saveToSession();
    }

    /**
     * Initialize filterDraft when filter popup is opened
     */
    public function initializeFilterDraft()
    {
        $this->filterDraft = $this->filter;
    }

    /**
     * Save current state to session for export
     */
    protected function saveToSession()
    {
        $moduleKey = $this->moduleKey ?? $this->browsePath;
        if ($moduleKey) {
            // Add unique identifier for sub modules to avoid session collision
            $sessionKey = $this->foreignKeyValue ? "{$moduleKey}_{$this->foreignKeyValue}" : $moduleKey;
            session([
                "cb_filter_{$sessionKey}" => $this->filter,
                "cb_search_{$sessionKey}" => $this->search,
                "cb_sortBy_{$sessionKey}" => $this->sortField,
                "cb_sortType_{$sessionKey}" => $this->sortDirection,
            ]);
        }
    }

    /**
     * Load state from session
     */
    protected function loadFromSession()
    {
        $moduleKey = $this->moduleKey ?? $this->browsePath;
        if ($moduleKey) {
            // Add unique identifier for sub modules to avoid session collision
            $sessionKey = $this->foreignKeyValue ? "{$moduleKey}_{$this->foreignKeyValue}" : $moduleKey;
            $this->filter = session("cb_filter_{$sessionKey}", []);
            $this->search = session("cb_search_{$sessionKey}", '');
            $this->sortField = session("cb_sortBy_{$sessionKey}", $this->sortField);
            $this->sortDirection = session("cb_sortType_{$sessionKey}", $this->sortDirection);
        }
    }

    /**
     * Handle search update and save to session
     */
    public function updatedSearch()
    {
        $this->saveToSession();
    }

    public function mount($moduleKey = null,
                          $foreignKey = null,
                          $foreignKeyValue = null,
                          $withHeader = true,
                          $formDialog = false,
                          $tableTitle = 'Browse Data',
                          $ref = null,
                          $encryptedParentModule = null): void // ADD encryptedParentModule param
    {
        $this->withHeader = $withHeader;
        $this->tableTitle = $tableTitle;
        $this->moduleKey = $moduleKey; 
        $this->foreignKey = $foreignKey;
        $this->foreignKeyFilter = $foreignKeyValue;
        $this->foreignKeyValue = $foreignKeyValue; // Store foreignKeyValue for session key
        $this->formDialog = $formDialog;
        $this->ref = $ref; 
        
        // If moduleKey is provided (SubModule), override browsePath before initialization
        if ($moduleKey) {
            $this->browsePath = $moduleKey;
        }
        
        // Store encrypted parent module for secure URL generation
        if ($encryptedParentModule) {
            session(['encrypted_parent_module_' . $this->browsePath => $encryptedParentModule]);
        }
        
        // Set module key for action button isolation
        if ($moduleKey) {
            \CrudBooster\Components\ActionButton\ActionButton::__setModuleKey($moduleKey);
        }
        
        $this->callOnBrowseMounting();
        
        // Call init method for browse component initialization
        $this->init();
        
        $this->checkAuthorization();
        
        // Load state from session if SubModule is in non-dialog mode
        if (!$formDialog) {
            $this->loadFromSession();
        }

        // Initialize filterDraft dengan nilai filter saat ini
        $this->filterDraft = $this->filter;

        // Inisialisasi awal result agar selalu ter-set sebelum rendering
        $this->result = new LengthAwarePaginator(collect([]), 0, $this->perPage);
    }

    /**
     * Navigate back using ref parameter or fallback to dashboard
     */
    public function goBack()
    {
        if ($this->ref) {
            $this->redirect(urldecode($this->ref), navigate: true);
        } else {
            $this->redirect(getCmsUrl('dashboard'), navigate: true);
        }
    }

    private function checkAuthorization()
    {
        if (!auth()->user()->can('read', $this->module['key'])) {
            $this->showAlertMessage('You are not authorized to access this page', 'warning');
            $this->redirect(getCmsUrl('dashboard'), navigate: true);
        }
    }

    public function getPaginate($perPage = 10, $browseColumns = [], array $hookQuery = []): LengthAwarePaginator
    {
        $this->callOnBrowseQueryCreating();
        return $this->modelService::getPaginate(
            filter: $this->filter,
            search: $this->search,
            sortBy: $this->sortField,
            sortType: $this->sortDirection,
            perPage: $perPage,
            columns: $browseColumns,
            hookQuery: $hookQuery,
            hookSearch: $this->__getHookSearch());
    }

    private function getFilterableColumns(): array
    {
        $filterableColumns = array_filter($this->__getBrowseColumn(true), fn($column) => $column['filterable']);
        
        return array_map(function ($column) {
            $column['key'] = str_replace('.', '__', $column['key']);
            
            // Handle select_query filter type
            if (isset($column['filter_type']) && $column['filter_type'] === 'select_query' && isset($column['filter_options'])) {
                $options = $column['filter_options'];
                if (isset($options['query_closure']) && is_callable($options['query_closure'])) {
                    $queryClosure = $options['query_closure'];
                    $query = $queryClosure();
                    $column['filter_options']['query_options'] = $query->get()->map(function ($item) {
                        return [
                            'value' => $item->value,
                            'label' => $item->label
                        ];
                    })->toArray();
                    // Remove closure before passing to Blade
                    unset($column['filter_options']['query_closure']);
                }
            }
            
            return $column;
        }, $filterableColumns);
    }

    private function constructChildRows($parentColumn, $parentValue)
    {
        if ($parentValue == null) return null;

        return $this->modelService::getPaginate(
            filter: $this->filter,
            search: $this->search,
            sortBy: $this->sortField,
            sortType: $this->sortDirection,
            perPage: 5000,
            columns: $this->__getBrowseColumn(),
            hookQuery: [fn(Builder $query) => $query->where($parentColumn, $parentValue)],
            hookSearch: $this->__getHookSearch()
        );
    }

    private function interceptRowData(LengthAwarePaginator $result)
    {    
        if ($this->__getBrowseColumn()) {            
            $result->getCollection()->transform(function ($row) {
                foreach ($this->__getBrowseColumn() as $column) {
                    $row = $this->callOnBrowseColumnRendering($this->modelName, $row, $column);
                }

                $row->__buttonDeleteVisible = !isset($this->__hideDeleteButtonWhen) || !$this->__getHideDeleteButtonWhen($row);
                $row->__buttonEditVisible = !isset($this->__hideEditButtonWhen) || !$this->__getHideEditButtonWhen($row);
                $row->__buttonDetailVisible = !isset($this->__buttonDetailCondition) || !$this->__getHideDetailButtonWhen($row);
                $row->__checkboxVisible = !isset($this->__hideCheckboxWhen) || !$this->__getHideCheckboxWhen($row);

                if ($this->browseDraggable) {
                    $row->__childRows = $this->constructChildRows($this->browseDraggableParentColumn, $row->{(new $this->modelName)->getKeyName()});
                }

                return $row;
            });
        }
        return $result;
    }

    public function render()
    {
        $this->result = $this->getPaginate($this->perPage, $this->__getBrowseColumn(), $this->__getHookQuery());
        $this->result = $this->interceptRowData($this->result);
        $this->callOnBrowseRendering($this->modelName);
        $filterable = $this->getFilterableColumns();
        $this->hideBulkActionOnEmpty();

        return view($this->__view, [
            'tableActionButtons' => TableActionButton::__getOption(),
            'filterable' => $filterable,
            'result' => $this->result,
            'actionButtonMode' => $this->actionButtonMode
        ])->layout($this->layout)->title($this->pageTitle);
    }

    /**
     * Public getter for exportable columns (tanpa closure)
     */
    public function getExportableColumns()
    {
        return $this->__getBrowseColumn(false);
    }
}
