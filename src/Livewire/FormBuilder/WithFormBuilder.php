<?php

namespace CrudBooster\Livewire\FormBuilder;

use Closure;
use CrudBooster\Attributes\OnFormDehydrate;
use CrudBooster\Attributes\OnFormGetData;
use CrudBooster\Attributes\OnFormGettingData;
use CrudBooster\Attributes\OnFormHydrate;
use CrudBooster\Attributes\OnFormInit;
use CrudBooster\Attributes\OnFormMounted;
use CrudBooster\Attributes\OnFormRendering;
use CrudBooster\Attributes\OnPropertyUpdated;
use CrudBooster\Components\Type\TypeOptionAbstract;
use Illuminate\Support\Facades\Log;

trait WithFormBuilder
{
    public bool $containRequired = false;
    public array $formColumns = [];
    protected array $__formColumns = [];
    private array $validationRules = [];
    private array $columnCallbackExcept = ['key', 'transform', 'transformValue', 'transformDisplay', 'type','onChange','showOn'];
    protected int $__bindingDebounce = 800;
    private static array $__tempOnChangeCaller = [];

    /**
     * To wrap the form columns
     * @param array $columns
     * @return void
     */
    public function makeForm(array $columns): void
    {
        foreach ($columns as $i => $column) {
            if (is_array($column) && !isset($column['label'])) {
                foreach ($column as $s => $subColumn) {
                    $columnMap = $subColumn->get();
                    $columns[$i][$s] = $columnMap;
                }
            } else {
                $columnMap = $column->get();
                $columns[$i] = $columnMap;
            }
        }
        $this->__formColumns = $columns;
        $this->formColumns = $columns;
        // We need unset the closure attribute since it can't be serialized by livewire
        $this->formColumns = $this->removeClosureAttribute($this->formColumns);
    }

    public function getFormColumns($includeClosure = false)
    {
        if ($includeClosure) return $this->__formColumns;
        return $this->formColumns;
    }

    #[OnFormInit]
    public function __checkValidationContainRequired($model): void
    {
        // Check if the form columns contain required attribute
        $this->containRequired = collect($this->formColumns)->contains(function ($column) {
            return isset($column['validation']) && str_contains($column['validation']['formData.' . $column['key']] ?? '', 'required');
        });
    }

    /**
     * To make a callback on each form columns
     * @param Closure $callback
     * @param bool $withClosure
     * @return void
     */
    public function formColumnsCallback(Closure $callback, bool $withClosure = false): void
    {
        $columns = $withClosure ? $this->__formColumns : $this->formColumns;
        foreach ($columns as $column) {
            if (is_array($column) && !isset($column['label'])) {
                foreach ($column as $subColumn) {
                    $callback($subColumn);
                }
            } else {
                $callback($column);
            }
        }
    }

    /**
     * To make a callback on each form columns with type
     * @param $type
     * @param Closure $callback
     * @return void
     */
    public function formColumnsCallbackOnType($type, Closure $callback): void
    {
        $this->formColumnsCallback(function ($column) use ($type, $callback) {
            if (isset($column['type']) && $column['type'] == $type) {
                $callback($column);
            }
        });
    }

    #[OnPropertyUpdated(order: 98)]
    #[OnFormHydrate(order: 98)]
    public function __clearClosureAttribute(): void
    {
        // We need re-unset the closure attribute since it can't be serialized by livewire
        $this->formColumns = $this->removeClosureAttribute($this->formColumns);
    }

    /**
     * This function is used to set the form validation
     * @param $validation
     * @param array $messages
     * @return void
     */
    private function setFormValidation($validation, array $messages = []): void
    {
        // remove empty array
        $validation = array_filter($validation);
        $messages = $messages ? array_filter($messages) : [];
        $this->formValidation = $validation;
        $this->formValidationMessage = $messages;
    }

