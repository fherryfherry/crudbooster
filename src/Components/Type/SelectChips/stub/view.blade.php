{{-- There are variables $column, $value, and $formData that you can use --}}
<div class="flex flex-wrap items-center">
    @if(isset($formData[$column['key']]))
        @foreach($formData[$column['key']] as $chip)
            <div class="flex items-center bg-sky-500 text-sm text-white dark:bg-sky-700 dark:text-gray-200 rounded-full px-3 py-1 m-1">
                <span>{{$chip['label']}}</span>
            </div>
        @endforeach
        @if(count($formData[$column['key']]) == 0)
            <div class="text-gray-500 dark:text-gray-400 text-sm">No data</div>
        @endif
    @endif
</div>
