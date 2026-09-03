<?php

namespace CrudBooster\Modules\PageBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\Icon\Icon;
use CrudBooster\Modules\PageBuilder\Models\CbPage;
use CrudBooster\Modules\PageBuilder\Services\CbPageService;
use Livewire\Attributes\On;
use Livewire\Component;

class PageBuilderStudio extends Component
{
    use WithAlertMessage;

    public $tools = [];
    public $data = [];
    public $name;
    public $path;
    public $grid = [];
    public $id;
    public $configElementComponent;
    public $configElementTitle;
    public $rowIndex;
    public $colIndex;

    public function mount($id = null)
    {
        $this->id = $id;
        $this->tools = $this->getToolList(true);
        if ($id) {
            $this->loadData($id);
        }
    }
    #[On('elementSaved')]
    public function onElementSaved()
    {
        $this->loadData($this->id);
    }

    public function loadData($id)
    {
        $this->data = CbPage::where('id', $id)->first()?->toArray();
        $this->grid = $this->data['config'] ?: [];
        $this->tools = $this->isContainLayout() ? $this->getToolList() : $this->getToolList(true);
        $this->name = $this->data['name'] ?? '';
        $this->path = $this->data['path'] ?? '';
    }

    private function isContainLayout()
    {
        if(!is_array($this->grid)) {
            return false;
        }
        foreach ($this->grid as $row) {
            foreach ($row as $col) {
                if (str_contains($col['type'], 'grid')) {
                    return true;
                }
            }
        }
        return false;
    }

    public function editElement($rowIndex, $colIndex)
    {
        $this->rowIndex = $rowIndex;
        $this->colIndex = $colIndex;
        $this->configElementTitle = $this->grid[$rowIndex][$colIndex]['content']['placeholder'] ?? 'Unknown';
        $this->configElementComponent = $this->grid[$rowIndex][$colIndex]['content']['type'];
    }

    #[On('saved')]
    public function clearEditElement()
    {
        $this->rowIndex = null;
        $this->colIndex = null;
        $this->configElementTitle = null;
        $this->configElementComponent = null;
    }

    public function updated()
    {
        if ($this->data && $this->data['name'] != "") {
            $this->save();
        }
    }

    public function sortColumn($rowIndex, $colIndex, $fromRowIndex, $fromColIndex)
    {
        if ($rowIndex === "" || $colIndex === "" || $fromRowIndex === "" || $fromColIndex === "") {
            return;
        }
        if ($rowIndex === null || $colIndex === null || $fromRowIndex === null || $fromColIndex === null) {
            return;
        }
        $from = $this->grid[$fromRowIndex][$fromColIndex];
        $to = $this->grid[$rowIndex][$colIndex];
        $this->grid[$rowIndex][$colIndex] = $from;
        $this->grid[$fromRowIndex][$fromColIndex] = $to;
        $this->save();
    }

