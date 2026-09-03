<div x-data="formSelect{{md5($column['label'])}}()" class="relative">
    @php
        // Get transformed dataset if available
        $dataset = $column['option']['dataset'] ?? [];
        if (isset($column['option']['transformLabel'])) {
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
    <div class="fake-select form-control dark:bg-gray-800 dark:text-gray-200">
        <div class="flex justify-between items-center cursor-pointer" @click="openDropdownSelect = true">
            @if($selected??false)
                <span @click="openDropdownSelect = true" class="flex items-center gap-2 cursor-pointer"
                      x-text="selectedLabel"></span>
            @else
                <span @click="openDropdownSelect = true" class="cursor-pointer text-gray-400 dark:text-gray-500">- Please select an item -</span>
            @endif

            <!-- Clear Button-->
            <div class="action flex gap-2 items-center">
                @if($selected??false)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor"
                         class="size-4 cursor-pointer hover:text-gray-900 dark:hover:text-gray-100"
                         wire:click="resetItem">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                @endif

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-4 cursor-pointer hover:text-gray-900 dark:hover:text-gray-100"
                     @click="openDropdownSelect = true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </div>
            <!-- end clear button -->
        </div>
    </div>
    <div x-show="openDropdownSelect" @click.away="openDropdownSelect = false"
         class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 rounded-md shadow-lg border dark:border-gray-700">
        <!-- search bar -->
        <div class="p-2">
            <input type="text" class="form-control dark:bg-gray-700 dark:text-gray-200"
                   placeholder="Search {{$column['label']}}..." wire:model.live.debounce.500ms="keyword">
        </div>
        <div class="flex flex-col gap-1 overflow-y-auto dark:bg-gray-800" style="max-height: 300px">
            <div wire:loading
                 class="flex items-center gap-2 text-gray-300 dark:text-gray-400 p-2 border-b dark:border-gray-700 ">
                <span class="text-sm ">
                    Please wait loading...
                </span>
            </div>
            @foreach($dataset as $group)
                @if(isset($group['options']))
                    <div wire:loading.remove
                             class="flex items-center gap-2 text-gray-500 dark:text-gray-400 p-2 border-b dark:border-gray-700 ">
                        <span class="text-sm ">
                            {{$group['label']}}
                        </span>
                    </div>
                    @foreach($group['options'] as $row)
                        <div wire:loading.remove
                             class="flex items-center gap-2 cursor-pointer text-gray-500 dark:text-gray-400 p-2 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700"
                             @click="selectItem('{{ $row['key'] }}','{{ $row['label'] }}')">
                            <span class="text-sm dark:text-gray-200">&nbsp;&nbsp; {{ $row['label'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div wire:loading.remove
                         class="flex items-center gap-2 cursor-pointer text-gray-500 dark:text-gray-400 p-2 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700"
                         @click="selectItem('{{ $group['key'] }}','{{ $group['label'] }}')">
                        <span class="text-sm dark:text-gray-200">{{ $group['label'] }}</span>
                    </div>
                @endif
            @endforeach

            <div wire:loading.remove
                 class="flex items-center gap-2 text-gray-300 dark:text-gray-400 p-2 border-b dark:border-gray-700 ">
                    <span class="text-sm ">
                        Search for more result...
                    </span>
            </div>
        </div>
    </div>
</div>
<script>
    function formSelect{{md5($column['label'])}}() {
        return {
            openDropdownSelect: false,
            selectedKey: "{{$selected??null}}",
            selectedLabel: "{{$selectedLabel??null}}",
            selectItem(key, label) {
                this.selectedKey = key;
                this.selectedLabel = label;
                this.openDropdownSelect = false;
                @this.call('selectItem', key);
            },
        }
    }
</script>
