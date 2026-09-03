{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
@php
    \Illuminate\Support\Facades\Log::info('[CB Select Debug] form.blade.php executed');
@endphp
@if($column['option']['searchable'] ?? false)
    <livewire:select-component :column="$column" :key="$column['label']" wire:model.live="formData.{{$column['key']}}"/>
@else
    {{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
    @php
        // Get transformed dataset if available
        $dataset = $column['option']['dataset'] ?? [];
        if (isset($column['option']['transformLabel'])) {
            \Illuminate\Support\Facades\Log::info('[CB Select Debug] transformLabel', [
                'type' => gettype($column['option']['transformLabel']),
                'value' => $column['option']['transformLabel']
            ]);
            $transformCode = $column['option']['transformLabel'];
            if (is_string($transformCode) && !empty($transformCode)) {
                try {
                    $callback = eval("return function(\$label, \$key, \$row) { $transformCode };");
                    $dataset = array_map(function ($item) use ($callback) {
                        if (isset($item['options'])) {
                            // For grouped options
                            $item['options'] = array_map(function ($option) use ($callback) {
                                $option['label'] = $callback($option['label'], $option['key'], $option);
                                return $option;
                            }, $item['options']);
                        } else {
                            // For simple options
                            $item['label'] = $callback($item['label'], $item['key'], $item);
                        }
                        return $item;
                    }, $dataset);
                } catch (\Exception $e) {
                    // If there's an error in the transform code, use original dataset
                }
            } elseif (is_callable($transformCode)) {
                $callback = $transformCode;
                $dataset = array_map(function ($item) use ($callback) {
                    if (isset($item['options'])) {
                        // For grouped options
                        $item['options'] = array_map(function ($option) use ($callback) {
                            $option['label'] = $callback($option['label'], $option['key'], $option);
                            return $option;
                        }, $item['options']);
                    } else {
                        // For simple options
                        $item['label'] = $callback($item['label'], $item['key'], $item);
                    }
                    return $item;
                }, $dataset);
            }
        }
    @endphp
    <select id="{{$column['key']}}"
            wire:model.live="formData.{{$column['key']}}"
            wire:loading.attr="readonly"
            wire:target="formSave"
            @if(isset($column['isOnChange']) && $column['isOnChange']) @change="$wire.__onChangeFormField('{{$column['key']}}', $event.target.value)" @endif
            @readonly($column['readonly'] ?? false)
            class="form-control">
        @if(isset($column['placeholder']) && $column['placeholder'] != '')
            <option value="">{{$column['placeholder']}}</option>
        @else
            <option value="">-- Select {{$column['label']}} --</option>
        @endif
        @foreach($dataset as $group)
            @if(isset($group['options']))
                <optgroup label="{{$group['label']}}">
                    @foreach($group['options'] as $row)
                        <option value="{{$row['key']}}">{{$row['label']}}</option>
                    @endforeach
                </optgroup>
            @else
                <option value="{{$group['key']}}">{{$group['label']}}</option>
            @endif
        @endforeach
    </select>
@endif
