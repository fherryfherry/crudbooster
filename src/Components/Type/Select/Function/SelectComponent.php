<?php

namespace CrudBooster\Components\Type\Select\Function;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class SelectComponent extends Component
{
    public $column;
    public $dataset;
    public $keyword;

    #[Modelable]
    public $selected;

    public $selectedLabel;
    public function mount()
    {
        $this->setDataSet();
        $this->findLabel();
    }

    protected function findLabel()
    {
        $dataset = $this->getTransformedDataset();
        $this->selectedLabel = collect($dataset)->where('key', $this->selected)->first()['label']??null;
    }

    protected function setDataSet()
    {
        $dataset = $this->getTransformedDataset();
        $this->dataset = collect($dataset)->take(10)->toArray();
    }

    protected function getTransformedDataset()
    {
        $dataset = $this->column['option']['dataset'] ?? [];
        
        if (isset($this->column['option']['transformLabel'])) {
            $transformCode = $this->column['option']['transformLabel'];
            if (is_string($transformCode) && !empty($transformCode)) {
                try {
                    $callback = eval("return function(\$label, \$key, \$row) { $transformCode };");
                    $dataset = array_map(function ($item) use ($callback) {
                        if (isset($item['options'])) {
                            // For grouped options
                            $item['options'] = array_map(function ($option) use ($callback) {
                                $option['label'] = $callback($option['label'], $option['key'], $option);
                                return $option;
                            }, $item['options']);
                        } else {
                            // For simple options
                            $item['label'] = $callback($item['label'], $item['key'], $item);
                        }
                        return $item;
                    }, $dataset);
                } catch (\Exception $e) {
                    // If there's an error in the transform code, use original dataset
                }
            } elseif (is_callable($transformCode)) {
                $callback = $transformCode;
                $dataset = array_map(function ($item) use ($callback) {
                    if (isset($item['options'])) {
                        // For grouped options
                        $item['options'] = array_map(function ($option) use ($callback) {
                            $option['label'] = $callback($option['label'], $option['key'], $option);
                            return $option;
                        }, $item['options']);
                    } else {
                        // For simple options
                        $item['label'] = $callback($item['label'], $item['key'], $item);
                    }
                    return $item;
                }, $dataset);
            }
        }
        
        return $dataset;
    }

    public function update()
    {
        // if selected changed, update the column
        if($this->selected) {
            $this->selectedLabel = $this->selected;
        }
    }

    public function resetItem()
    {
        $this->selected = null;
    }

    public function selectItem($key)
    {
        $this->selected = $key;
    }

    protected function findKeyword()
    {
        // 1. Check if we have dynamic model searching enabled
        if (isset($this->column['option']['model_definition']) && 
            isset($this->column['option']['searchable_query']) && 
            is_callable($this->column['option']['searchable_query'])) {
            
            $modelDef = $this->column['option']['model_definition'];
            $model = $modelDef['model'];
            $key = $modelDef['key'];
            $label = $modelDef['label'];
            $queryCallback = $modelDef['query_callback'];
            
            // Build the query
            $query = $model::query();
            
            // Apply base model callback
            if ($queryCallback) {
                 if (is_string($queryCallback)) {
                    $cb = eval("return function(\$query) { $queryCallback };");
                    $cb($query);
                 } elseif (is_callable($queryCallback)) {
                    $queryCallback($query);
                 }
            }
            
            // Apply searchable callback
            $searchableQuery = $this->column['option']['searchable_query'];
            // Check if searchableQuery expects a query builder or array (legacy)
            // But based on user request, we assume it's QueryBuilder if model_definition is present
            $searchableQuery($query, $this->keyword);
            
            // Get results
            $data = $query->take(20)->get();
            
            // Map data
            $this->dataset = $data->map(function($item) use ($key, $label) {
                $arr = $item->toArray();
                $base = [
                    'key' => $arr[$key] ?? null,
                    'label' => $arr[$label] ?? null,
                ];
                if (array_key_exists($key, $arr)) unset($arr[$key]);
                if (array_key_exists($label, $arr)) unset($arr[$label]);
                return array_merge($base, [
                    'additional' => $arr ?: [],
                ]);
            })->toArray();

            // Also apply label transformation if exists
            $this->dataset = $this->applyLabelTransformation($this->dataset);
            
            return;
        }

        $dataset = $this->getTransformedDataset();

        if(isset($this->column['option']['searchable_query']) && is_callable($this->column['option']['searchable_query'])) {
            // Legacy/Array support
            $this->dataset = $this->column['option']['searchable_query']($dataset, $this->keyword);
            return;
        }

        $this->dataset = collect($dataset)->filter(function ($value) {
            return str_contains(strtolower($value['label']), strtolower($this->keyword));
        })->toArray();
    }
    
    protected function applyLabelTransformation($dataset)
    {
        if (isset($this->column['option']['transformLabel'])) {
             $transformCode = $this->column['option']['transformLabel'];
             $callback = null;
             
             if (is_string($transformCode) && !empty($transformCode)) {
                 try {
                     $callback = eval("return function(\$label, \$key, \$row) { $transformCode };");
                 } catch (\Exception $e) {}
             } elseif (is_callable($transformCode)) {
                 $callback = $transformCode;
             }
             
             if ($callback) {
                 $dataset = array_map(function ($item) use ($callback) {
                    if (isset($item['options'])) {
                        $item['options'] = array_map(function ($option) use ($callback) {
                            $option['label'] = $callback($option['label'], $option['key'], $option);
                            return $option;
                        }, $item['options']);
                    } else {
                        $item['label'] = $callback($item['label'], $item['key'], $item);
                    }
                    return $item;
                 }, $dataset);
             }
        }
        return $dataset;
    }

    public function render()
    {
        if($this->keyword) {
            $this->findKeyword();
        } else {
            $this->setDataSet();
        }

        return view("cb-type-select::form_searchable");
    }
}
