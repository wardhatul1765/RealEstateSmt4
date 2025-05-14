@props(['title'])

<div class="bg-white dark:bg-white overflow-hidden shadow-md rounded-lg p-5">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-900 mb-4">{{ $title }}</h3>
    {{ $slot }}
</div>
