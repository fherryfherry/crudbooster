<div x-data="settingPage()">
    <h1 class="text-2xl text-gray-800 dark:text-gray-200 font-bold mb-2">Setting</h1>
    <div class="flex justify-between items-center">
        <div class="sub-title text-md text-gray-800 dark:text-gray-200">All configuration setting</div>
        <div class="sub-title text-md text-gray-800 dark:text-gray-200 flex justify-center items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
            </svg>
            Environment: <span title="This setting is reflect for this environment" class="text-green-600 dark:text-green-400">{{config('app.env')}}</span></div>
    </div>

    <div class="flex items-start gap-2 flex-col lg:flex-row mt-4 h-full">
        <div class="bg-white dark:bg-gray-800 lg:w-1/4 p-5 rounded-lg h-full shadow dark:shadow-gray-700">
            <ul class="flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-hidden">
                @foreach(cbSettingRegistrarList() as $i=>$settingLabel)
                    <li wire:key="{{$i}}"><a class="block p-4 hover:bg-gray-100 dark:hover:bg-gray-700"
                                             :class="{'bg-sky-50 dark:bg-sky-900': currentSetting == '{{$settingLabel['name']}}', 'text-sky-600 dark:text-sky-400': currentSetting == '{{$settingLabel['name']}}' }"
                                             href="javascript:"
                                             @click="openSetting('{{$settingLabel['name']}}')">
                            <div class="flex items-center justify-start gap-2 text-nowrap">
                                {!! $settingLabel['icon'] !!}
                                <span>{{ $settingLabel['label'] }}</span>
                            </div>
                        </a></li>
                @endforeach
            </ul>
        </div>
        <!-- main content -->
        <div class="bg-white dark:bg-gray-800 w-full lg:w-3/4 rounded-lg shadow dark:shadow-gray-700 h-full">
            <div class="p-8">
                @foreach(cbSettingRegistrarList() as $set)
                    <div wire:key="{{$set['name']}}" x-show="currentSetting == '{{ $set['name'] }}'">
                        @livewire('cb.setting::'.$set['name'])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        function settingPage() {
            return {
                showShimmer: false,
                currentSetting: null,
                init() {
                    let hash = window.location.hash;
                    if (hash) {
                        this.openSetting(hash.replace('#', ''));
                    } else {
                        this.openSetting('basic-info');
                    }
                },
                openSetting(name) {
                    window.history.pushState({}, '', '#' + name);
                    this.currentSetting = name;
                }
            }
        }
    </script>
</div>
