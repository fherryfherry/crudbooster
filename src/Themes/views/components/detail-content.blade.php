@foreach($formColumns as $column)
    @if(is_array($column) && !array_key_exists('key', $column))
        @php $anyChild = collect($column)->filter(fn($f) => $f['showDetail']??false)->count(); @endphp
        @if($anyChild)
            <div class="form-group flex space-x-4">
                @foreach($column as $subColumn)
                    @if(isset($subColumn['showDetail']) && $subColumn['showDetail'] && $subColumn['visible'])
                        <div class="w-1/2">
                            <label for="{{$subColumn['key']??''}}">{{$subColumn['label'] ?? ''}}</label>
                            <div id="{{$subColumn['key']??''}}" class="mt-2 text-sm rounded">
                                {!! createViewTag($subColumn, data_get($formData, $subColumn['key'], ''), $formData) !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    @else
        @if(isset($column['showDetail']) && $column['showDetail'] && $column['visible'])
            <div class="form-group overflow-auto">
                <label for="{{$column['key']}}">{{$column['label'] ?? ''}}</label>
                <div id="{{$column['key']}}" class="mt-2 text-sm rounded">
                    {!! createViewTag($column, data_get($formData,$column['key'],''), $formData) !!}
                </div>
            </div>
        @endif
    @endif
@endforeach
