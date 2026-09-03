<?php

namespace CrudBooster\Modules\ModuleBuilder\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Components\Type\CBTypeRegistrar;
use CrudBooster\Components\Type\TypeOptionAbstract;
use CrudBooster\Modules\ModuleBuilder\Models\CbModule;
use Illuminate\Support\Str;
use ReflectionClass;

class ModuleFormFormDesign extends ModuleForm
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form;
    public $uuid;
    public $menu = 'FORM_DESIGN';
    public $menuIconOnly = false;
    public $columns = [];

    public $input = [];
    public $currentIndexRow;
    public $currentIndexCol;

    public $changed = false;
    public $listOption = [];


    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->form = CbModule::where('uuid', $uuid)->first()?->config ?? [];
        $this->columns = array_filter($this->form['formDesignList'] ?? []);
    }

    public function listInputOption()
    {
        if(!isset($this->input['type'])) {
            return [];
        }
        if(!CBTypeRegistrar::__getTypes($this->input['type'], 'clazz')) {
            return [];
        }
        $typeClassGeneralOptionEnable = CBTypeRegistrar::__getTypes($this->input['type'], 'generalOption');
        $typeClassOption = new ReflectionClass(CBTypeRegistrar::__getTypes($this->input['type'], 'clazz'));
        $optionMethods = collect($typeClassOption->getMethods(\ReflectionMethod::IS_PUBLIC))->map(function ($method) {
            $docComment = $method->getDocComment();
            // Get param and map it
            preg_match_all('/@param\s+([^\s]+)\s+\$([^\s]+)\s+(.*)/', $docComment, $docParams, PREG_SET_ORDER);
            $docParamMap = [];
            foreach ($docParams as $docParam) {
                $docParamMap[$docParam[2]] = $docParam[3];
            }
            // Get placeholder and map it
            preg_match_all('/@placeholder\s+\$([^\s]+)\s+"([^"]+)"/', $docComment, $docPlaceholders, PREG_SET_ORDER);
            $docPlaceholderMap = [];
            foreach ($docPlaceholders as $docPlaceholder) {
                $docPlaceholderMap[$docPlaceholder[1]] = $docPlaceholder[2];
            }
            // Trim unused doc comment
            $descDocComment = preg_replace('/^\s*\*\s*@(?:param|return|placeholder).+$/m', '', $docComment);
            $descDocComment = str_replace(['/**', '*/','*'], '', $descDocComment);
            // Construct param array
            $paramArray = [];
            foreach ($method->getParameters() as $param) {
                $type = $param->getType();
                $paramDesc = $docParamMap[$param->name]??'';
                $typeName = $type instanceof \ReflectionUnionType ? implode('|', array_map(fn($t) => $t->getName(), $type->getTypes())) : $type?->getName();
                $placeholder = $typeName?: 'mixed';
                if (str_contains($type,'array') && !str_contains($paramDesc, 'App\Models')) {
                    $paramDescArr = str_replace("E.g: ", '', $paramDesc);
                    preg_match_all("/'(\w+)'\s*=>/", $paramDescArr, $matches);
                    $keys = array_unique($matches[1]);
                    if(count($keys) == 0) {
                        $placeholder = "key1|key2";
                        $paramDesc = "Array of string / integer. Each value separated by pipe (|)";
                    } else {
                        if (preg_match("/\[\[.*\]\]/", $paramDescArr)) {
                            $placeholder = "";
                            for($i=0;$i<=3;$i++) {
                                $placeholder .= implode("|",$keys)."\n";
                            }
                            $paramDesc = "Array of Object Key-Value Pair Array. Each key separated by pipe (|). And each data separated by new line.";
                        } else {
                            $placeholder = implode("|",$keys);
                            $paramDesc = "Object Key-Value Pair Array";
                        }
                    }
                }

                if(count($docPlaceholderMap)>0) {
                    if(isset($docPlaceholderMap[$param->name])) {
                        $placeholder = str_replace('\n', "\n", $docPlaceholderMap[$param->name]);
                    }
                }
                $paramArray[] = [
                    'name' => Str::studly($param->name),
                    'placeholder' => $placeholder,
                    'type' => $typeName,
                    'description' => $paramDesc,
                ];
            }

            return [
                'name'=>$method->name,
                'label'=>ucwords(str_replace('_', ' ', $method->name)),
                'description'=>$descDocComment,
                'paramCount'=>$method->getNumberOfParameters(),
                'paramList'=>$paramArray
            ];
        })->filter(fn($f) => !Str::startsWith($f['name'],'__') &&
            !in_array($f['name'],['getInputEvents','html','option'])
        )->toArray();
        if(!$typeClassGeneralOptionEnable) {
            $optionMethods = array_filter($optionMethods, fn($f) => !in_array($f['name'], $this->getGeneralOption()));
        }

        return $optionMethods;
    }

    private function getGeneralOption()
    {
        $typeAbstract = new ReflectionClass(TypeOptionAbstract::class);
        $optionMethods = collect($typeAbstract->getMethods())->map(function ($method) {
            return $method->name;
        })->filter(fn($f) => !Str::startsWith($f,'__') && !in_array($f,['getInputEvents','html','option']))->toArray();
        return $optionMethods;
    }

    public function updated() {
        if(isset($this->input['type'])) {
            $this->input['options'] = $this->input['options'] ?? [];
            $this->listOption = $this->listInputOption();
        }
    }

    public function setIndex($row, $col)
    {
        $this->currentIndexRow = (int)$row;
        $this->currentIndexCol = (int)$col;
    }

    public function addInput($row, $col)
    {
        $this->setIndex($row, $col);
        $this->input = [
            'showDetail'=> true,
            'showCreate'=> true,
            'showEdit'=> true,
        ];
    }

    public function editInput($row, $col)
    {
        $this->setIndex($row, $col);
        $this->input = $this->columns[$row][$col];
        $this->listOption = $this->listInputOption();
    }

    /**
     * To save form input include form configuration
     * @return void
     */
    public function saveInput()
    {
        if ($this->currentIndexRow === null || $this->currentIndexCol === null) {
            return;
        }

        if($this->input['validationRequired'] ?? false) {
            $this->input['validation'] = 'required';
        }

        $this->columns[$this->currentIndexRow][$this->currentIndexCol] = $this->input;
        $this->columns[$this->currentIndexRow] = array_values($this->columns[$this->currentIndexRow]);
        $this->changed = true;
    }

    public function removeInput(int $indexRow, int $indexCol)
    {
        $this->columns[$indexRow][$indexCol] = null;
        $this->changed = true;
    }

    public function addSideColumn(int $indexRow, int $indexCol)
    {
        // Validate max 3 columns
        if (count($this->columns[$indexRow]) >= 3) {
            return;
        }

        $targetIndex = $indexCol + 1;
        $this->columns[$indexRow] = array_merge(
            array_slice($this->columns[$indexRow], 0, $targetIndex),
            [null],
            array_slice($this->columns[$indexRow], $targetIndex)
        );
        $this->columns[$indexRow] = array_values($this->columns[$indexRow]);
        $this->changed = true;
    }

    public function removeColumn($indexRow, $indexCol)
    {
        unset($this->columns[$indexRow][$indexCol]);
        $this->changed = true;
    }

    public function addColumn($columnNumber = 1)
    {
        $this->columns = $this->columns ?? [];
        if ($columnNumber == 1) {
            $this->columns[] = [null];
        } else if ($columnNumber == 2) {
            $this->columns[] = [null, null];
        } else if ($columnNumber == 3) {
            $this->columns[] = [null, null, null];
        }
        $this->changed = true;
    }

    public function resetForm()
    {
        $ignoreFields = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
        $fields = [];
        $table = $this->form['table_name'] ?? $this->form['table'];
        foreach ($this->fieldGroup() as $fg) {
            if($fg['table'] == $table)
            foreach ($fg['fields'] as $field) {
                $fields[] = $field;
            }
        }

        $fields = array_diff($fields, $ignoreFields);

        $this->columns = [];
        foreach ($fields as $field) {
            $label = ucwords(str_replace(['.', '_'], ' ', $field));
            $this->columns[] = [
                [
                    'key' => $field,
                    'label' => $label,
                    'type'=> 'text',
                    'helpText'=> 'Please enter '.$label,
                    'showDetail'=> true,
                    'showCreate'=> true,
                    'showEdit'=> true,
                    'placeholder'=> '',
                ]
            ];
        }

        $this->changed = true;
    }

    /**
     * Reorder/move element from a slot to another slot (supports swap)
     */
    public function reorderElement($fromRow, $fromCol, $toRow, $toCol)
    {
        // Basic guards
        if ($fromRow === null || $fromCol === null) return;
        $fromRow = (int) $fromRow; $fromCol = (int) $fromCol;
        $toRow = (int) $toRow; $toCol = (int) $toCol;

        if (!isset($this->columns[$fromRow][$fromCol])) return;
        if (!isset($this->columns[$toRow][$toCol])) return;
        if ($fromRow === $toRow && $fromCol === $toCol) return;

        $fromVal = $this->columns[$fromRow][$fromCol] ?? null;
        $toVal = $this->columns[$toRow][$toCol] ?? null;

        // Move if target is empty, otherwise swap
        if ($toVal === null) {
            $this->columns[$toRow][$toCol] = $fromVal;
            $this->columns[$fromRow][$fromCol] = null;
        } else {
            $this->columns[$toRow][$toCol] = $fromVal;
            $this->columns[$fromRow][$fromCol] = $toVal;
        }

        // Normalize indices for both rows
        $this->columns[$toRow] = array_values($this->columns[$toRow]);
        $this->columns[$fromRow] = array_values($this->columns[$fromRow]);
        $this->changed = true;
    }

    public function formSave()
    {
        $this->validate([
            'columns' => 'required',
        ]);

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('module-builder'), navigate: true);
            return;
        }

        $this->form['formDesignList'] = $this->columns;
        CbModule::where('uuid', $this->uuid)->update(['config' => $this->form]);
        $this->changed = false;
        $this->showAlertMessage('Form design saved successfully');
        $this->redirect(getCmsUrl('module-builder/' . $this->uuid . '/form-hook'), navigate: true);
    }

    public function render()
    {
        return view("cb.module-builder::module_form_design")->layout("cb.themes::layout-app");
    }

}
