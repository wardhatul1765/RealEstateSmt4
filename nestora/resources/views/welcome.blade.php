<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth"
    x-data="{
        darkMode: localStorage.getItem('darkMode') !== 'false',
        scrolled: false,
        mobileMenuOpen: false,
        activeSection: 'hero'
    }"
    :class="{ 'dark': darkMode }"
    @scroll.window="scrolled = (window.scrollY > 50)"
>
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
            color: #65a30d !important; /* lime-600 */
        }
        .dark .navbar-link-active {
            color: #a3e635 !important; /* lime-400 */
        }
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
        .hero-image-glow::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80%;
            height: 80%;
            background-image: radial-gradient(circle, rgba(163, 230, 53, 0.25), transparent 70%);
            transform: translate(-50%, -50%);
            filter: blur(60px);
            z-index: -1;
            transition: opacity 0.3s ease-in-out;
        }
        .dark .hero-image-glow::before {
            background-image: radial-gradient(circle, rgba(163, 230, 53, 0.15), transparent 70%);
        }
    </style>
</head>
<body
    x-init="
        $watch('darkMode', val => localStorage.setItem('darkMode', val));
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    activeSection = entry.target.id;
                }
            });
        }, { rootMargin: '-30% 0px -70% 0px' });
        document.querySelectorAll('main section').forEach(section => {
            observer.observe(section);
        });
    "
    class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 selection:bg-lime-300 selection:text-lime-900"