    public function constructValidation()
    {
        $validationRules = [];
        $validationMessages = [];
        $this->formColumnsCallback(function ($column) use (&$validationRules, &$validationMessages) {
            if (isset($column['key'])) {
                $_column = collect($this->getFormColumns(true))->firstWhere('key', $column['key']);
                // We want to except the column that has showOn attribute and not visible, so we don't need to validate it
                if (isset($_column['showOn']) && !$_column['visible']) return;
                if (isset($_column['showEdit']) && !$_column['showEdit']) return;
                if (isset($_column['showCreate']) && !$_column['showCreate']) return;

                // Special handling for file type validation
                if (isset($column['type']) && $column['type'] === 'file') {
                    $existingValue = $this->formData[$column['key']] ?? null;
                    $hasExistingFile = $existingValue && !is_object($existingValue) && !empty($existingValue);
                    
                    // Also check if we have file in fileColumns (from WithFileInput trait)
                    if (!$hasExistingFile && isset($this->fileColumns[$column['key']])) {
                        $hasExistingFile = true;
                    }
                    
                    if (isset($column['validation']) && is_array($column['validation'])) {
                        $fieldValidation = $column['validation'];
                        foreach ($fieldValidation as $field => $rules) {
                            // If there's existing file and rule contains 'required', modify the rule
                            if ($hasExistingFile && str_contains($rules, 'required')) {
                                $rules = str_replace('required|', '', $rules);
                                $rules = str_replace('|required', '', $rules);
                                $rules = str_replace('required', '', $rules);
                                $rules = trim($rules, '|');
                                
                                // If rules become empty, skip this validation
                                if (empty($rules)) {
                                    continue;
                                }
                            }
                            $validationRules[$field] = $rules;
                        }
                    }
                } else {
                    // Normal validation for non-file types
                    if (isset($column['validation']) && is_array($column['validation'])) {
                        $validationRules = array_merge($validationRules, $column['validation']);
                    }
                }
                
                if (isset($column['validationMessage']) && is_array($column['validationMessage'])) {
                    $validationMessages = array_merge($validationMessages, $column['validationMessage']);
                }
            }
        });

        // Set validation rules
        $this->setFormValidation($validationRules, $validationMessages);
    }

    #[OnFormRendering]
    public function __callbackShowEditCreate($model = null)
    {
        foreach ($this->__formColumns as $i => $column) {
            if (is_array($column) && !isset($column['label'])) {
                foreach ($column as $s => $subColumn) {
                    if (isset($subColumn['showEdit']) && $this->formId) {
                        // Untuk detail view, tetap tampilkan field meskipun showEdit false
                        if ($this->__view === 'cb.themes::detail') {
                            $this->formColumns[$i][$s]['visible'] = true;
                        } else {
                            $this->formColumns[$i][$s]['visible'] = (bool) $subColumn['showEdit'];
                        }
                    }
                    if (isset($subColumn['showCreate']) && !$this->formId) {
                        $this->formColumns[$i][$s]['visible'] = (bool) $subColumn['showCreate'];
                    }
                }
            } else {
                if (isset($column['showEdit']) && $this->formId) {
                    // Untuk detail view, tetap tampilkan field meskipun showEdit false
                    if ($this->__view === 'cb.themes::detail') {
                        $this->formColumns[$i]['visible'] = true;
                    } else {
                        $this->formColumns[$i]['visible'] = (bool) $column['showEdit'];
                    }
                }
                if (isset($column['showCreate']) && !$this->formId) {
                    $this->formColumns[$i]['visible'] = (bool) $column['showCreate'];
                }
            }
        }
    }

    /**
     * Column showOn attribute handler
     */
    #[OnFormRendering]
    public function __callbackShowOn($model = null): void
    {
        foreach ($this->__formColumns as $i => $column) {
            if (is_array($column) && !isset($column['label'])) {
                foreach ($column as $s => $subColumn) {
                    if (isset($subColumn['showOn'])) {
                        $this->formColumns[$i][$s]['visible'] = (bool) $subColumn['showOn']($this->formData ?? []);
                    }
                }
            } else {
                if (isset($column['showOn'])) {
                    $this->formColumns[$i]['visible'] = (bool) $column['showOn']($this->formData ?? []);
                }
            }
        }
    }

    /**
     * To form column re-map based on callback
     * @param Closure $callback
     * @return void
     */
    public function formColumnMap(Closure $callback): void
    {
        $this->formColumns = collect($this->formColumns)->map(function ($column) use ($callback) {
            if (is_array($column) && !isset($column['label'])) {
                return collect($column)->map(function ($subColumn) use ($callback) {
                    return $callback($subColumn);
                })->toArray();
            } else {
                return $callback($column);
            }
        })->toArray();
    }

    /**
     * To filter form columns based on callback
     * @param $columns
     * @return array
     */
    private function removeClosureAttribute($columns): array
    {
        return array_map(function ($column) {
            $closureRemover = function ($column) use (&$closureRemover) {
                foreach ($column as $key => $val) {
                    if (!is_string($val) && ($val instanceof Closure || is_callable($val))) {
                        $column[$key] = null;
                    } else if (!is_string($val) && is_object($val)) {
                        $column[$key] = null;
                    } else if (is_array($val)) {
                        $column[$key] = $closureRemover($val);
                    }
                }
                return $column;
            };
            return $closureRemover($column);
        }, $columns);
    }

