<?php

namespace CrudBooster\Livewire;

use CrudBooster\Attributes\WithAttributeCaller;
use CrudBooster\Domain\Services\ServiceContract;
use CrudBooster\Modules\ModuleRegistrar;
use Livewire\Component;

abstract class BaseModuleAbstract extends Component
{
    use WithAttributeCaller;

    public $moduleKey = null;
    protected $modelService;
    protected $modelName;
    public $__view;
    public $layout = "cb.themes::layout-app";
    public $pageTitle;
    public string $redirectBackPath;
    public string $browsePath;
    public ?array $module;

    public function boot()
    {
        // Set browse path
        $this->__initBrowsePath();
        // Set module
        $this->__initModule();
    }

    public function hydrate()
    {
        $this->__formInit();
        $this->callOnFormHydrate();
        $this->callOnBrowseHydrate();
    }

    public function dehydrate()
    {
        $this->callOnFormDehydrate();
    }

    public function updated()
    {
        $this->init();
        $this->callOnPropertyUpdated();
    }

    public function rendering()
    {
        // Initialize module column configuration
    }

    public function rendered()
    {
    }

    public function __formInit()
    {
        $this->init();
        $this->callOnFormInit($this->modelName);
    }

    public function init() {}

    /**
     * Detect browse path
     * @return void
     */
    public function __initBrowsePath(): void
    {
        if(!empty($this->browsePath)) return;
        $url = url()->current();
        $this->browsePath = explode('/', trim(str_replace(getCmsUrl('/'), '', $url), '/'))[0];
        $this->redirectBackPath = $this->redirectBackPath ?? $this->browsePath; // Default redirect back path
        $this->redirectBackPath = request('ref') ? sanitizeUrl(request('ref'), $this->redirectBackPath) : $this->redirectBackPath;
    }

    public function __initModule(): void
    {
        $this->module = ModuleRegistrar::getModules()[$this->moduleKey ?? $this->browsePath];
        if($this->module['additional']['permissionAvailable']??false) {
            $this->module['additional']['permissionAvailable'] = array_map(function($permission) {
                return $permission->name;
            }, $this->module['additional']['permissionAvailable']);
        }
    }

    /**
     * Get the model service class
     * @return string|null
     */
    public function getModelService(): ?string
    {
        return $this->modelService;
    }

    /**
     * Get the model service class statically
     * @return string|null
     */
    public static function getModelServiceStatic(): ?string
    {
        return static::$modelService ?? null;
    }
}
