<?php

namespace CrudBooster\Attributes;

use CrudBooster\Events\EventBrowseColumnRendering;
use CrudBooster\Events\EventBrowseRendering;
use CrudBooster\Events\EventDataDeleted;
use CrudBooster\Events\EventDataDeleting;
use CrudBooster\Events\EventFormGetData;
use CrudBooster\Events\EventFormGettingData;
use CrudBooster\Events\EventFormMounting;
use CrudBooster\Events\EventFormSaved;
use CrudBooster\Events\EventFormSaving;
use CrudBooster\Events\EventFormValidated;
use Log;

trait WithAttributeCaller
{
    protected static $__attributeCallerInstance = [];

    /**
     * This function is used to call all methods contained in OnFormRendering attribute
     * @return void
     */
    private function callOnFormRendering()
    {
        $methods = OnFormRenderingReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                $this->{$method}();
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
    }

    /**
     * This function is used to call all methods contained in OnBrowseHydrate attribute
     */
    private function callOnBrowseHydrate(): void
    {
        $methods = OnBrowseHydrateReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$this->moduleKey.'_'.$method])) {
                $this->{$method}();
                static::$__attributeCallerInstance[$this->moduleKey.'_'.$method] = $method;
            }
        }
    }

    /**
     * This function is used to call all methods contained in OnBrowseQueryCreating attribute
     * @return void
     */
    private function callOnBrowseQueryCreating(): void
    {
        $methods = OnBrowseQueryCreatingReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$this->moduleKey.'_'.$method])) {
                $this->{$method}();
                static::$__attributeCallerInstance[$this->moduleKey.'_'.$method] = $method;
            }
        }
    }

    /**
     * This function is used to call all methods contained in OnBrowseMounting attribute
     * @return void
     */
    private function callOnBrowseMounting(): void
    {
        $methods = OnBrowseMountingReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$this->moduleKey.'_'.$method])) {
                $this->{$method}();
                static::$__attributeCallerInstance[$this->moduleKey.'_'.$method] = $method;
            }
        }
    }

    /**
     * This function is used to call all methods contained in OnDragged attribute
     * And called after dragged
     * @param array $ids
     */
    private function callOnDragged(array $ids): void
    {
        $methods = OnDraggedReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                $this->{$method}($ids);
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
    }

    /**
     * This function is used to call all methods contained in OnDataDeleted attribute
     * And called after data deleted
     * @param $model
     * @param $data
     * @param $id
     * @return void
     */
    private function callOnDataDeleted($model, $data, $id): void
    {
        $methods = OnDataDeletedReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 3) {
                    // Method expects 3 parameters: $model, $data, $id
                    $this->{$method}($model, $data, $id);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with all available parameters (backward compatibility)
                    $this->{$method}($model, $data, $id);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventDataDeleted($model, $data, $id));
    }

    /**
     * This function is used to call all methods contained in OnDataDeleting attribute
     * And called before data deleting
     * @param $model
     * @param $data
     * @param $id
     * @return void
     */
    private function callOnDataDeleting($model, $data, $id): void
    {
        $methods = OnDataDeletingReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 3) {
                    // Method expects 3 parameters: $model, $data, $id
                    $this->{$method}($model, $data, $id);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with all available parameters (backward compatibility)
                    $this->{$method}($model, $data, $id);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventDataDeleting($model, $data, $id));
    }

    /**
     * This function is used to call all methods contained in OnFormValidated attribute
     * And called after form validated
     * @param $model
     * @param $data
     * @param null $id
     * @return void
     */
    private function callOnFormValidated($model, $data, $id = null): void
    {
        $methods = OnFormValidatedReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 3) {
                    // Method expects 3 parameters: $model, $data, $id
                    $this->{$method}($model, $data, $id);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with all available parameters (backward compatibility)
                    $this->{$method}($model, $data, $id);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormValidated($model, $data, $id));
    }

    /**
     * This function is used to call all methods contained in OnBrowseColumnRendering attribute
     * And called after browse column rendering
     * @param $row
     * @param array $column
     * @return mixed
     */
    private function callOnBrowseColumnRendering($model, $row, array $column): mixed
    {
        $methods = OnBrowseColumnRenderingReader::getMethods($this);
        foreach ($methods as $method) {
            $row = $this->{$method}($model, $row, $column);
        }
        event(new EventBrowseColumnRendering($model, $row, $column));
        return $row;
    }

    /**
     * This method is used to call all methods contained in OnFormGetData attribute
     * And called after form data get
     * @return void
     */
    private function callOnFormGetData($model, $data, $id)
    {
        $methods = OnFormGetDataReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 3) {
                    // Method expects 3 parameters: $model, $data, $id
                    $this->{$method}($model, $data, $id);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with all available parameters (backward compatibility)
                    $this->{$method}($model, $data, $id);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormGetData($model, $data, $id));
    }

    /**
     * This function is used to call all methods contained in OnFormGettingData attribute
     * @param $model
     * @param $id
     * @return void
     */
    private function callOnFormGettingData($model, $id)
    {
        $methods = OnFormGettingDataReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 2) {
                    // Method expects 2 parameters: $model, $id
                    $this->{$method}($model, $id);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with all available parameters (backward compatibility)
                    $this->{$method}($model, $id);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormGettingData($model, $id));
    }

    private function callOnFormSaved($model, $data, $id = null): void
    {
        $methods = OnFormSavedReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 3) {
                    // Method expects 3 parameters: $model, $data, $id
                    $this->{$method}($model, $data, $id);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with all available parameters (backward compatibility)
                    $this->{$method}($model, $data, $id);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormSaved($model, $data, $id));
    }

    /**
     * This method is used to call all methods contained in OnFormSaving attribute
     * And called before form data save
     * @return void
     */
    private function callOnFormSaving($model, $data, $id = null)
    {
        $methods = OnFormSavingReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 3) {
                    // Method expects 3 parameters: $model, $data, $id
                    $this->{$method}($model, $data, $id);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with all available parameters (backward compatibility)
                    $this->{$method}($model, $data, $id);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormSaving($model, $data, $id));
    }

    /**
     * This function is used to call all methods contained in OnFormDehydrate attribute
     * And called after form data dehydrate
     * @return void
     */
    private function callOnFormDehydrate()
    {
        $methods = OnFormDehydrateReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                $this->{$method}();
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
    }

    /**
     * This function is used to call all methods contained in OnFormInit attribute
     * And called after form init
     * @return void
     */
    private function callOnFormInit($model)
    {
        $methods = OnFormInitReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 1) {
                    // Method expects 1 parameter: $model
                    $this->{$method}($model);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with available parameter
                    $this->{$method}($model);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormMounting($model));
    }

    /**
     * This function is used to call all methods contained in OnBrowseRendering attribute
     * And called after browse rendering
     * @return void
     */
    private function callOnBrowseRendering($model)
    {
        $methods = OnBrowseRenderingReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 1) {
                    // Method expects 1 parameter: $model
                    $this->{$method}($model);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with available parameter
                    $this->{$method}($model);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventBrowseRendering($model));
    }

    /**
     * This function is used to call all methods contained in OnFormMounted attribute
     * And called after form mounted
     * @param $model
     * @return void
     */
    private function callOnFormMounted($model): void
    {
        $methods = OnFormMountedReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 1) {
                    // Method expects 1 parameter: $model
                    $this->{$method}($model);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with available parameter
                    $this->{$method}($model);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormMounting($model));
    }

    /**
     * This function is used to call all methods contained in OnFormMounting attribute
     * And called before form mounted
     * @param $model
     * @return void
     */
    private function callOnFormMounting($model): void
    {
        $methods = OnFormMountingReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                // Check method parameter count using reflection
                $reflection = new \ReflectionMethod($this, $method);
                $parameterCount = $reflection->getNumberOfParameters();
                
                if ($parameterCount >= 1) {
                    // Method expects 1 parameter: $model
                    $this->{$method}($model);
                } elseif ($parameterCount == 0) {
                    // Method expects no parameters
                    $this->{$method}();
                } else {
                    // Default: call with available parameter
                    $this->{$method}($model);
                }
                
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
        event(new EventFormMounting($model));
    }

    /**
     * This function is used to call all methods contained in OnFormHydrate attribute
     * And called in form hydrate
     * @return void
     */
    private function callOnFormHydrate(): void
    {
        $methods = OnFormHydrateReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                $this->{$method}();
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
    }

    /**
     * This function is used to call all methods contained in OnPropertyUpdated attribute
     * And called after property updated
     * @return void
     */
    private function callOnPropertyUpdated(): void
    {
        $methods = OnPropertyUpdatedReader::getMethods($this);
        foreach ($methods as $method) {
            if(!isset(static::$__attributeCallerInstance[$method])) {
                $this->{$method}();
                static::$__attributeCallerInstance[$method] = $method;
            }
        }
    }

}
