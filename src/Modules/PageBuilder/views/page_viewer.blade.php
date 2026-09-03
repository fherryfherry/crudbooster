<div class="space-y-2">
    @include('cb.page-builder::page_top_viewer')
    <div class="flex flex-grow gap-4 h-screen">
        <div class="w-full" id="grid-wrapper">
            <div id="page-area-grid" class="h-screen space-y-4">
                @foreach($grid as $rowIndex=>$columns)
                <div class="flex items-start gap-4">
                    @foreach($columns as $colIndex=>$column)
                    @if($column['content']['type'] ?? false)
                    @livewire($column['content']['type'].'-viewer', ['id'=>$rowIndex.$colIndex,'config'=>$column['content']['config']])
                    @else
                    <div class="w-full">
                        <!-- empty widget -->
                    </div>
                    @endif
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div> <!-- end of page studio -->
