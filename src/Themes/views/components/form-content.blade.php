@foreach($formColumns as $i=>$formColumn)
    @if(is_array($formColumn) && !array_key_exists('key', $formColumn))
        @php
            // Check if any field in this column array is visible
            $hasVisibleFields = false;
            foreach($formColumn as $column) {
                if($column['visible'] ?? false) {
                    $hasVisibleFields = true;
                    break;
                }
            }
        @endphp
        @if($hasVisibleFields)
            <div class="form-group flex space-x-4">
                @foreach($formColumn as $e=>$column)
                    @if($column['visible']??false)
                        <div class="w-1/2">
                            @if(isset($column['key']))
                                @include('cb.themes::form-input', ['column'=>$column, 'i' => $e, 'formData'=> $formData])
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    @else
        @if($formColumn['visible']??false)
            <div class="form-group">
                @if(isset($formColumn['key']))
                    @include('cb.themes::form-input', ['column'=>$formColumn, 'i' => $i, 'formData'=> $formData])
                @endif
            </div>
        @endif
    @endif
@endforeach