    /**
     * Executor all attribute with closure
     * @param null $model
     * @return void
     */
    #[OnFormHydrate]
    #[OnPropertyUpdated]
    #[OnFormMounted(order: 98)]
    public function __callbackColumnClosureAttr($model = null): void
    {
        if (!$this->getFormColumns(true) || !$this->formData) return;
        $row = $this->formData;
        $this->formColumns = collect($this->getFormColumns(true))->map(function ($column) use ($row) {
            if (is_array($column) && !isset($column['label'])) {
                return collect($column)->map(function ($subColumn) use ($row) {
                    foreach ($subColumn as $key => $val) {
                        if ($val instanceof Closure && !in_array($key, $this->columnCallbackExcept)) {
                            $subColumn[$key] = !is_string($val) && is_callable($val) ? call_user_func($val, $row) : $val;
                            $subColumn[$key] = $subColumn[$key] instanceof TypeOptionAbstract ? $subColumn[$key]->__getOption() : $subColumn[$key];
                        }
                    }
                    return $subColumn;
                })->toArray();
            } else {
                foreach ($column as $key => $val) {
                    if ($val instanceof Closure && !in_array($key, $this->columnCallbackExcept)) {
                        $column[$key] = !is_string($val) && is_callable($val) ? call_user_func($val, $row) : $val;
                        if ($column[$key] instanceof TypeOptionAbstract) {
                            $column[$key] = $column[$key]->__getOption();
                            foreach ($column[$key] as $k => $v) {
                                if (!is_string($v) && (is_callable($v) || $v instanceof Closure)) {
                                    $column[$key][$k] = call_user_func($v, $row);
                                }
                            }
                        }
                    }
                }
                return $column;
            }
        })->toArray();

        // Make sure form columns has no closure any more
        $this->formColumns = $this->removeClosureAttribute($this->formColumns);
    }

    #[OnFormInit]
    public function __bindingValueMounted($model): void
    {
        if (!$this->formColumns) return;
        $this->formColumnsCallback(function ($column) {
            if (isset($column['bindValue'])) {
                $bindTargetKey = $column['bindValue'];
                foreach ($this->formColumns as $i => $col) {
                    if (is_array($col) && !isset($col['label'])) {
                        foreach ($col as $e => $subCol) {
                            if ($subCol['key'] == $bindTargetKey) {
                                $this->formColumns[$i][$e]['live'] = $this->__bindingDebounce;
                            }
                        }
                    } else {
                        if ($col['key'] == $bindTargetKey) {
                            $this->formColumns[$i]['live'] = $this->__bindingDebounce;
                        }
                    }
                }
            }
        });
    }

    #[OnPropertyUpdated(order: 99)]
    #[OnFormHydrate(order: 99)]
    public function __bindingValue(): void
    {
        if (!$this->formData) return;
        $this->formColumnsCallback(function ($column) {
            if (isset($column['bindValue'])) {
                $this->formData[$column['key']] = $this->formData[$column['bindValue']] ?? null;
            }
        });
    }

    #[OnFormHydrate(order: 99)]
    #[OnPropertyUpdated(order: 99)]
    public function __transformValueOnUpdateProperty(): void
    {
        if (!$this->formData) return;
        $this->formColumnsCallback(function ($column) {
            if (isset($column['transform'])) {
                $this->formData[$column['key']] = $column['transform']($this->formData[$column['key']] ?? null);
            }
        }, true);
    }

    #[OnFormHydrate(order: 100)]
    #[OnPropertyUpdated(order: 100)]
    public function __transformValueForInput(): void
    {
        if (!$this->formData) return;
        $this->formColumnsCallback(function ($column) {
            if (isset($column['transformValue'])) {
                $this->formData[$column['key']] = $column['transformValue']($this->formData[$column['key']] ?? null);
            }
        }, true);
    }

    public function __onChangeFormField($columnKey, $value): void
    {
        if(!isset(static::$__tempOnChangeCaller[$columnKey])) {
            $this->formColumnsCallback(function ($column) use ($columnKey, $value) {
                if (isset($column['onChange']) && $column['key'] == $columnKey) {
                    if(is_string($value)) {
                        call_user_func($column['onChange'], $value);
                    }
                    static::$__tempOnChangeCaller[$columnKey] = true;
                }
            }, true);
        }
    }

    #[OnFormGettingData]
    public function __setDefaultValueFormData(): void
    {
        $this->formColumnsCallback(function ($column) {
            if (isset($column['default'])) {
                $this->formData[$column['key']] = $this->formData[$column['key']] ?? $column['default'];
            }
        });
    }

    #[OnFormGetData]
    public function __transformValueForFormData(): void
    {
        if (!$this->formData) return;
        $this->formColumnsCallback(function ($column) {
            if (isset($column['transformValue'])) {
                $this->formData[$column['key']] = $column['transformValue']($this->formData[$column['key']] ?? null);
            }
        }, true);
    }

    /**
     * Transform display values for detail page only
     * This method is called specifically for detail page display
     */
    public function __transformDisplayForDetail(): void
    {
        if (!$this->formData) return;
        $this->formColumnsCallback(function ($column) {
            if (isset($column['transformDisplay'])) {
                $this->formData[$column['key']] = $column['transformDisplay']($this->formData[$column['key']] ?? null);
            }
        }, true);
    }
}
