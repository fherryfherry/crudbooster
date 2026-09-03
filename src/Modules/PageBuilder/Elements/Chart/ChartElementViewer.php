<?php

namespace CrudBooster\Modules\PageBuilder\Elements\Chart;

use CrudBooster\Modules\QueryBuilder\Models\CbQueryBuilder;
use CrudBooster\Modules\QueryBuilder\Services\CbQueryBuilderService;
use Livewire\Component;

class ChartElementViewer extends Component
{
    public $config;
    public $id;
    public $labels = [];
    public $datasets = [];
    public function mount($id, $config)
    {
        $this->config = $config;
        $this->id = $id;
        $this->generateLabels();
        $min = $this->config['minXAxis'];
        $max = $this->config['maxXAxis'];
        foreach ($this->config['datasets'] as $dataset) {
            $query = CbQueryBuilder::where('id', $dataset['query'])->first();
            $queryResult = CbQueryBuilderService::runQuery($query->config);
            $data = [];

            if ($this->config['dataType'] == 'YEARLY') {
                for ($i = $min; $i <= $max; $i++) {
                    $data[] = $queryResult->where($dataset['comparatorField'] ?? '', $i)->first()->{$dataset['pointField']} ?? 0;
                }
            } elseif ($this->config['dataType'] == 'MONTHLY') {
                for ($i = $min; $i <= $max; $i++) {
                    $data[] = $queryResult->where($dataset['comparatorField'] ?? '', $i)
                        ->where($dataset['yearField'] ?? '', $this->config['yearXAxis'])
                        ->first()->{$dataset['pointField']} ?? 0;
                }
            } elseif ($this->config['dataType'] == 'DAILY') {
                $min = strtotime($min);
                $max = strtotime($max);
                for ($i = $min; $i <= $max; $i = strtotime('+1 day', $i)) {
                    $value = $queryResult->where($dataset['comparatorField'] ?? '', date('Y-m-d', $i))->first();
                    $data[] = $value->{$dataset['pointField']} ?? 0;
                }
            } elseif ($this->config['dataType'] == 'SEMIANNUALY') {
                for ($i = $min; $i <= $max; $i++) {
                    $data[] = $queryResult->where($dataset['comparatorField'] ?? '', 'Semester ' . $i)
                        ->where($dataset['yearField'] ?? '', $this->config['yearXAxis'])
                        ->first()->{$dataset['pointField']} ?? 0;
                }
            } elseif ($this->config['dataType'] == 'QUARTERLY') {
                for ($i = $min; $i <= $max; $i++) {
                    $data[] = $queryResult->where($dataset['comparatorField'] ?? '', 'Quarter ' . $i)
                        ->where($dataset['yearField'] ?? '', $this->config['yearXAxis'])
                        ->first()->{$dataset['pointField']} ?? 0;
                }
            } elseif ($this->config['dataType'] == 'BYDAY') {
                $min = strtotime($min);
                $max = strtotime($max);
                for ($i = $min; $i <= $max; $i = strtotime('+1 day', $i)) {
                    $data[] = $queryResult->where($dataset['comparatorField'] ?? '', date('l', $i))
                        ->where($dataset['yearField'] ?? '', $this->config['yearXAxis'])
                        ->first()->{$dataset['pointField']} ?? 0;
                }
            } elseif ($this->config['dataType'] == 'HOURLY') {
                for ($i = $min; $i <= $max; $i++) {
                    $data[] = $queryResult->where($dataset['comparatorField'] ?? '', $i . ':00')
                        ->where($dataset['yearField'] ?? '', $this->config['yearXAxis'])
                        ->first()->{$dataset['pointField']} ?? 0;
                }
            }

            $this->datasets[] = [
                "label" => $dataset['label'],
                "data" => $data,
                "backgroundColor" => $dataset['backgroundColor'],
                "borderColor" => $dataset['borderColor']
            ];
        }

    }

    public function generateLabels()
    {
        if ($this->config['dataType'] == 'YEARLY') {
            $min = $this->config['minXAxis'];
            $max = $this->config['maxXAxis'];
            for ($i = $min; $i <= $max; $i++) {
                $this->labels[] = $i;
            }
        } elseif ($this->config['dataType'] == 'MONTHLY') {
            $min = $this->config['minXAxis'];
            $max = $this->config['maxXAxis'];
            for ($i = $min; $i <= $max; $i++) {
                $this->labels[] = date('F', mktime(0, 0, 0, $i, 10));
            }
        } elseif ($this->config['dataType'] == 'DAILY') {
            $min = $this->config['minXAxis']; // E.g: 2025-01-01
            $max = $this->config['maxXAxis']; // E.g: 2025-01-31
            $min = strtotime($min);
            $max = strtotime($max);
            for ($i = $min; $i <= $max; $i = strtotime('+1 day', $i)) {
                $this->labels[] = date('Y-m-d', $i);
            }
        } elseif ($this->config['dataType'] == 'SEMIANNUALY') {
            $min = $this->config['minXAxis'];
            $max = $this->config['maxXAxis'];
            for ($i = $min; $i <= $max; $i++) {
                $this->labels[] = 'Semester ' . $i;
            }
        } elseif ($this->config['dataType'] == 'QUARTERLY') {
            $min = $this->config['minXAxis'];
            $max = $this->config['maxXAxis'];
            for ($i = $min; $i <= $max; $i++) {
                $this->labels[] = 'Quarter ' . $i;
            }
        } elseif ($this->config['dataType'] == 'BYDAY') {
            $min = $this->config['minXAxis']; // E.g: 1 = Monday, 2 = Tuesday, etc
            $max = $this->config['maxXAxis']; // E.g: 7 = Sunday
            for ($i = $min; $i <= $max; $i++) {
                $this->labels[] = date('l', strtotime('Sunday + ' . $i . ' days')); // E.g: Monday
            }
        } elseif ($this->config['dataType'] == 'HOURLY') {
            $min = $this->config['minXAxis'];
            $max = $this->config['maxXAxis']; // max = 23
            for ($i = $min; $i <= $max; $i++) {
                $this->labels[] = $i . ':00';
            }
        } elseif ($this->config['dataType'] == 'WEEKLY') {
            $min = $this->config['minXAxis']; // E.g: 1 = Week 1, 2 = Week 2, etc
            $max = $this->config['maxXAxis']; // E.g: 52 = Week 52
            for ($i = $min; $i <= $max; $i++) {
                $this->labels[] = 'Week ' . $i;
            }
        }
    }
    public function render()
    {
        return view('cb.element::' . basename(__DIR__) . '.views.view');
    }
}
