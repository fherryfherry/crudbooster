<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ basicInfoSetting()->getAppName() ?? 'CRUDBooster' }}</title>
    <link rel="shortcut icon" href="{{ appearanceSetting()->getFavicon() ? getStorageUrl(appearanceSetting()->getFavicon()) : asset('vendor/crudbooster/themes/assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{asset('vendor/crudbooster/themes/assets/css/app.min.css')}}?v={{time()}}">
    @cbAssets
</head>
<body class="antialiased">
<div
    x-data="cbApp()"
    class="flex h-screen">
    <!-- Image Preview -->
    @include('cb.themes::components.imagepreview')
    @livewire('alert-message')
    <!-- Sidebar -->
    <div wire:ignore x-show="sidebar" x-cloak @click.away="sidebar = (window.innerWidth < 768) ? false : sidebar"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full" class="sidebar">
        @include('cb.themes::sidebar')
    </div>
    <!-- Main Area -->
    <div class="main-area" :class="{'blur': window.innerWidth < 768 ? sidebar : false}" x-cloak>
        <!-- Main Area Top Bar -->
        <div class="top-bar">
            <button type="button"
                    @click="sidebar = !sidebar; document.cookie = 'sidebar=' + sidebar + '; path=/; max-age=' + (7 * 24 * 60 * 60);"
                    class="text-gray-500">
                {!! \CrudBooster\Components\Icon\Icon::BAR !!}
            </button>

            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button type="button" x-show="!darkMode" @click="toggleDarkMode" class="text-gray-500">
                    {!! \CrudBooster\Components\Icon\Icon::LIGHT !!}
                </button>
                <button type="button" x-show="darkMode" @click="toggleDarkMode" class="text-gray-500">
                    {!! \CrudBooster\Components\Icon\Icon::MOON !!}
                </button>

                @foreach(cbTopBarComponents() as $component)
                    @livewire($component['name'])
                @endforeach
            </div>
        </div>

        <!-- Main Area Content -->
        <div class="p-1 lg:p-4">
            {{$slot}}
        </div>
    </div>
</div>
<script>
    function cbApp() {
        return {
            openThumbnail: false,
            thumbnailSrc: '',
            thumbnailCaption: '',
            sidebar: true,
            darkMode: document.documentElement.classList.contains('dark'),
            init() {
                let sidebarCookie = document.cookie.split('; ').find(row => row.startsWith('sidebar=')) ? document.cookie.split('; ').find(row => row.startsWith('sidebar='))?.split('=')[1] === 'true' : null;
                this.sidebar = sidebarCookie !== null ? sidebarCookie : (window.innerWidth >= 768);

                // if mobile, close sidebar by default
                if (window.innerWidth < 768) {
                    this.sidebar = false;
                }

                this.darkMode = (document.cookie.split('; ').find(row => row.startsWith('darkMode='))?.split('=')[1] === 'true');
                document.documentElement.classList.toggle('dark', this.darkMode);
            },
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
                document.documentElement.classList.toggle('dark', this.darkMode);
                document.cookie = 'darkMode=' + this.darkMode + '; path=/; max-age=' + (7 * 24 * 60 * 60);
            },
            showPreviewImage(imageUrl) {
                this.openThumbnail = true;
                this.thumbnailSrc = imageUrl;
                // get base name
                this.thumbnailCaption = imageUrl.split('/').pop();
            }
        }
    }
</script>
</body>
</html>
