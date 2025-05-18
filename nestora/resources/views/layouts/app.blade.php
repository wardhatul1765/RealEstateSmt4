<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nestora') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>


<body class="font-sans antialiased">
    {{-- Tambahkan x-data untuk state sidebar --}}
    <div x-data="{ isSidebarOpen: true }" class="min-h-screen flex bg-gray-100">
        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Area Konten Utama --}}
        {{-- Ganti 'transition-all' menjadi 'transition-[margin-left]' --}}
        <div class="flex-1 flex flex-col transition-[margin-left] duration-300 ease-in-out" :class="isSidebarOpen ? 'md:ml-64' : 'md:ml-0'">
                                          {{-- ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ --}}
            {{-- Navbar - Cukup include satu kali --}}
            @include('layouts.navigation')

            {{-- Page Heading (jika ada) --}}
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-full mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 p-6 bg-gray-50">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
