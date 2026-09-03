<?php

namespace CrudBooster\Components\Type\SelectChips\Function;

use Closure;
use CrudBooster\Components\Type\TypeOptionAbstract;
use Illuminate\Database\Eloquent\Builder;

class SelectChips extends TypeOptionAbstract
{
    /**
     * If you want to automatically get the data from the model, you can use this function
     * @param string $pivotModel E.g: App\Models\RoleUser::class
     * @param string $firstPivotFk E.g: user_id
     * @param string $secondPivotFk E.g: role_id
     * @param string $displayModel E.g: App\Models\Role::class. Model to display the data
     * @param string $displayColumn E.g: "name". Column to display the data
     * @param Closure|null $displayQueryCallback E.g: function($builder) { $builder->where('status', 'active'); }
     * @return static
     */
    public function model(string $pivotModel, string $firstPivotFk, string $secondPivotFk, string $displayModel, string $displayColumn, Closure $displayQueryCallback = null): static
    {
        /** @var Builder $displayQuery */
        $displayQuery = $displayModel::query();
        $dataSelect = $displayQuery
            ->when($displayQueryCallback, fn(Builder $builder) => $displayQueryCallback($builder))
            ->get()
            ->map(function($item) use ($displayModel, $displayColumn) {
                $primaryKey = (new $displayModel())->getKeyName();
                return [
                    'key' => $item->{$primaryKey},
                    'label' => $item->{$displayColumn},
                ];
            })->toArray();
        $this->option = [
            'model'=> $pivotModel,
            'dataSelect'=> $dataSelect ?? [],
            'firstForeignKey' => $firstPivotFk,
            'secondForeignKey'=> $secondPivotFk,
            'displayModel' => $displayModel,
            'displayColumn' => $displayColumn,
        ];
        return $this;
    }

    /**
     * If you want to use your manual dataset, you can use this function
     * @param array $dataSet E.g: [['key' => 1, 'label' => 'Admin'], ['key' => 2, 'label' => 'User']]
     * @param string $pivotModel E.g: App\Models\UserRole::class
     * @param string $firstFk E.g: user_id
     * @param string $secondFk E.g: role_id
     * @param string $displayModel E.g: App\Models\Role::class. Model to display the data
     * @param string $displayColumn E.g: "name". Column to display the data
     * @return static
     */
    public function dataset(array $dataSet, string $pivotModel, string $firstFk, string $secondFk, string $displayModel, string $displayColumn): static
    {
        if(!is_string($dataSet) && is_callable($dataSet)) {
            $dataSet = $dataSet();
        }

        $this->option = [
            'dataSelect' => $dataSet,
            'model'=> $pivotModel,
            'firstForeignKey' => $firstFk,
            'secondForeignKey'=> $secondFk,
            'displayModel' => $displayModel,
            'displayColumn' => $displayColumn,
        ];
        return $this;
    }
}
