<?php

use CrudBooster\Components\Type\Select\Function\Select;
use CrudBooster\Components\Type\TextArea\Function\TextArea;
use CrudBooster\Livewire\FormBuilder\Form;

return [
    Form::add(label: 'Data Select Type', key: 'input_options.optionName', type: 'select', placeholder: '** Select a Type')
        ->option(Select::option()->dataset([
            ['key' => 'dataset', 'label' => 'Dataset'],
            ['key' => 'datasetGroup', 'label' => 'Dataset Group'],
            ['key' => 'model', 'label' => 'Model'],
        ]))->showOn(function ($data) {
            return isset($data['input_options']) && $data['input_options']['type'] === 'select';
        }),
    /** For dataset */
    Form::add(label: 'Select: Dataset', key: "input_options.option.dataset", type: 'textarea', placeholder: "key1|label1
key2|label2
key3|label3
            ", helpText: 'For multiple value, separate it with new line.')
        ->showOn(function ($data) {
            return ($data['input_options']['optionName'] ?? '') === 'dataset' && $data['input_options']['type'] === 'select';
        })->option(TextArea::option()->heightRow(10)),
    /** For datasetGroup */
    Form::add(label: 'Select: DataGroup', key: "input_options.option.datasetGroup", type: 'textarea', placeholder: "group1
- key1|label1
- key2|label2
group2
- key3|label3
- key4|label4
            ", helpText: 'For multiple value, separate it with new line.')
        ->showOn(function ($data) {
            return ($data['input_options']['optionName'] ?? '') === 'datasetGroup' && $data['input_options']['type'] === 'select';
        })->option(TextArea::option()->heightRow(10)),

    /** For Model */
    Form::add(label: 'Select: Model', key: "input_options.option.model.modelName", type: 'select', placeholder: "- Select a Model -", helpText: 'This is the model class name that will be used to get the data for the select field')
        ->option(Select::option()->dataset(
            collect(getModelList())->map(function ($model) {
                return ['key' => $model, 'label' => $model];
            })->toArray()
        ))
        ->showOn(function ($data) {
            return ($data['input_options']['optionName'] ?? '') === 'model' && $data['input_options']['type'] === 'select';
        }), [
        Form::add(label: 'Select: Model Key', key: "input_options.option.model.key", type: 'text', placeholder: "E.g: id", helpText: 'This is the key that will be used as the value for the select field')
            ->showOn(function ($data) {
                return ($data['input_options']['optionName'] ?? '') === 'model' && $data['input_options']['type'] === 'select';
            }),
        Form::add(label: 'Select: Model Label', key: "input_options.option.model.label", type: 'text', placeholder: "E.g: name", helpText: 'This is the key that will be used as the label for the select field')
            ->showOn(function ($data) {
                return ($data['input_options']['optionName'] ?? '') === 'model' && $data['input_options']['type'] === 'select';
            })],
    Form::add(label: 'Select: Model Query', key: "input_options.option.model.queryCallback", type: 'textarea', placeholder: "E.g: function(\$query, \$id = null) {\$query->where('status', 1)}", helpText: 'This is the query that will be used to get the data for the select field. You can use $id parameter to access current form data ID.')
        ->showOn(function ($data) {
            return ($data['input_options']['optionName'] ?? '') === 'model' && $data['input_options']['type'] === 'select';
        }),

    /** For Label Transformation */
    Form::add(label: 'Select: Label Transformation', key: "input_options.option.transformLabel", type: 'textarea', placeholder: "function(\$label, \$key, \$row) { return strtoupper(\$label); }", helpText: 'Optional: Custom function to transform label display. Parameters: $label (current label), $key (option key), $row (full option data). Return the transformed label.')
        ->showOn(function ($data) {
            return isset($data['input_options']) && $data['input_options']['type'] === 'select';
        })->option(TextArea::option()->heightRow(5)),
];
