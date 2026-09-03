<?php

namespace CrudBooster\Components\Type\Radio\Function;

use Closure;
use CrudBooster\Components\Type\TypeOptionAbstract;
use Illuminate\Database\Eloquent\Builder;

class Radio extends TypeOptionAbstract
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
     * @return Radio
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
     * @return Radio
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
     * @return Radio
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
     * @return Radio
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
            ->addSelect("{$key} as key")
            ->addSelect("{$label} as label")
            ->get()->toArray();

        $this->option = array_merge($this->option, [
            'dataset' => $data,
        ]);

        return $this;
    }
}
