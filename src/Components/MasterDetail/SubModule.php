<?php

namespace CrudBooster\Components\MasterDetail;

use CrudBooster\Modules\ModuleRegistrar;

class SubModule
{
    private $moduleKey;
    private $moduleClass;
    private $foreignKey;
    private $localKey;
    private $tableTitle;
    private $openBehavior = 'dialog'; // ADD THIS LINE

    public function __construct($moduleClass)
    {
        $this->moduleClass = $moduleClass;
        $module = collect(ModuleRegistrar::getModules())->firstWhere('browseModuleClass', $moduleClass);
        $this->moduleKey = $module['key'];
        $this->tableTitle = $module['name'];
    }

    public static function create(string $moduleClass)
    {
        return new static($moduleClass);
    }

    public function foreignKey(string $foreignKey): self
    {
        $this->foreignKey = $foreignKey;
        return $this;
    }

    public function localKey(string $localKey): self
    {
        $this->localKey = $localKey;
        return $this;
    }

    public function tableTitle(string $tableTitle): self
    {
        $this->tableTitle = $tableTitle;
        return $this;
    }

    /**
     * Open SubModule in the same page instead of popup dialog
     */
    public function openInPage(): self
    {
        $this->openBehavior = 'page';
        return $this;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->moduleKey,
            'moduleClass' => $this->moduleClass,
            'foreignKey' => $this->foreignKey,
            'localKey' => $this->localKey,
            'tableTitle' => $this->tableTitle,
            'openBehavior' => $this->openBehavior,
        ];
    }

    public function __getData()
    {
        return $this->toArray();
    }

}
