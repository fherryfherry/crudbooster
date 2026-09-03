<?php

namespace CrudBooster\Components\Type\JsonTable\Function;

use Closure;
use CrudBooster\Components\Type\TypeOptionAbstract;

class JsonTable extends TypeOptionAbstract
{
    protected array $closureAttributes = ['placeholder','readonly','helpText'];
    /**
     * To set the dataset for the json table
     * @param array $items E.g: ['Item1','Item2','Item3']
     * @param string $itemLabel E.g: 'Item Label'
     * @param array $inputs E.g: [['name' => 'Input Name','type'=>'checkbox|text|number','placeholder'=>'Input Placeholder']]
     * @return JsonTable
     */
    public function dataset(array $items, string $itemLabel, array $inputs = [])
    {
        foreach ($items as $i=>$item) {
            $itemInputs = $inputs;
            foreach ($itemInputs as $e=>$input) {
                foreach ($this->closureAttributes as $closureAttribute) {
                    if(isset($input[$closureAttribute]) && $input[$closureAttribute] instanceof Closure) {
                        $input[$closureAttribute] = $input[$closureAttribute]($item);
                    }
                }
                $itemInputs[$e] = $input;
            }
            $items[$i]['inputs'] = $itemInputs;
        }

        // Remove closure
        $inputs = array_map(function($input) {
            foreach ($this->closureAttributes as $closureAttribute) {
                if(isset($input[$closureAttribute]) && $input[$closureAttribute] instanceof Closure) {
                    unset($input[$closureAttribute]);
                }
            }
            return $input;
        }, $inputs);

        $this->option = array_merge($this->option,[
            'data' => $items,
            'item_label'=> $itemLabel,
            'inputs'=> $inputs
        ]);
        return $this;
    }
}
