@props(['title', 'value', 'unit'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-5">
    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $title }}</h3>
    <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $value }}</div>
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $unit }}</p>
</div>
