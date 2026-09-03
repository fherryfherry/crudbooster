<div x-data="boxCounter{{$id}}()" class="widget widget-light space-y-4">
    <div class="flex justify-start items-center gap-4">
        {!! \CrudBooster\Components\Icon\Icon::valueOf($config['icon']) ?: null !!}
        <h2 class="font-light">{{$config['title']??'Widget'}}</h2>
    </div>
    <div class="flex justify-start items-center gap-4">
        <div class="text-3xl font-light">
            @if($count)
                {{$count}}
            @else
                <!-- animate pulse -->
                <div class="animate-pulse bg-gray-200 h-10 w-20 rounded-md"></div>
            @endif
        </div>
        @if($percentage > 0 || $percentage < 0)
            <div class="badge-{{$percentage<0?'red':'green'}}">{{$percentage??0}}
                % {!! $percentage<0 ? \CrudBooster\Components\Icon\Icon::DOWN : \CrudBooster\Components\Icon\Icon::UP !!}</div>
        @endif
    </div>
</div>
<script>
    function boxCounter{{$id}}() {
        return {
            init() {
            @this.loadData();
            },
        }
    }
</script>
