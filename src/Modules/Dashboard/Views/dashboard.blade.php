@php use CrudBooster\Components\Icon\Icon; @endphp
<div>
    <!-- Page Header -->
    <div class="w-full pt-2 pb-2 flex flex-wrap gap-2 justify-between items-center mb-4 rounded-md">
        <div class="text-xl text-gray-500 font-bold">
            Dashboard (Demo)
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center justify-between gap-3">
            <div
                class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 shadow-md flex items-center justify-between rounded-md bg-white dark:bg-gray-800 px-2 py-1.5 gap-3 border border-gray-300 dark:border-gray-600">
                <div class="flex items-center pr-2 border-r dark:border-gray-600">
                    {!! Icon::DATE !!}
                    <span class="ml-2 text-gray-500 dark:text-gray-400 overflow-ellipsis w-full">Oct 18 - Nov 18</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-500 dark:text-gray-400">Monthly</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500 dark:text-gray-400"
                         viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <a href="javascript:" class="btn btn-outline-light shadow-md dark:bg-gray-800 dark:text-gray-400">Filter</a>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Section 1 -->
    <div class="flex flex-grow-2 gap-4 mb-4 overflow-auto">
        <!-- Widget 1 -->
        <div class="widget widget-light space-y-4">
            <div class="flex justify-start items-center gap-4">
                {!! Icon::EYE !!}
                <h2 class="font-light dark:text-gray-300">Page Views</h2>
            </div>
            <div class="flex justify-start items-center gap-4">
                <div class="text-3xl font-light dark:text-gray-300">12,450</div>
                <div class="badge-green">+10% {!! Icon::UP !!}</div>
            </div>
        </div>

        <div class="widget widget-light space-y-4">
            <div class="flex justify-start items-center gap-4">
                {!! Icon::DB !!}
                <h2 class="font-light dark:text-gray-300">Total Revenue</h2>
            </div>
            <div class="flex justify-start items-center gap-4">
                <div class="text-3xl font-light dark:text-gray-300">$ 345,678</div>
                <div class="badge-red">-15% {!! Icon::DOWN !!}</div>
            </div>
        </div>

        <div class="widget widget-light space-y-4">
            <div class="flex justify-start items-center gap-4">
                {!! Icon::BOLT !!}
                <h2 class="font-light dark:text-gray-300">Bounce Rate</h2>
            </div>
            <div class="flex justify-start items-center gap-4">
                <div class="text-3xl font-light dark:text-gray-300">56%</div>
                <div class="badge-green">+20% {!! Icon::UP !!}</div>
            </div>
        </div>

        <div class="widget widget-light space-y-4">
            <div class="flex justify-start items-center gap-4">
                {!! Icon::USER !!}
                <h2 class="font-light dark:text-gray-300">New Members</h2>
            </div>
            <div class="flex justify-start items-center gap-4">
                <div class="text-3xl font-light dark:text-gray-300">15,678</div>
                <div class="badge-green">+65% {!! Icon::UP !!}</div>
            </div>
        </div>
    </div>
    <!-- End Section 1 -->

    <!-- Section 2 -->
    <div class="flex flex-col lg:flex-row justify-between gap-4 mb-4">
        <div class="panel w-full lg:w-3/4">
            <div class="p-4">
                <div class="flex flex-wrap justify-between items-center">
                    <div class="panel-header-title flex gap-2 items-center">
                        {!! Icon::CHART !!}
                        <h2>Overview</h2>
                    </div>
                    <div class="flex items-center gap-1 lg:hidden">
                        <a href="javascript:" class="btn btn-outline-light active">1 Year</a>
                        <a href="javascript:" class="btn btn-outline-light">6 Months</a>
                        <a href="javascript:" class="btn btn-outline-light">3 Months</a>
                        <a href="javascript:" class="btn btn-outline-light">1 Month</a>
                    </div>
                </div>
            </div>
            <div class="panel-content">
                <div x-data="{ chart: null }" x-init="
                        chart = new Chart($refs.canvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: $wire.labels,
                                datasets: [{
                                    label: 'My Dataset',
                                    data: $wire.dataPoints,
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    ">
                    <canvas x-ref="canvas" width="500" height="350"></canvas>
                </div>
            </div>
        </div>
        <div class="panel w-full lg:w-1/4">
            <div class="panel-header">
                <div class="panel-header-title flex gap-2 items-center">
                    {!! Icon::CHART !!}
                    <h2>Visits</h2>
                </div>
            </div>
            <div class="panel-content">
                <div x-data="{ chart: null }" x-init="
                        chart = new Chart($refs.canvas.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: $wire.labelBrowser,
                                datasets: [{
                                    label: 'My Dataset',
                                    data: $wire.dataBrowser,
                                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'],
                                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
                                    borderWidth: 1,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    ">
                    <canvas x-ref="canvas" width="500" height="250"></canvas>
                </div>
                <table class="table table-bordered mt-2">
                    <thead>
                    <tr>
                        <th>Browser</th>
                        <th>Visits</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Chrome</td>
                        <td>12,345</td>
                    </tr>
                    <tr>
                        <td>Firefox</td>
                        <td>10,345</td>
                    </tr>
                    <tr>
                        <td>Safari</td>
                        <td>8,345</td>
                    </tr>
                    <tr>
                        <td>Edge</td>
                        <td>6,345</td>
                    </tr>
                    <tr>
                        <td>Opera</td>
                        <td>4,345</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Section 2 -->

    <!-- Section 3 -->
    <div class="flex flex-col lg:flex-row justify-between gap-4 mb-4">
        <div class="panel w-full lg:w-[550px]">
            <div class="panel-header">
                <div class="panel-header-title flex gap-2 items-center">
                    {!! Icon::CHART !!}
                    <h2>Revenue</h2>
                </div>
            </div>
            <div class="panel-content">
                <div x-data="{ chart: null }" x-init="
                        chart = new Chart($refs.canvas.getContext('2d'), {
                            type: 'polarArea',
                            data: {
                                labels: $wire.labelRevenue,
                                datasets: [{
                                    label: 'My Dataset',
                                    data: $wire.dataRevenue,
                                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'],
                                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    ">
                    <canvas x-ref="canvas" width="100%" height="230"></canvas>
                </div>
            </div>
        </div>
        <div class="panel w-full">
            <div class="p-4">
                <div class="flex flex-wrap justify-between items-center">
                    <div class="panel-header-title flex gap-2 items-center">
                        {!! Icon::CHART !!}
                        <h2>Orders</h2>
                    </div>
                    {{--                    <div class="flex items-center gap-1">--}}
                    {{--                        <a href="javascript:" class="btn btn-outline-light active">All</a>--}}
                    {{--                        <a href="javascript:" class="btn btn-outline-light">Pending</a>--}}
                    {{--                        <a href="javascript:" class="btn btn-outline-light">Completed</a>--}}
                    {{--                        <a href="javascript:" class="btn btn-outline-light">Canceled</a>--}}
                    {{--                    </div>--}}
                </div>
            </div>
            <div class="panel-content overflow-auto">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>Product 1</td>
                        <td>$ 100</td>
                        <td><span class="badge-green">Completed</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jane Doe</td>
                        <td>Product 2</td>
                        <td>$ 200</td>
                        <td><span class="badge-green">Completed</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>John Doe</td>
                        <td>Product 3</td>
                        <td>$ 300</td>
                        <td><span class="badge-red">Pending</span></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Jane Doe</td>
                        <td>Product 4</td>
                        <td>$ 400</td>
                        <td><span class="badge-green">Completed</span></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>John Doe</td>
                        <td>Product 5</td>
                        <td>$ 500</td>
                        <td><span class="badge-red">Pending</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Section 3 -->
</div>
