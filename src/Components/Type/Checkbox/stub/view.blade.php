{{-- There are variables $column, $value, and $formData that you can use --}}
@if(isset($column['option']['dataset']))
    @php 
        $dataset = $column['option']['dataset'];
        // Apply label transformation if available
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
        $columnValue = isset($value) ? collect($dataset)->whereIn('key', $value)->pluck('label')->implode(', ') : null; 
    @endphp
    {{ $columnValue ? $columnValue : '-' }}
@endif
