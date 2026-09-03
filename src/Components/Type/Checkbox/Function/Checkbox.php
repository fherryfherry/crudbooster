<?php

namespace CrudBooster\Components\Type\Checkbox\Function;

use Closure;
use CrudBooster\Components\Type\TypeOptionAbstract;
use Illuminate\Database\Eloquent\Builder;

class Checkbox extends TypeOptionAbstract
{
    protected function convertStringDatasetArray(string $dataset)
    {
        $dataset = explode("\n", $dataset);
        $dataset = array_map(function ($value) {
            $value = explode('|', $value);
            if(count($value) == 1) {
                $key = $value[0];
                $label = $value[0];
            } else {
                $key = $value[0];
                $label = $value[1];
            }
            return [
                'key' => $key,
                'label' => $label,
            ];
        }, $dataset);
        return $dataset;
    }

    /**
     * To set the dataset for the select
     * @param array|string $dataSet E.g: [['key'=>'key1','label'=>'Label 1'], ['key'=>'key2','label'=>'Label 2']]
     * @return Checkbox
     */
    public function dataset(array|string $dataSet)
    {
        // Convert string to array
        if (is_string($dataSet)) {
            $dataSet = $this->convertStringDatasetArray($dataSet);
        }

        // Validation dataSet should be contained key and label
        foreach ($dataSet as $value) {
            if (!isset($value['label']) && !isset($value['key'])) {
                throw new \InvalidArgumentException('Dataset should be contain key and label');
            }
        }

        $this->option = array_merge($this->option, [
            'dataset' => $dataSet,
        ]);
        return $this;
    }

    /**
     * To make dataset from model
     * @param string|array $modelName E.g: App\Models\User::class
     * @param string|null $key E.g: id
     * @param string|null $label E.g: name
     * @param \Closure|null $queryCallback E.g: function($query, $id = null) { $query->where('status', 1); }
     * @return static
     */
    public function model(string|array $modelName, ?string $key = null, ?string $label = null, ?Closure $queryCallback = null)
    {
        if(!is_array($modelName) && str_contains($modelName, '|')) {
            /*
             * Format will be
             * App\Models\User::class|id|name
             */
            $modelName = explode('|', $modelName);
            $key = $modelName[1];
            $label = $modelName[2];
            $modelName = $modelName[0];
        }
        if(is_array($modelName)) {
            $key = $modelName['key'];
            $label = $modelName['label'];
            $modelName = $modelName['modelName'];
            $queryCallback = $modelName['queryCallback'] ?? null;
        }

        $data = $modelName::query()
            ->when($queryCallback, function(Builder $builder) use ($queryCallback) {
                // Call callback with query only
                $queryCallback($builder);
            })
            ->get()
            ->map(function($item) use ($key, $label) {
                $arr = $item->toArray();
                $base = [
                    'key' => $arr[$key] ?? null,
                    'label' => $arr[$label] ?? null,
                ];
                // Remove key/label only if exists
                if (array_key_exists($key, $arr)) unset($arr[$key]);
                if (array_key_exists($label, $arr)) unset($arr[$label]);
                return array_merge($base, [
                    'additional' => $arr ?: [],
                ]);
            })->toArray();

        $this->option = array_merge($this->option, [
            'dataset' => $data,
        ]);

        return $this;
    }

    /**
     * To transform label using closure or string
     * @param Closure|string $callback E.g: function($label, $key, $row) { return strtoupper($label); } or string code
     * @return static
     */
    public function transformLabel(Closure|string $callback)
    {
        if ($callback instanceof \Closure) {
            // Process dataset directly here if already exists
            if (isset($this->option['dataset'])) {
                $this->option['dataset'] = $this->applyLabelTransformation($this->option['dataset'], $callback);
            }
            // Save closure only for chaining, but remove before going to Blade
            $this->option['transformLabel'] = null;
        } else {
            // String code sent to Blade for eval
            $this->option['transformLabel'] = $callback;
        }
        return $this;
    }

    /**
     * Apply label transformation to dataset
     * @param array $dataset
     * @param Closure $callback
     * @return array
     */
    protected function applyLabelTransformation(array $dataset, ?Closure $callback = null)
    {
        if (!$callback && !isset($this->option['transformLabel'])) {
            return $dataset;
        }
        $cb = $callback ?? $this->option['transformLabel'];
        return array_map(function ($item) use ($cb) {
            if (isset($item['additional'])) {
                // For model data with additional fields
                $row = array_merge([
                    'key' => $item['key'],
                    'label' => $item['label'],
                ], is_array($item['additional']) ? $item['additional'] : []);
            } else {
                // For simple dataset
                $row = [
                    'key' => $item['key'],
                    'label' => $item['label'],
                ];
            }
            $item['label'] = $cb($item['label'], $item['key'], (object)$row);
            return $item;
        }, $dataset);
    }

    /**
     * Get transformed dataset
     * @return array
     */
    public function getTransformedDataset()
    {
        $dataset = $this->option['dataset'] ?? [];
        return $this->applyLabelTransformation($dataset);
    }

    /**
     * Process transform label from string setting
     * @param string $transformCode
     * @return Closure|null
     */
    public static function processTransformLabel($transformCode)
    {
        if (empty($transformCode)) {
            return null;
        }

        try {
            // Create a closure from the string code (same approach as select)
            $closure = eval("return function(\$label, \$key, \$row) { $transformCode };");
            return $closure;
        } catch (\Exception $e) {
            // If there's an error in the code, return null
            return null;
        }
    }

    /**
     * Apply transform label from setting
     * @param string $transformCode
     * @return static
     */
    public function applyTransformLabel($transformCode)
    {
        $closure = self::processTransformLabel($transformCode);
        if ($closure) {
            $this->transformLabel($closure);
        }
        return $this;
    }
}
