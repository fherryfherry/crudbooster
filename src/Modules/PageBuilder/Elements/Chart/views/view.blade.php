<div class="panel w-full">
    <div class="p-4">
        <div class="flex justify-between items-center">
            <div class="panel-header-title flex gap-2 items-center">
                {!! \CrudBooster\Components\Icon\Icon::valueOf($config['icon']) ?: null !!}
                <h2>{{$config['title']}}</h2>
            </div>
            <div class="flex items-center gap-1">

            </div>
        </div>
    </div>
    <div class="panel-content">
        <div x-data="{ chart: null }" x-init="
            chart = new Chart($refs.canvas.getContext('2d'), {
                type: '{{$config['chartType']}}',
                data: {
                    labels: $wire.labels,
                    datasets: $wire.datasets
                },
                options: {
                    indexAxis: '{{$config['indexAxis']??'x'}}',
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            beginAtZero: true,
                            stacked: true
                        }
                    }
                }
            });
            ">
            <canvas x-ref="canvas" class="w-full" height="200"></canvas>
        </div>
    </div>
</div>
