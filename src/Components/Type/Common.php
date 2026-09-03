<?php

use CrudBooster\Components\Type\CBTypeRegistrar;

if(!function_exists('createInputTag')) {
    /**
     * To create input tag
     * @param $column
     * @param $value
     * @param bool $focus
     * @return string
     */
    function createInputTag($column, $value, bool $focus = false): string
    {
        return view(CBTypeRegistrar::__getTypes($column['type'])['form'], ['column' => $column, 'value'=>$value, 'focus'=> $focus])->render();
    }
}
if(!function_exists('createViewTag')) {
    /**
     * To create view tag
     * @param $value
     * @param $column
     * @param $formData
     * @return string
     */
    function createViewTag($column, $value, $formData): string
    {
        return view(CBTypeRegistrar::__getTypes($column['type'])['view'], ['column' => $column, 'value'=> $value,'formData'=>$formData])->render();
    }
}