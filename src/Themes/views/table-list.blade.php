<div>
    <ul class="browse-list" id="list">
        @foreach($result as $i=>$row)
            @php $rowId = $row->id; @endphp
            <li wire:key="{{$row->id}}" class="cursor-grab" x-data="dragAndDrop()"
                draggable="true" @dragstart="dragStart($event)" @dragover="dragOver($event)" @drop="drop($event)">
                <div class="list-item">
                    <div class="w-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-5 inline-block">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.5 6a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM9.5 10a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM9.5 14a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM9.5 18a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 6a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 10a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 14a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 18a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1z"/>
                        </svg>
                    </div>
                    @if($buttonBulkAction)
                        <div>@if($row->__checkboxVisible)
                                <input type="checkbox" name="ids[]" wire:model.live="selectedIds"
                                       value="{{$row->id}}">
                            @endif
                        </div>
                    @endif

                    @foreach($browseColumns as $column)
                            <?php $content = isset($column['relation']) ? $row->{$column['relation']['key']} : $row->{$column['key']};
                            ?>
                        <div>
                            @if(isset($column['is_html']))
                                <span>{!! $content !!}</span>
                            @else
                                <span>{{ $content }}</span>
                            @endif
                        </div>
                    @endforeach

                    <div class="action">
                        @include('cb.themes::action')
                    </div>
                </div>

                @if($row->__childRows)
                    <ul class="browse-list" id="list-child-{{$row->id}}">
                        @foreach($row->__childRows as $subRow)
                            @php $rowId = $subRow->id; @endphp
                            <li wire:key="{{$subRow->id}}" class="cursor-grab" x-data="dragAndDrop()"
                                draggable="true" @dragstart="dragStart($event)" @dragover="dragOver($event)"
                                @drop="drop($event)">
                                <div class="list-item">
                                    <div class="w-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5"
                                             stroke="currentColor" class="size-5 inline-block">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9.5 6a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM9.5 10a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM9.5 14a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM9.5 18a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 6a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 10a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 14a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1zM14.5 18a0.5 0.5 0 1 1 0-1 0.5 0.5 0 1 1 0 1z"/>
                                        </svg>
                                    </div>
                                    @foreach($browseColumns as $column)
                                            <?php $content = isset($column['relation']) ? $subRow->{$column['relation']['key']} : $subRow->{$column['key']};
                                            ?>
                                        <div>
                                            @if(isset($column['is_html']))
                                                <span>{!! $content !!}</span>
                                            @else
                                                <span>{{ $content }}</span>
                                            @endif
                                        </div>
                                    @endforeach

                                    <div class="action">
                                        @include('cb.themes::action')
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>

    @if($browseDraggable)
        <script>
            function dragAndDrop() {
                return {
                    row: null,
                    parentUl: null,
                    dragStart(event) {
                        row = event.target.closest('li');
                        parentUl = row.closest('ul').getAttribute('id');
                    },
                    dragOver(event) {
                        event.preventDefault();
                        var e = event;

                        if (!e.target.closest('li')) {
                            return;
                        }

                        let targetUl = e.target.closest('ul').getAttribute('id');
                        if (parentUl !== targetUl) {
                            return;
                        }

                        let children = Array.from(e.target.closest('ul').children);

                        if (children.indexOf(e.target.closest('li')) > children.indexOf(row)) {
                            e.target.closest('li').after(row);
                        } else {
                            if (e.target.closest('ul').getAttribute('id') === parentUl) {
                                e.target.closest('li').before(row);
                            }
                        }
                    },
                    drop(event) {
                        event.preventDefault();
                        // get all index of tr
                        let trs = Array.from(event.target.closest('ul').children);
                        let ids = trs.map((tr, i) => ({key: tr.getAttribute('wire:key'), index: i}));
                        // call livewire method updateDraggableOrder
                        @this.updateDraggableOrder(ids)
                    },
                }
            }
        </script>
    @endif
</div>
