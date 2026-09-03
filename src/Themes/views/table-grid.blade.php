<div x-data="{openThumbnail: false, thumbnailSrc: ''}">
    <table class="table">
        <thead>
        <tr>
            @if($buttonBulkAction)
                <th scope="col">
                    <input type="checkbox" class="cursor-pointer" name="ids[]"
                           x-on:click="$wire.triggerSelectAll($event.target.checked)"
                           value="all">
                </th>
            @endif
            @foreach($browseColumns as $column)
                <th scope="col" class="whitespace-nowrap" title="Sort by {{$column['label']}}"
                    @if($column['sortable'])wire:click="sortBy('{{$column['key']}}')"@endif>
                    <span class="inline-flex items-center gap-1 whitespace-nowrap">
                        {{$column['label']}}
                        @if($sortField == $column['key'] && $sortDirection == 'asc')
                            &downarrow;
                        @elseif($sortField == $column['key'] && $sortDirection == 'desc')
                            &uparrow;
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-3 inline-block align-middle">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/>
                            </svg>
                        @endif
                    </span>
                </th>
            @endforeach
            <th scope="col">
                <div class="text-center">Action</div>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($result as $i=>$row)
            @php $rowId = $row->id; @endphp
            <tr wire:key="{{$row->id}}">
                @if($buttonBulkAction)
                    <td>@if($row->__checkboxVisible)
                            <input type="checkbox" name="ids[]" wire:model.live="selectedIds"
                                   value="{{$row->id}}">
                        @endif
                    </td>
                @endif

                @foreach($browseColumns as $column)
                        <?php $content = isset($column['relation']) ? $row->{$column['relation']['key']} : $row->{$column['key']}; ?>
                    @php $nowrap = !empty($column['no_wrap']); @endphp
                    @if(isset($column['is_html']))
                        <td class="whitespace-nowrap" @if($nowrap) style="white-space:nowrap;" @endif>{!! $content !!}</td>
                    @else
                        <td class="whitespace-nowrap" @if($nowrap) style="white-space:nowrap;" @endif>{{ $content }}</td>
                    @endif
                @endforeach

                <td class="td-nowrap w-1/12" style="min-width:80px;">
                    <div class="flex justify-center items-center gap-1">
                        @include('cb.themes::action', ['actionButtonMode' => $actionButtonMode ?? 'inline'])
                    </div>
                </td>
            </tr>
        @endforeach
        @if($result->isEmpty())
            <tr>
                <td colspan="{{count($browseColumns) + 1}}">
                    <div class="text-center text-gray-400">No data available</div>
                </td>
            </tr>
        </tbody>
        @endif
    </table>
    <div class="mt-4 border-t py-3 dark:border-gray-600">
        {{ $result->links('cb.themes::pagination') }}
    </div>

    <!-- Modal Image Thumbnail -->
    @include('cb.themes::components.imagepreview')
</div>
