<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Pastikan nama 'Nestora' dan title sesuai --}}
        <title>{{ config('app.name', 'Nestora') }} - Login</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- Background Gradasi sesuai tema nestora-lime --}}
        {{-- Anda bisa menyesuaikan 'from' dan 'to' agar gradasi lebih halus --}}
        <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-lime-100 via-nestora-lime to-green-100 dark:from-lime-900 dark:via-green-900 dark:to-green-800">
             {{-- Hapus logo default jika ada --}}
            {{-- <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div> 

            {{-- Container utama untuk card dua kolom, dibuat lebih lebar --}}
            <div class="w-full max-w-4xl mt-6">
                {{ $slot }} {{-- Konten login.blade.php akan masuk di sini --}}
            </div>
        </div>
    </body>
</html>