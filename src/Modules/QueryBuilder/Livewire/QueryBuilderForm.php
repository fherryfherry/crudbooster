<?php

namespace CrudBooster\Modules\QueryBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class QueryBuilderForm extends Component
{
    use WithAlertMessage;

    public $id;
    public $embedded = false;
    public $createdAt;
    public $modelList;
    public $modelName;
    public $name;
    public $builderMode;
    public $rawQuery;

    public $columns = [];
    public $conditions = [];
    public $relationships = [];
    public $orderByColumn = 'id';
    public $orderByDirection = 'asc';
    public $groupByColumns = [];
    public $havingConditions = [];
    public $selectColumns = [];
    public $queryType = 'where'; // 'where' or 'orWhere'
    public $conditionGroups = []; // Array to hold groups of conditions
    public $results;
    public $aggregationType = 'ARRAY'; // Default output type
    protected $__aggregationType = 'ARRAY';
    public $aggregationColumn; // Column to apply aggregation

    public function mount($id = null, $embedded = false)
    {
        $this->id = $id;
        $this->embedded = $embedded;
        $this->modelList = getModelList();
        $this->builderMode = "QUERY_BUILDER";
        if ($id) {
            $this->getData($id);
        }
    }

    private function getData($id)
    {
        $queryData = CbQueryBuilder::find($id);
        if ($queryData) {
            $this->id = $queryData->id;
            $this->createdAt = $queryData->created_at;
            $this->name = $queryData->name;
            $config = $queryData->config ?: [];
            $this->builderMode = $config['builderMode'] ?? 'QUERY_BUILDER';
            $this->rawQuery = $config['rawQuery'] ?? '';
            $this->modelName = $config['modelName'];
            $this->columns = $config['columns'];
            $this->conditions = $config['conditions'];
            $this->relationships = $config['relationships'];
            $this->orderByColumn = $config['orderByColumn'];
            $this->orderByDirection = $config['orderByDirection'];
            $this->groupByColumns = $config['groupByColumns'];
            $this->havingConditions = $config['havingConditions'];
            $this->selectColumns = $config['selectColumns'];
            $this->conditionGroups = $config['conditionGroups'];
            $this->aggregationType = $config['aggregationType'];
            $this->aggregationColumn = $config['aggregationColumn'];
        }
    }

    public function changeModel($model)
    {
        $this->modelName = $model;
        $model = new $model();
        $this->columns = Schema::getColumnListing($model->getTable());
        $this->selectColumns = $this->columns;
    }

    public function addCondition()
    {
        $this->conditions[] = ['field' => '', 'operator' => '', 'value' => '', 'type' => 'where'];
    }

    public function removeCondition($index)
    {
        unset($this->conditions[$index]);
        $this->conditions = array_values($this->conditions);
    }

    public function addConditionGroup()
    {
        $this->conditionGroups[] = ['conditions' => [], 'group_type' => 'AND'];
    }

    public function addConditionToGroup($groupIndex)
    {
        $this->conditionGroups[$groupIndex]['conditions'][] = [
            'field' => '',
            'operator' => '',
            'value' => '',
            'type' => 'where'
        ];
    }

    public function removeConditionGroup($index)
    {
        unset($this->conditionGroups[$index]);
        $this->conditionGroups = array_values($this->conditionGroups);
    }

    public function addRelationship()
    {
        $this->relationships[] = ['first_table' => '', 'first_field' => '', 'operator' => '', 'second_table' => '', 'second_field' => ''];
    }

    public function removeRelationship($index)
    {
        unset($this->relationships[$index]);
        $this->relationships = array_values($this->relationships);
    }

    public function addHavingCondition()
    {
        $this->havingConditions[] = ['field' => '', 'operator' => '', 'value' => ''];
    }

    public function removeHavingCondition($index)
    {
        unset($this->havingConditions[$index]);
        $this->havingConditions = array_values($this->havingConditions);
    }

    public function runQuery()
    {
        if(!$this->rawQuery && !$this->modelName) {
            return;
        }

        $this->results = CbQueryBuilderService::runQuery([
            'rawQuery' => $this->rawQuery,
            'modelName' => $this->modelName,
            'selectColumns' => $this->selectColumns,
            'conditionGroups' => $this->conditionGroups,
            'relationships' => $this->relationships,
            'orderByColumn' => $this->orderByColumn,
            'orderByDirection' => $this->orderByDirection,
            'groupByColumns' => $this->groupByColumns,
            'havingConditions' => $this->havingConditions,
            'aggregationType' => $this->aggregationType,
            'aggregationColumn' => $this->aggregationColumn
        ]);
    }

    public function formSave()
    {
        $this->validate([
            'name' => 'required',
            'builderMode' => 'required'
        ]);

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            if (!$this->embedded) {
                $this->redirectIntended(getCmsUrl('query-builder'), navigate: true);
            }
            return;
        }

        if ($this->builderMode == 'QUERY_RAW') {
            $this->validate([
                'rawQuery' => 'required'
            ]);
        } else {
            if ($this->aggregationType != 'ARRAY') {
                $this->validate([
                    'modelName' => 'required',
                    'aggregationColumn' => 'required'
                ]);
            } else {
                $this->validate([
                    'modelName' => 'required',
                    'selectColumns' => 'required'
                ]);
            }
        }

        $form = [
            'updated_at' => now(),
            'name' => $this->name,
            'config' => [
                'builderMode' => $this->builderMode,
                'rawQuery' => $this->rawQuery,
                'modelName' => $this->modelName,
                'columns' => $this->columns,
                'conditions' => $this->conditions,
                'relationships' => $this->relationships,
                'orderByColumn' => $this->orderByColumn,
                'orderByDirection' => $this->orderByDirection,
                'groupByColumns' => $this->groupByColumns,
                'havingConditions' => $this->havingConditions,
                'selectColumns' => $this->selectColumns,
                'conditionGroups' => $this->conditionGroups,
                'aggregationType' => $this->aggregationType,
                'aggregationColumn' => $this->aggregationColumn
            ]
        ];
        if ($this->id) {
            CbQueryBuilder::where('id', $this->id)->update($form);
        } else {
            $form['created_at'] = now();
            $this->id = CbQueryBuilder::create($form)->id;
        }

        $this->showAlertMessage('Query Builder saved successfully', 'success');

        if ($this->embedded) {
            $this->dispatch('query-saved', id: $this->id, name: $this->name);
            return;
        }

        $this->redirect(getCmsUrl('query-builder'), navigate: true);
    }

    public function render()
    {
        return view("cb.query-builder::form")->layout("cb.themes::layout-app");
    }
}
