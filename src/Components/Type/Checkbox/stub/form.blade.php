{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
@php
    // Get transformed dataset if available
    $dataset = $column['option']['dataset'] ?? [];
    if (isset($column['option']['transformLabel'])) {
        $transformCode = $column['option']['transformLabel'];
        if (is_string($transformCode) && !empty($transformCode)) {
            try {
                $callback = eval("return function(\$label, \$key, \$row) { $transformCode };");
                $dataset = array_map(function ($item) use ($callback) {
                    if (isset($item['additional'])) {
                        // For model data with additional fields
                        $row = array_merge([
                            'key' => $item['key'],
                            'label' => $item['label'],
                        ], is_array($item['additional']) ? $item['additional'] : []);
                    } else {
                        // For simple dataset
                        $row = [
                            'key' => $item['key'],
                            'label' => $item['label'],
                        ];
                    }
                    $item['label'] = $callback($item['label'], $item['key'], (object)$row);
                    return $item;
                }, $dataset);
            } catch (\Exception $e) {
                // If there's an error in the transform code, use original dataset
            }
        } elseif (is_callable($transformCode)) {
            $callback = $transformCode;
            $dataset = array_map(function ($item) use ($callback) {
                if (isset($item['additional'])) {
                    // For model data with additional fields
                    $row = array_merge([
                        'key' => $item['key'],
                        'label' => $item['label'],
                    ], is_array($item['additional']) ? $item['additional'] : []);
                } else {
                    // For simple dataset
                    $row = [
                        'key' => $item['key'],
                        'label' => $item['label'],
                    ];
                }
                $item['label'] = $callback($item['label'], $item['key'], (object)$row);
                return $item;
            }, $dataset);
        }
    }
@endphp
@foreach($dataset as $group)
    <label class="input-checkbox-group">
        <input type="checkbox"
               wire:model.live="formData.{{$column['key']}}"
               wire:loading.attr="readonly"
               wire:target="formSave"
               @readonly($column['readonly'] ?? false)
               class="mr-1"
               value="{{$group['key']}}"
        >
        <span>{{$group['label']??'-'}}</span>
    </label>
@endforeach
