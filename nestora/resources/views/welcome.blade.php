<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nestora - Manajemen Properti & Prediksi Harga</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .gradient-bg {
            background: linear-gradient(120deg, #f0fdf4 0%, #dcfce7 100%);
        }
        .dark .gradient-bg {
             background: linear-gradient(120deg, #111827 0%, #1f2937 100%);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .navbar-link-active {
            position: relative;
        }
        .navbar-link-active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #84cc16; /* lime-500 */
        }
         /* CSS tambahan untuk animasi scroll */
        .fade-in-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-in-visible {
            opacity: 1;
            transform: translateY(0);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body
    x-data="{ scrolled: false, mobileMenuOpen: false }"
    @scroll.window="scrolled = (window.scrollY > 50)"
    class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 selection:bg-lime-300 selection:text-lime-900"
>

    {{-- ========================== Navbar ========================== --}}
    <nav
        :class="{ 'bg-white dark:bg-gray-800 shadow-md': scrolled, 'bg-transparent': !scrolled }"
        class="fixed w-full z-30 top-0 transition-colors duration-300 ease-in-out"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2 text-xl font-bold" title="Nestora Home">
                        <div class="flex items-center justify-center h-8 w-8 bg-lime-500 rounded text-white flex-shrink-0">N</div>
                        <span :class="{ 'text-gray-900 dark:text-white': scrolled, 'text-gray-900 dark:text-white': !scrolled }">Nestora</span>
                    </a>
                </div>

                {{-- Nav Links (Desktop) --}}
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-6">
                    <a href="#fitur" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-lime-600 dark:hover:text-lime-400 transition-colors duration-200">Fitur</a>
                    <!-- <a href="#kontak" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-lime-600 dark:hover:text-lime-400 transition-colors duration-200">Kontak</a> -->
                    <span class="border-l border-gray-200 dark:border-gray-700 h-6"></span>
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-lime-600 dark:hover:text-lime-400 transition-colors duration-200">Log in</a>
                        {{-- <a href="{{ route('register') }}" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-lime-800 bg-lime-200 hover:bg-lime-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 transition-colors duration-200">Register</a> --}}
                    @endauth
                </div>

                {{-- Mobile Menu Button --}}
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-lime-500 transition duration-150 ease-in-out" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg x-show="!mobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu Panel --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="sm:hidden absolute top-16 inset-x-0 p-2 transition transform origin-top-right"
        >
            <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="#fitur" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">Fitur</a>
                    <!-- <a href="#kontak" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">Kontak</a> -->
                </div>
                <div class="pt-4 pb-3">
                     @auth
                        <div class="flex items-center px-4 mb-3">
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="space-y-1">
                             <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">Dashboard</a>
                             <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" @click.prevent="$root.submit(); mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">Log Out</a>
                            </form>
                        </div>
                    @else
                        <div class="space-y-1">
                             <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">Log in</a>
                            {{-- <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">Register</a> --}}
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ========================== Hero Section ========================== --}}
    <main class="pt-16">
        <div class="relative isolate overflow-hidden gradient-bg">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-20 pb-24 sm:pt-28 sm:pb-32 lg:grid lg:grid-cols-5 lg:gap-x-20 lg:items-center lg:pt-36 lg:pb-40">

                {{-- Kolom Teks --}}
                <div class="lg:col-span-2 max-w-2xl mx-auto lg:mx-0 lg:pt-0 text-center lg:text-left">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-black sm:text-6xl lg:text-6xl fade-in" style="animation-delay: 0.1s;">
                        Kelola & Prediksi <span class="text-lime-600 dark:text-lime-600">Properti</span> Lebih Cerdas
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-black fade-in" style="animation-delay: 0.3s;">
                        Nestora adalah platform properti digital yang dilengkapi model prediksi harga berbasis Model serta memungkinkan transaksi langsung antara pemilik dan pencari properti secara cepat, mandiri, dan transparan.
                    </p>
                    <div class="mt-10 flex items-center justify-center lg:justify-start gap-x-6 fade-in" style="animation-delay: 0.5s;">
                        <a href="{{ route('login') }}" class="rounded-md bg-lime-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-lime-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lime-600 transition-colors duration-200">Mulai Sekarang</a>
                        <a href="#fitur" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">Pelajari Fitur <span aria-hidden="true">→</span></a>
                    </div>
                </div>

                {{-- Kolom Gambar --}}
                <div class="lg:col-span-3 mt-16 sm:mt-24 lg:mt-0 lg:flex-shrink-0 lg:flex-grow fade-in" style="animation-delay: 0.2s;">
                    <img src="{{ asset('images/prediksi.png') }}"
                         alt="Nestora Prediksi Properti"
                         class="w-full mx-auto rounded-xl shadow-xl ring-1 ring-gray-400/10"
                         width="1200" height="675" loading="lazy">
                </div>

            </div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-gray-50 dark:from-gray-900 pointer-events-none" aria-hidden="true"></div>
        </div>

        {{-- ========================== Fitur Section ========================== --}}
        <section id="fitur" class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="max-w-2xl mx-auto lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-lime-600 dark:text-lime-400">Kenapa Nestora?</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Semua yang Anda Butuhkan untuk Manajemen Properti</p>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                        Dari pengelolaan data hingga insight prediksi, Nestora menyediakan alat yang tepat untuk admin properti modern.
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">

                        {{-- Fitur 1: Manajemen Data --}}
                        <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                                <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-lime-100 dark:bg-lime-900 group-hover:bg-white">
                                     <svg class="h-6 w-6 text-lime-600 dark:text-lime-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                                </div>
                                Manajemen Data Terpusat
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Kelola dan organisir semua data properti yang masuk dari aplikasi mobile pengguna dalam satu dashboard yang rapi.</p>
                            </dd>
                        </div>

                        {{-- Fitur 2: Prediksi Harga AI --}}
                        <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                                 <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-lime-100 dark:bg-lime-900 group-hover:bg-white">
                                     <svg class="h-6 w-6 text-lime-600 dark:text-lime-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg>
                                </div>
                                Prediksi Harga AI
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Dapatkan estimasi harga properti yang akurat berdasarkan data historis dan fitur properti menggunakan model AI terintegrasi.</p>
                            </dd>
                        </div>

                        {{-- Fitur 3: API Terintegrasi --}}
                        <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                                 <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-lime-100 dark:bg-lime-900 group-hover:bg-white">
                                     <svg class="h-6 w-6 text-lime-600 dark:text-lime-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V5.75A2.25 2.25 0 0 0 18 3.5H6A2.25 2.25 0 0 0 3.75 5.75v12.5A2.25 2.25 0 0 0 6 20.25Z" />
                                     </svg>
                                </div>
                                API Terintegrasi
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Sediakan API endpoint untuk model prediksi yang dapat diakses langsung oleh aplikasi mobile pengguna Anda.</p>
                            </dd>
                        </div>

                    </dl>
                </div>
            </div>
        </section>

        {{-- ========================== Footer ========================== --}}
        <footer id="kontak" class="bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-12 px-6 lg:px-8 md:flex md:items-center md:justify-between">
                <div class="flex justify-center space-x-6 md:order-2">
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"></svg>
                    </a>
                </div>
                <div class="mt-8 md:mt-0 md:order-1">
                    <p class="text-center text-base text-gray-500 dark:text-gray-400">&copy; {{ date('Y') }} Nestora. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </main>

    {{-- Script animasi scroll --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Fungsi Animasi Fade-in on Scroll
            const observerOptions = {
                root: null, rootMargin: '0px', threshold: 0.1
            };
            const observerCallback = (entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-visible');
                        observer.unobserve(entry.target);
                    }
                });
            };
            const observer = new IntersectionObserver(observerCallback, observerOptions);
            const elementsToAnimate = document.querySelectorAll('#fitur dl > div');
            elementsToAnimate.forEach(el => {
                el.classList.add('fade-in-on-scroll');
                observer.observe(el);
            });
        });
    </script>

</body>
</html>