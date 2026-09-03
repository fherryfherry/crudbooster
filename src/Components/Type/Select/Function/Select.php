<?php

namespace CrudBooster\Components\Type\Select\Function;

use Closure;
use CrudBooster\Components\Type\TypeOptionAbstract;
use Illuminate\Support\Str;

/**
 * Select Component with Label Transformation Support
 * 
 * Example usage:
 * 
 * // Basic transformation
 * Select::option()
 *     ->dataset([['key' => '1', 'label' => 'john'], ['key' => '2', 'label' => 'jane']])
 *     ->transformLabel(function($label, $key, $row) {
 *         return strtoupper($label);
 *     });
 * 
 * // With model and transformation
 * Select::option()
 *     ->model('App\Models\User', 'id', 'name')
 *     ->transformLabel(function($label, $key, $row) {
 *         return ucfirst($label) . ' (ID: ' . $key . ')';
 *     });
 * 
 * // Complex transformation
 * Select::option()
 *     ->dataset([['key' => 'active', 'label' => 'active'], ['key' => 'inactive', 'label' => 'inactive']])
 *     ->transformLabel(function($label, $key, $row) {
 *         $status = $label === 'active' ? '🟢' : '🔴';
 *         return $status . ' ' . ucfirst($label);
 *     });
 */
class Select extends TypeOptionAbstract
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
     * To make the select searchable
     * @return $this
     */
    public function searchable(?Closure $query = null)
    {
        $this->option['searchable'] = true;
        if($query) {
            $this->option['searchable_query'] = $query;
        }
        return $this;
    }

    /**
     * To set the dataset for the select
     */
    public function availableNot()
    {
        $this->option = array_merge($this->option, [
            'dataset' => [
                ['key' => 'available', 'label' => 'Available'],
                ['key' => 'not_available', 'label' => 'Not Available'],
            ],
        ]);
        return $this;
    }

    /**
     * To set the dataset for the select
     * @param bool $isString If true, convert key and label to string
     */
    public function activeInactive(bool $isString = false)
    {
        // If isString is true, convert key and label to string
        if ($isString) {
            $this->option = array_merge($this->option, [
                'dataset' => [
                    ['key' => 'active', 'label' => 'Active'],
                    ['key' => 'inactive', 'label' => 'Inactive'],
                ],
            ]);
            return $this;
        }
        $this->option = array_merge($this->option, [
            'dataset' => [
                ['key' => '1', 'label' => 'Active'],
                ['key' => '0', 'label' => 'Inactive'],
            ],
        ]);

        return $this;
    }

    /**
     * To set the dataset for the select
     * @param bool $isString If true, convert key and label to string
     */
    public function yesNo(bool $isString = false)
    {
        // If isString is true, convert key and label to string
        if ($isString) {
            $this->option = array_merge($this->option, [
                'dataset' => [
                    ['key' => 'yes', 'label' => 'Yes'],
                    ['key' => 'no', 'label' => 'No'],
                ],
            ]);
            return $this;
        }

        $this->option = array_merge($this->option, [
            'dataset' => [
                ['key' => '1', 'label' => 'Yes'],
                ['key' => '0', 'label' => 'No'],
            ],
        ]);
        return $this;
    }

    /**
     * To set the dataset for the select
     * @param array|string $dataSet E.g: [['key'=>'key1','label'=>'Label 1'], ['key'=>'key2','label'=>'Label 2']]
     * @return Select
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
     * To set the dataset group for the select
     * @placeholder $dataSet "group1\n- key1|label1\n- key2|label2\n- key3|label3\ngroup2\n- key1|label1\n- key2|label2\n- key3|label3\n\n"
     * @param array|string $dataSet E.g: [['label' => 'Group 1', 'options' => [['label' => 'Label 1', 'key' => 'key1'], ['label' => 'Label 2', 'key' => 'key2']]]
     * @return $this
     */
    public function datasetGroup(array|string $dataSet)
    {
        if(is_string($dataSet)) {
            /*
             * Format will be
             * group1
             * - key1|label1
             * - key2|label2
             */
            $dataSet = explode("\n", $dataSet);
            $group = [];
            $groupIndex = 0;
            foreach ($dataSet as $value) {
                if (empty($value)) {
                    continue;
                }
                if (Str::startsWith($value, '-')) {
                    $value = ltrim($value, '- ');
                    $value = explode('|', $value);
                    $group[$groupIndex]['options'][] = [
                        'key' => $value[0],
                        'label' => $value[1],
                    ];
                } else {
                    $groupIndex++;
                    $group[$groupIndex]['label'] = $value;
                    $group[$groupIndex]['options'] = [];
                }
            }
            $dataSet = $group;
        }

        // Validation dataSet should be contained key and label
        foreach ($dataSet as $value) {
            if (!isset($value['options']) && !isset($value['label'])) {
                throw new \InvalidArgumentException('Dataset should be contain options and label');
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
     * @param Closure|string|null $queryCallback E.g: function($query, $id = null) { $query->where('name', 'like', '%a%'); }
     * @return static
     */
    public function model(string|array $modelName, ?string $key = null, ?string $label = null, Closure|string|null $queryCallback = null)
    {
        $model = $modelName;

        if(!is_array($modelName) && str_contains($modelName, '|')) {
            $modelName = explode('|', $modelName);
            $key = $modelName[1];
            $label = $modelName[2];
            $model = $modelName[0];
        }
        if(is_array($modelName) && isset($modelName['key'])) {
            try {
                $key = $modelName['key'];
                $label = $modelName['label'];
                $model = $modelName['modelName'];
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Model name should be contained key, label, and modelName');
            }
            $queryCallback = $modelName['queryCallback'] ?? null;
        }

        $this->option['model_definition'] = [
            'model' => $model,
            'key' => $key,
            'label' => $label,
            'query_callback' => $queryCallback
        ];

        $data = $model::query()
            ->when($queryCallback, function($query) use ($queryCallback) {
                // Call callback with query only
                if (is_string($queryCallback)) {
                    // For string callbacks (from setting form), evaluate
                    $callback = eval("return function(\$query) { $queryCallback };");
                    $callback($query);
                } else {
                    // For closure callbacks
                    $queryCallback($query);
                }
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
            // Proses dataset langsung di sini jika sudah ada
            if (isset($this->option['dataset'])) {
                $this->option['dataset'] = $this->applyLabelTransformation($this->option['dataset'], $callback);
            }
            // Simpan closure hanya untuk chaining, tapi hapus sebelum ke Blade
            $this->option['transformLabel'] = null;
        } else {
            // String code dikirim ke Blade untuk eval
            $this->option['transformLabel'] = $callback;
        }
        return $this;
    }

    /**
     * Apply label transformation to dataset (static for patch)
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
            if (isset($item['options'])) {
                $item['options'] = array_map(function ($option) use ($cb) {
                    $row = isset($option['additional']) ? array_merge([
                        'key' => $option['key'],
                        'label' => $option['label'],
                    ], is_array($option['additional']) ? $option['additional'] : []) : [
                        'key' => $option['key'],
                        'label' => $option['label'],
                    ];
                    $option['label'] = $cb($option['label'], $option['key'], (object)$row);
                    return $option;
                }, $item['options']);
            } else {
                $row = isset($item['additional']) ? array_merge([
                    'key' => $item['key'],
                    'label' => $item['label'],
                ], is_array($item['additional']) ? $item['additional'] : []) : [
                    'key' => $item['key'],
                    'label' => $item['label'],
                ];
                $item['label'] = $cb($item['label'], $item['key'], (object)$row);
            }
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
            // Create a closure from the string code
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
