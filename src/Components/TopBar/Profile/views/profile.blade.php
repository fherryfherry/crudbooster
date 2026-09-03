<div>
    <!-- Profile Image -->
    <div x-data="{ open: false }" class="relative">
        <a href="javascript:void(0)" @click="open = !open">
            <img
                src="{{ auth()->user()->photo ? getStorageUrl(auth()->user()->photo) : asset(config('cb.default_avatar')) }}"
                alt="Profile Image" class="thumbnail w-8 h-8">
        </a>
        <div x-show="open" @click.away="open = false"
             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg text-sm z-50">
            <h3 class="px-4 py-2 text-gray-400 dark:text-gray-200 font-bold truncate">Welcome {{ auth()->user()->name }}</h3>
            <a href="{{route('profile')}}" wire:navigate
               class="px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.232 5.232l3.536 3.536M9 11.25l6.75-6.75a2.121 2.121 0 113 3L12 14.25H9v-3z"/>
                </svg>
                <span>Edit Profile</span>
            </a>
            <a href="{{route('logout')}}" wire:navigate
               class="px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 9V5.25a.75.75 0 00-.75-.75h-6a.75.75 0 00-.75.75V9m0 6v3.75a.75.75 0 00.75.75h6a.75.75 0 00.75-.75V15m-6-6h6m-6 6h6m-6-6l-3 3m3-3l-3-3"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
</div>
