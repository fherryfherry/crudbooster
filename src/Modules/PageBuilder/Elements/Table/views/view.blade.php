<div class="panel w-full">
    <div class="px-4 pt-4">
        <div class="flex justify-between items-center">
            <!-- if icon svg is set then show icon -->
            @if(isset($config['icon']))
            <div class="flex items-center gap-2">
                <div class="icon">
                    {!! \CrudBooster\Components\Icon\Icon::valueOf($config['icon']) !!}
                </div>
                <h1>{{$config['title']}}</h1>
            </div>
            @else
            <h1>{{$config['title']}}</h1>
            @endif

            <div class="flex gap-1">
                @if(isset($config['showAllLink']))
                <a href="{{ $config['showAllLink'] }}" class="btn btn-outline-light" target="_blank">Show All</a>
                @endif
            </div>
        </div>
    </div>
    <div class="panel-content">
        <table class="table table-bordered">
            <thead>
                <tr>
                    @foreach(($results[0]??[]) as $key=>$val)
                    <th>{{ $key }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                <tr>
                    @foreach($result as $key=>$val)
                    <td class="td-no-wrap">{{ \Illuminate\Support\Str::limit($result->{$key}??'', 50) }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
