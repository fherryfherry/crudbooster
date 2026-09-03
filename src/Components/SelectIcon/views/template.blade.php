<div x-data="{openDropdownSelect: false}" class="relative">
    <div class="fake-select form-control dark:bg-gray-800 dark:text-gray-200">
        <div class="flex justify-between items-center">
            @if($selected)
                <span @click="openDropdownSelect = true"
                      class="flex items-center gap-2 cursor-pointer">{!! \CrudBooster\Components\Icon\Icon::valueOf($selected) !!} {{$selected}}</span>
            @else
                <span @click="openDropdownSelect = true" class="cursor-pointer text-gray-400 dark:text-gray-500">- Please select an item -</span>
            @endif

            <div class="action flex gap-2 items-center">
                @if($selected)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-4 cursor-pointer hover:text-gray-900 dark:hover:text-gray-100" wire:click="unselectIcon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                @endif

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-4 cursor-pointer hover:text-gray-900 dark:hover:text-gray-100" @click="openDropdownSelect = true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </div>
        </div>
    </div>
    <div x-show="openDropdownSelect" @click.away="openDropdownSelect = false"
         class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 rounded-md shadow-lg border dark:border-gray-700">
        <!-- search bar -->
        <div class="p-2">
            <input type="text" class="form-control dark:bg-gray-700 dark:text-gray-200" placeholder="Search icon" wire:model.live.debounce.400ms="keyword">
        </div>
        <div class="flex flex-col gap-1 overflow-y-auto dark:bg-gray-800" style="max-height: 300px">
            @foreach($this->getIcons() as $icon)
                <div class="flex items-center gap-2 cursor-pointer text-gray-500 dark:text-gray-400 p-2 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700"
                     @click="openDropdownSelect = false" wire:click="selectIcon('{{ $icon['key'] }}')">
                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-md flex items-center justify-center">
                        {!! $icon['value'] !!}
                    </div>
                    <span class="text-sm dark:text-gray-200">{{ $icon['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