    public function save()
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('page-builder'), navigate: true);
            return;
        }

        $this->data['config'] = $this->grid;
        $this->data = CbPageService::saveOrUpdate($this->data);

        $id = $this->data['id'];

        if ($id) {
            $this->showAlertMessage('Page updated successfully', 'success');
            $this->redirect(getCmsUrl('page-builder/' . $id . '/studio'), navigate: true);
        } else {
            $this->showAlertMessage('Page failed to update, check log!', 'warning');
            $this->redirect(getCmsUrl('page-builder'), navigate: true);
        }
    }
    public function addRightColumn($rowIndex, $colIndex)
    {
        $this->grid[$rowIndex] = array_merge(
            array_slice($this->grid[$rowIndex], 0, $colIndex + 1),
            [[
                'type' => 'grid-1',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ]],
            array_slice($this->grid[$rowIndex], $colIndex + 1)
        );
        $this->save();
    }
    public function deleteColumn($rowIndex, $colIndex)
    {
        unset($this->grid[$rowIndex][$colIndex]);
        $this->grid[$rowIndex] = array_values($this->grid[$rowIndex]);
        // Remove grid that has no array children
        $this->grid = array_values(array_filter($this->grid));
        $this->save();
    }

    public function setContent($rowIndex, $colIndex, $type, $typeLabel)
    {
        // Prevent layout column (grid-*) inside another layout column
        if (str_starts_with($type, 'grid')) {
            $parentType = $this->grid[$rowIndex][$colIndex]['type'] ?? null;
            if ($parentType && str_starts_with($parentType, 'grid')) {
                $this->showAlertMessage('You cannot put a layout column inside another layout column!', 'danger');
                return;
            }
        }
        $this->grid[$rowIndex][$colIndex]['content'] = [
            'type' => $type,
            'placeholder' => $typeLabel,
            'config' => $this->grid[$rowIndex][$colIndex]['content']['config'] ?? []
        ];
        $this->save();
    }

    public function addColumn($type = 'grid-1')
    {
        $column = [];
        if ($type == 'grid-2') {
            $column[] = [
                'type' => 'grid-2',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
            $column[] = [
                'type' => 'grid-2',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
        } elseif ($type == 'grid-3') {
            $column[] = [
                'type' => 'grid-3',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
            $column[] = [
                'type' => 'grid-3',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
            $column[] = [
                'type' => 'grid-3',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
        } elseif ($type == 'grid-4') {
            $column[] = [
                'type' => 'grid-4',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
            $column[] = [
                'type' => 'grid-4',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
            $column[] = [
                'type' => 'grid-4',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
            $column[] = [
                'type' => 'grid-4',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
        } else {
            $column[] = [
                'type' => 'grid-1',
                'content' => [
                    'placeholder' => 'Drop Element Here'
                ]
            ];
        }

        $this->grid[] = $column;
        $this->tools = $this->getToolList(false);
        $this->save();
    }

    private function getToolList($onlyGrid = false)
    {
        return [
            [
                'group' => 'Layout',
                'tools' => [
                    ['name' => '1 Column', 'icon' => Icon::GRID, 'type' => 'grid-1', 'is_active' => true],
                    ['name' => '2 Columns', 'icon' => Icon::GRID, 'type' => 'grid-2', 'is_active' => true],
                    ['name' => '3 Columns', 'icon' => Icon::GRID, 'type' => 'grid-3', 'is_active' => true],
                    ['name' => '4 Columns', 'icon' => Icon::GRID, 'type' => 'grid-4', 'is_active' => true],
                ]
            ],
            [
                'group' => 'Text',
                'tools' => [
                    ['name' => 'Heading', 'icon' => Icon::TEXT, 'type' => 'heading', 'is_active' => !$onlyGrid],
                    ['name' => 'Paragraph', 'icon' => Icon::TEXT, 'type' => 'paragraph', 'is_active' => !$onlyGrid]
                ]
            ],
            [
                'group' => 'Image',
                'tools' => [
                    ['name' => 'Image', 'icon' => Icon::IMAGE, 'type' => 'image', 'is_active' => !$onlyGrid]
                ]
            ],
            [
                'group' => 'Data',
                'tools' => [
                    ['name' => 'Table', 'icon' => Icon::TABLE, 'type' => 'table', 'is_active' => !$onlyGrid],
                    ['name' => 'Box Counter', 'icon' => Icon::CHART, 'type' => 'box-counter', 'is_active' => !$onlyGrid],
                ]
            ],
            [
                'group' => 'Chart',
                'tools' => [
                    ['name' => 'ChartJS', 'icon' => Icon::CHART, 'type' => 'chart', 'is_active' => !$onlyGrid],
                ]
            ],
            [
                'group' => 'Map',
                'tools' => [
                    ['name' => 'Google Map', 'icon' => Icon::MAP, 'type' => 'google-map', 'is_active' => !$onlyGrid],
                ]
            ]
        ];
    }

    public function render()
    {
        return view("cb.page-builder::page_studio")->layout("cb.themes::layout-app");
    }

    public function preview()
    {
        $this->redirect(getCmsUrl('p/' . $this->id), navigate: true);
    }

    public function createPageWithTitle($title)
    {
        $data = [
            'name' => $title,
            'path' => \Str::slug($title),
            'config' => [],
        ];
        $page = \CrudBooster\Modules\PageBuilder\Models\CbPage::create($data);
        $this->dispatch('pageCreated', id: $page->id);
    }
}