>

    {{-- ========================== Navbar ========================== --}}
    <nav
        :class="{ 'bg-white/80 dark:bg-gray-800/80 shadow-md backdrop-blur-sm': scrolled, 'bg-transparent': !scrolled }"
        class="fixed w-full z-30 top-0 transition-all duration-300 ease-in-out"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2 text-xl font-bold" title="Nestora Home">
                        <div class="flex items-center justify-center h-8 w-8 bg-lime-500 rounded text-white flex-shrink-0">N</div>
                        <span class="text-gray-900 dark:text-white">Nestora</span>
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
                    <a href="#fitur" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-lime-600 dark:hover:text-lime-400 transition-colors duration-200" :class="{ 'navbar-link-active': activeSection === 'fitur' }">Fitur</a>
                    <a href="#testimonials" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-lime-600 dark:hover:text-lime-400 transition-colors duration-200" :class="{ 'navbar-link-active': activeSection === 'testimonials' }">Testimoni</a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ml-6 sm:space-x-4">
                     @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 dark:focus:ring-offset-gray-800 transition-transform hover:scale-105">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-lime-600 dark:hover:text-lime-400">Log in</a>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 dark:focus:ring-offset-gray-800 transition-transform hover:scale-105">
                            Mulai
                        </a>
                    @endauth
                    <button @click="darkMode = !darkMode" class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-lime-500" aria-label="Toggle dark mode">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="darkMode = !darkMode" class="p-2 mr-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-lime-500">
                        <span class="sr-only">Buka menu</span>
                        <svg x-show="!mobileMenuOpen" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="mobileMenuOpen" x-cloak x-transition class="sm:hidden">
            <div class="bg-white dark:bg-gray-800 pt-2 pb-3 space-y-1">
                <a href="#fitur" @click="mobileMenuOpen = false" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium" :class="activeSection === 'fitur' ? 'border-lime-500 bg-lime-50 text-lime-700 dark:bg-gray-700 dark:text-lime-300' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'">Fitur</a>
                <a href="#testimonials" @click="mobileMenuOpen = false" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium" :class="activeSection === 'testimonials' ? 'border-lime-500 bg-lime-50 text-lime-700 dark:bg-gray-700 dark:text-lime-300' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'">Testimoni</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                @auth
                    <div class="flex items-center px-4 mb-3">
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <a href="{{ route('logout') }}" @click.prevent="$root.submit()" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Log Out</a>
                        </form>
                    </div>
                @else
                    <div class="space-y-1">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Log in</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ========================== Main Content ========================== --}}
    <main class="pt-16">
        {{-- Hero Section --}}
        <section id="hero" class="relative isolate overflow-hidden gradient-bg">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-20 pb-24 sm:pt-28 sm:pb-32 lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-center">
                <div class="text-center lg:text-left">
                    {{-- [PERBAIKAN] Menggunakan :class dari Alpine.js untuk memastikan warna teks berubah --}}
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl fade-in"
                        :class="darkMode ? 'text-white' : 'text-slate-800'">
                        Kelola & Prediksi <span class="text-lime-600">Properti</span> Lebih Cerdas
                    </h1>
                    <p class="mt-6 text-lg leading-8 fade-in"
                        :class="darkMode ? 'text-gray-300' : 'text-slate-600'"
                        style="animation-delay: 0.2s;">
                        Nestora adalah platform properti digital yang dilengkapi model prediksi harga berbasis AI, memungkinkan transaksi langsung secara cepat, mandiri, dan transparan.
                    </p>
                    <div class="mt-10 flex items-center justify-center lg:justify-start gap-x-6 fade-in" style="animation-delay: 0.4s;">
                        <a href="{{ route('login') }}" class="rounded-md bg-lime-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-lime-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lime-600 transition-transform hover:scale-105">Mulai Sekarang</a>
                        <a href="#fitur" class="text-sm font-semibold leading-6 transition-colors"
                           :class="darkMode ? 'text-white hover:text-gray-300' : 'text-slate-700 hover:text-slate-500'">
                            Pelajari Fitur <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
                <div class="relative mt-16 h-80 lg:mt-0 lg:h-full fade-in" style="animation-delay: 0.1s;">
                    <div class="relative w-full h-full flex items-center justify-center hero-image-glow">
                        <img src="{{ asset('images/prediksi.png') }}" alt="Nestora Prediksi Properti" class="w-full max-w-lg lg:max-w-none mx-auto rounded-xl shadow-2xl ring-1 ring-gray-400/10" width="1200" height="675" loading="lazy">
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-white dark:from-gray-900 pointer-events-none" aria-hidden="true"></div>
        </section>


        {{-- Fitur Section --}}
        <section id="fitur" class="py-24 sm:py-32 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="max-w-2xl mx-auto lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-lime-600 dark:text-lime-400">Kenapa Nestora?</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Semua yang Anda Butuhkan untuk Properti</p>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                        Dari pengelolaan data hingga insight prediksi, Nestora menyediakan alat yang tepat untuk properti modern.
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        @php
                        $features = [
                            ['icon' => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125', 'title' => 'Manajemen Data Terpusat', 'description' => 'Kelola dan organisir semua data properti yang masuk dari aplikasi mobile pengguna dalam satu dashboard yang rapi dan mudah diakses.'],
                            ['icon' => 'M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z', 'title' => 'Prediksi Harga AI', 'description' => 'Dapatkan estimasi harga properti akurat berdasarkan data historis dan fitur properti menggunakan model AI yang terintegrasi.'],
                            ['icon' => 'M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V5.75A2.25 2.25 0 0 0 18 3.5H6A2.25 2.25 0 0 0 3.75 5.75v12.5A2.25 2.25 0 0 0 6 20.25Z', 'title' => 'API Terintegrasi', 'description' => 'Sediakan API endpoint untuk model prediksi yang dapat diakses langsung oleh aplikasi mobile pengguna Anda dengan mudah dan aman.'],
                        ];
                        @endphp
                        @foreach($features as $feature)
                        <div class="flex flex-col p-6 rounded-lg hover:shadow-xl hover:scale-105 transition-all duration-300 dark:hover:bg-gray-800">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                                <div class="flex h-12 w-12 flex-none items-center justify-center rounded-lg bg-lime-100 dark:bg-gray-700">
                                    <svg class="h-6 w-6 text-lime-600 dark:text-lime-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}" /></svg>
                                </div>
                                {{ $feature['title'] }}
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">{{ $feature['description'] }}</p>
                            </dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </section>

        {{-- Testimonials Section --}}
        <section id="testimonials" class="relative isolate overflow-hidden bg-white dark:bg-gray-900 px-6 py-24 sm:py-32 lg:px-8">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.indigo.100),white)] dark:bg-[radial-gradient(45rem_50rem_at_top,theme(colors.indigo.900/10),theme(colors.gray.900))] opacity-20"></div>
            <div class="absolute inset-y-0 right-1/2 -z-10 mr-16 w-[200%] origin-bottom-left skew-x-[-30deg] bg-white dark:bg-gray-800 shadow-xl shadow-lime-600/10 ring-1 ring-lime-50 dark:ring-lime-900/10 sm:mr-28 lg:mr-0 xl:mr-16 xl:origin-center"></div>
            <div class="mx-auto max-w-2xl lg:max-w-4xl">
                <img class="mx-auto h-12" src="https://tailwindui.com/img/logos/workcation-logo-gray-900.svg" alt="">
                <figure class="mt-10">
                    <blockquote class="text-center text-xl font-semibold leading-8 text-gray-900 dark:text-white sm:text-2xl sm:leading-9">
                        <p>“Nestora mengubah cara kami mengelola listing. Fitur prediksinya sangat akurat dan membantu kami menetapkan harga yang kompetitif. Sangat direkomendasikan untuk agen properti modern!”</p>
                    </blockquote>
                    <figcaption class="mt-10">
                        <img class="mx-auto h-10 w-10 rounded-full" src="{{ asset('images/gojo.jpeg') }}" alt="Gojo Satoru" />
                        <div class="mt-4 flex items-center justify-center space-x-3 text-base">
                            <div class="font-semibold text-gray-900 dark:text-white">Gojo Satoru</div>
                            <svg viewBox="0 0 2 2" width="3" height="3" aria-hidden="true" class="fill-gray-900 dark:fill-gray-400">
                                <circle cx="1" cy="1" r="1" />
                            </svg>
                            <div class="text-gray-600 dark:text-gray-400">術師 / Jujutsushi</div>
                        </div>
                    </figcaption>
                </figure>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="bg-white dark:bg-gray-800/50">
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:flex lg:items-center lg:justify-between lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Siap untuk Mengubah<br>Manajemen Properti Anda?
                </h2>
                <div class="mt-10 flex items-center gap-x-6 lg:mt-0 lg:flex-shrink-0">
                    <a href="{{ route('login') }}" class="rounded-md bg-lime-600 px-5 py-3 text-base font-semibold text-white shadow-lg hover:bg-lime-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lime-600 transition-transform hover:scale-105">Mulai Sekarang</a>
                    <a href="#fitur" class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Pelajari fitur <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </section>
    </main>

    {{-- ========================== Footer ========================== --}}
    <footer id="kontak" class="bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto py-12 px-6 lg:px-8 md:flex md:items-center md:justify-between">
            <div class="flex justify-center space-x-6 md:order-2">
                {{-- Social media icons here --}}
            </div>
            <div class="mt-8 md:mt-0 md:order-1">
                <p class="text-center text-base text-gray-500 dark:text-gray-400">&copy; {{ date('Y') }} Nestora. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Script animasi scroll --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
            const observerCallback = (entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-visible');
                        observer.unobserve(entry.target);
                    }
                });
            };
            const observer = new IntersectionObserver(observerCallback, observerOptions);
            const elementsToAnimate = document.querySelectorAll('#fitur dl > div, #testimonials figure, #hero h1, #hero p, #hero .flex');
            elementsToAnimate.forEach(el => {
                el.classList.add('fade-in-on-scroll');
                observer.observe(el);
            });
        });
    </script>
</body>
</html>