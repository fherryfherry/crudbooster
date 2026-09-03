<?php

namespace CrudBooster\Livewire\FormBuilder;

use Closure;
use CrudBooster\Components\Type\TypeOptionAbstract;

class Form implements FormContract
{
    private array $columns;

    public function __construct($column)
    {
        $this->columns = $column;
    }

    public function get()
    {
        return $this->columns;
    }

    private static function constructRules($key, $validation = null): array
    {
        $validationMessage = [];
        $validationRules = [];
        if ($validation && is_array($validation)) {
            $rules = [];
            foreach ($validation as $rule => $message) {
                $rules[] = $rule;
                $rule = explode(':', $rule)[0];
                $validationMessage['formData.' . $key . '.' . $rule] = $message;
            }
            $validationRules['formData.' . $key] = implode('|', $rules);
        }
        if ($validation && !is_array($validation)) {
            $validationRules['formData.' . $key] = $validation;
        }
        return [$validationRules, $validationMessage];
    }

    public static function empty()
    {
        return new static([
            'visible' => true, 
            'key' => 'empty',
            'type' => 'empty',
            'label' => '',
            'validation' => [],
            'validationMessage' => [],
            'placeholder' => '',
            'helpText' => '',
            'readonly' => false,
            'showDetail' => false,
            'showEdit' => false,
            'showCreate' => false,
            'bindValue' => null,
            'option' => [],
            'onChange' => null,
            'default' => null,
        ]);
    }

    public static function add($label,
                               $key,
                               $type = 'text',
                               $validation = null,
                               $placeholder = null,
                               $helpText = null,
                               $readonly = false,
                               $bindValue = null,
                               $option = []): Form
    {
        [$validationRules, $validationMessage] = self::constructRules($key, $validation);
        $option = $option instanceof TypeOptionAbstract ? $option->__getOption() : $option;
        $column = [
            'label' => $label,
            'key' => $key,
            'type' => $type,
            'validation' => $validationRules,
            'validationMessage' => $validationMessage,
            'placeholder' => $placeholder,
            'helpText' => $helpText,
            'readonly' => $readonly,
            'showDetail'=> !in_array($key, config('cb.hide_field_on_detail')),
            'showEdit'=> true,
            'showCreate'=> true,
            'visible' => true,
            'bindValue' => $bindValue,
            'option' => $option,
            'onChange'=> null,
            'default' => null,
        ];

        return new static($column);
    }

    /**
     * To set the column as required
     * @return Form
     */
    public function required()
    {
        [$validationRules, $validationMessage] = self::constructRules($this->columns['key'], 'required');
        $this->columns['validation'] = $validationRules;
        $this->columns['validationMessage'] = $validationMessage;
        return $this;
    }

    /**
     * To set a default value on the column
     * @param $value
     * @return $this
     */
    public function default($value)
    {
        $this->columns['default'] = $value;
        return $this;
    }

    /**
     * Create an event live on the column
     * @param int $debounceTime
     * @return $this
     */
    public function live(int $debounceTime = 500)
    {
        $this->columns['live'] = $debounceTime;
        return $this;
    }

    /**
     * Create an event change on the column
     * @param callable $callback
     * @return $this
     */
    public function onChange(callable $callback)
    {
        $this->columns['onChange'] = $callback;
        $this->columns['isOnChange'] = true;
        return $this;
    }

    /**
     * Bind the column value to another column
     * @param string $key
     * @return $this
     */
    public function bindValue(string $key)
    {
        $this->columns['bindValue'] = $key;
        return $this;
    }

    /**
     * This function is used to transform the column value
     * @param Closure $transform (function($value) { return $value; })
     * @return $this
     */
    public function transform(Closure $transform)
    {
        $this->columns['transform'] = $transform;
        return $this;
    }

    /**
     * This function is used to transform the value that will be passed to the input
     * @param Closure $transformValue (function($value) { return $value; })
     * @return $this
     */
    public function transformValue(Closure $transformValue)
    {
        $this->columns['transformValue'] = $transformValue;
        return $this;
    }

    /**
     * This function is used to transform the value for display in detail page only
     * @param Closure $transformDisplay (function($value) { return $value; })
     * @return $this
     */
    public function transformDisplay(Closure $transformDisplay)
    {
        $this->columns['transformDisplay'] = $transformDisplay;
        return $this;
    }

    /**
     * This function is used to set the column as readonly
     * @return $this
     */
    public function readonly()
    {
        $this->columns['readonly'] = true;
        return $this;
    }

    /**
     * This function is used to set the column as readonly with a condition
     * @param Closure $callback (function($data) { return true; })
     * @return $this
     */
    public function readonlyOn(Closure $callback)
    {
        $this->columns['readonlyOn'] = $callback;
        return $this;
    }

    /**
     * This function is used to set the placeholder text
     * @param string $placeholderText
     * @return $this
     */
    public function placeholder(string $placeholderText)
    {
        $this->columns['placeholder'] = $placeholderText;
        return $this;
    }

    /**
     * This function is used to set the help text
     * @param string $helpText
     * @return $this
     */
    public function help(string $helpText)
    {
        $this->columns['helpText'] = $helpText;
        return $this;
    }

    /**
     * This function is used to set the option
     * @param TypeOptionAbstract|array $optionConfig
     * @return $this
     */
    public function option(TypeOptionAbstract|array $optionConfig)
    {
        if($optionConfig instanceof TypeOptionAbstract) {
            $optionConfig = $optionConfig->__getOption();
        }
        $this->columns['option'] = array_merge($this->columns['option'], $optionConfig);
        return $this;
    }

    /**
     * This to set the column as hidden on the form detail
     * @param bool $showDetail
     * @return $this
     */
    public function showDetail(bool $showDetail = true)
    {
        $this->columns['showDetail'] = $showDetail;
        return $this;
    }

    public function showEdit(bool $showEdit = true)
    {
        $this->columns['showEdit'] = $showEdit;
        return $this;
    }

    public function showCreate(bool $showCreate = true)
    {
        $this->columns['showCreate'] = $showCreate;
        return $this;
    }

    public function showOn(Closure $callback)
    {
        $this->columns['showOn'] = $callback;
        $this->columns['visible'] = false;
        return $this;
    }
}
