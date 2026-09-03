<div class="w-full bg-white dark:bg-gray-800 pl-4 pr-4 pt-2 pb-2 flex flex-wrap justify-between items-center border border-gray-200 dark:border-gray-700 rounded-md shadow">
    <div class="text-xl text-gray-500 dark:text-gray-300 font-bold">
        {{ $pageTitle ?? "Dashboard" }}
    </div>
    <div class="text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ getCmsUrl('dashboard') }}" wire:navigate>Dashboard</a>
        &raquo; {{ $pageTitle ?? "Dashboard" }}
    </div>
</div>
