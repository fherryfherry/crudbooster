<?php

namespace CrudBooster\Livewire;

use Closure;
use CrudBooster\Attributes\OnBrowseRendering;

abstract class BaseBrowseAbstract extends BaseModuleAbstract
{
    public $buttonSearch = true;
    public $buttonCreate = true;
    public $buttonImport = true;
    public $buttonExportXls = true;
    public $buttonExportCsv = true;
    public $buttonExportPdf = true;
    public $buttonDelete = true;
    public $buttonEdit = true;
    public $buttonDetail = true;
    public $buttonFilter = true;
    public $buttonBulkAction = true;
    public $buttonAction = true;
    public $buttonActionStyle = 'ICON_ONLY'; // ICON_ONLY, TEXT_ONLY, ICON_TEXT
    public $withHeader = true;
    public $tableTitle = 'Browse Data';
    public $foreignKey = null;
    public $foreignKeyFilter = null;
    protected $__hideDeleteButtonWhen;
    protected $__hideDetailButtonWhen;
    protected $__hideEditButtonWhen;

    protected $__freezeMode = false;

    /**
     * Freeze mode will disable all button action
     * @param bool $enable
     * @return void
     */
    public function freezeMode(bool $enable)
    {
        $this->__freezeMode = $enable;
    }
    #[OnBrowseRendering]
    public function freezeModeAction($model)
    {
        if($this->__freezeMode) {
            $this->buttonBulkAction = false;
            $this->buttonCreate = false;
            $this->buttonDelete = false;
            $this->buttonEdit = false;
            $this->buttonAction = false;
        }
    }

    /**
     * To developer set hide delete button condition
     * @param Closure|string $columnName
     * @param string|null $equalValue
     * @return void
     */
    public function hideDeleteButtonWhen(Closure|string $columnName, $equalValue = null): void
    {
        if($columnName instanceof Closure) {
            $this->__hideDeleteButtonWhen = $columnName;
            return;
        }

        $this->__hideDeleteButtonWhen = function ($row) use ($columnName, $equalValue) {
            return $row->{$columnName} == $equalValue;
        };
    }
    protected function __getHideDeleteButtonWhen($row): bool
    {
        return call_user_func($this->__hideDeleteButtonWhen, $row);
    }

    /**
     * To developer set hide edit button condition
     * @param Closure|string $columnName
     * @param null $equalValue
     * @return void
     */
    public function hideEditButtonWhen(Closure|string $columnName, $equalValue = null): void
    {
        if($columnName instanceof Closure) {
            $this->__hideEditButtonWhen = $columnName;
            return;
        }

        $this->__hideEditButtonWhen = function ($row) use ($columnName, $equalValue) {
            return $row->{$columnName} == $equalValue;
        };
    }
    protected function __getHideEditButtonWhen($row): bool
    {
        return call_user_func($this->__hideEditButtonWhen, $row);
    }

    /**
     * To developer set hide detail button condition
     * @param Closure|string $columnName
     * @param null $equalValue
     * @return void
     */
    public function hideDetailButtonWhen(Closure|string $columnName, $equalValue = null): void
    {
        if($columnName instanceof Closure) {
            $this->__hideDetailButtonWhen = $columnName;
            return;
        }

        $this->__hideDetailButtonWhen = function ($row) use ($columnName, $equalValue) {
            return $row->{$columnName} == $equalValue;
        };
    }
    protected function __getHideDetailButtonWhen($row): bool
    {
        return call_user_func($this->__hideDetailButtonWhen, $row);
    }
}
