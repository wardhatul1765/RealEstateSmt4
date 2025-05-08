{{-- Sidebar --}}
{{-- State sidebar (isSidebarOpen) dikelola di app.blade.php --}}
<aside x-show="isSidebarOpen"
       x-transition:enter="transition ease-in-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       {{-- Warna disesuaikan dengan sidebar baru --}}
       class="w-64 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen p-4 fixed border-r border-gray-200 dark:border-gray-700 overflow-y-auto md:translate-x-0 z-30 transition-transform duration-300 ease-in-out">

       {{-- Logo Section --}}
       <div class="mb-6 pt-2 pb-4 border-b border-gray-200 dark:border-gray-700">
           <a href="{{ route('dashboard') }}" class="flex items-center justify-center md:justify-start">
               <div class="flex items-center justify-center h-8 w-8 bg-lime-500 rounded mr-2 flex-shrink-0">
                   <span class="text-white font-bold text-lg">N</span>
               </div>
               <span class="text-gray-800 dark:text-gray-100 text-xl font-bold hidden md:inline">Nestora</span>
           </a>
       </div>

    {{-- Bungkus Navigasi dengan div yang memiliki x-data untuk submenu --}}
    <div x-data="{
            dataMasterOpen: {{ request()->routeIs('data-master.*') ? 'true' : 'false' }},
            dataUserOpen: {{ request()->routeIs('data-user.*') ? 'true' : 'false' }},
            manajemenPropertiOpen: {{ request()->routeIs('manajemen-properti.*') ? 'true' : 'false' }},
            prediksiOpen: {{ request()->routeIs('prediksi.*') ? 'true' : 'false' }}
         }">
        <nav>
            <ul>
                {{-- Dashboard Link --}}
                <li class="mb-2">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-3 py-2 rounded-md transition duration-150 ease-in-out focus:outline-none {{ request()->routeIs('dashboard')
                            ? 'bg-nestora-lime text-gray-800 font-semibold'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                        <svg class="h-5 w-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 018.25 20.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                        <span class="hidden md:inline">Dashboard</span>
                    </a>
                </li>

                {{-- Manajemen Properti Link (Collapsible) --}}
                {{-- DIPINDAHKAN KE ATAS DATA MASTER --}}
                <li class="mb-2">
                    <button @click="manajemenPropertiOpen = !manajemenPropertiOpen"
                            class="w-full flex justify-between items-center px-3 py-2 rounded-md text-left transition duration-150 ease-in-out focus:outline-none {{ request()->routeIs('manajemen-properti.*') ? 'bg-nestora-lime text-gray-800 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                        <span class="flex items-center">
                            <svg class="h-5 w-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6.75h1.5m-1.5 3h1.5m-1.5 3h1.5M6.75 21v-2.25A2.25 2.25 0 019 16.5h6a2.25 2.25 0 012.25 2.25V21m-9 0h6" /></svg>
                            <span class="hidden md:inline">Manajemen Properti</span>
                        </span>
                        <svg class="h-5 w-5 transform transition-transform duration-150 hidden md:inline" :class="{'rotate-180': manajemenPropertiOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                    <ul x-show="manajemenPropertiOpen" x-transition class="mt-1 ml-4 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-2 hidden md:block">
                        <li><a href="{{ route('manajemen-properti.index') }}" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('manajemen-properti.index') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Daftar Properti</a></li>
                        <li><a href="{{ route('manajemen-properti.persetujuan') }}" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('manajemen-properti.persetujuan') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Persetujuan Iklan</a></li>
                        <!-- <li><a href="{{ route('manajemen-properti.create') }}" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('manajemen-properti.create') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Tambah Properti</a></li> -->
                    </ul>
                 </li>

                {{-- Data Master Link (Collapsible) --}}
                <li class="mb-2">
                    <button @click="dataMasterOpen = !dataMasterOpen"
                            class="w-full flex justify-between items-center px-3 py-2 rounded-md text-left transition duration-150 ease-in-out focus:outline-none {{ request()->routeIs('data-master.*')
                                ? 'bg-nestora-lime text-gray-800 font-semibold'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                        <span class="flex items-center">
                            <svg class="h-5 w-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                            <span class="hidden md:inline">Data Master</span>
                        </span>
                        <svg class="h-5 w-5 transform transition-transform duration-150 hidden md:inline" :class="{'rotate-180': dataMasterOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                    <ul x-show="dataMasterOpen" x-transition class="mt-1 ml-4 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-2 hidden md:block">
                        <li><a href="{{ route('data-master.properti.index') }}" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('data-master.properti.*') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Master Properti</a></li>
                        <!-- <li><a href="#" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('data-master.kategori.*') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Kategori Properti</a></li>
                        <li><a href="#" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('data-master.lokasi.*') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Lokasi</a></li>
                        <li><a href="#" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('data-master.fasilitas.*') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Fasilitas Properti</a></li>
                        <li><a href="#" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('data-master.status.*') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Status Properti</a></li> -->
                    </ul>
                </li>

                {{-- Data User Link (Collapsible) --}}
                 <li class="mb-2">
                     <button @click="dataUserOpen = !dataUserOpen"
                             class="w-full flex justify-between items-center px-3 py-2 rounded-md text-left transition duration-150 ease-in-out focus:outline-none {{ request()->routeIs('data-user.*') ? 'bg-nestora-lime text-gray-800 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                         <span class="flex items-center">
                             <svg class="h-5 w-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                             <span class="hidden md:inline">Data User</span>
                         </span>
                         <svg class="h-5 w-5 transform transition-transform duration-150 hidden md:inline" :class="{'rotate-180': dataUserOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                     </button>
                     <ul x-show="dataUserOpen" x-transition class="mt-1 ml-4 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-2 hidden md:block">
                         <li><a href="{{ route('data-user.index') }}" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('data-user.index') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}"> Data Pengguna</a></li>
                         {{-- Jika ada route lain di bawah data-user, tambahkan di sini --}}
                     </ul>
                 </li>

                 {{-- Menu Prediksi Link (Collapsible) --}}
                 <li class="mb-2">
                     <button @click="prediksiOpen = !prediksiOpen"
                             class="w-full flex justify-between items-center px-3 py-2 rounded-md text-left transition duration-150 ease-in-out focus:outline-none {{ request()->routeIs('prediksi.*') ? 'bg-nestora-lime text-gray-800 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                         <span class="flex items-center">
                             <svg class="h-5 w-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg>
                             <span class="hidden md:inline">Menu Prediksi</span>
                         </span>
                         <svg class="h-5 w-5 transform transition-transform duration-150 hidden md:inline" :class="{'rotate-180': prediksiOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                     </button>
                     <ul x-show="prediksiOpen" x-transition class="mt-1 ml-4 space-y-1 border-l-2 border-gray-200 dark:border-gray-700 pl-2 hidden md:block">
                         <li><a href="{{ route('prediksi.create') }}" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('prediksi.create') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Prediksi Harga</a></li>
                         <li><a href="{{ route('prediksi.history') }}" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('prediksi.history') ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700' }}">Riwayat Prediksi</a></li>
                         {{-- <li><a href="#" class="block px-3 py-1 rounded-md text-sm {{ request()->routeIs('prediksi.laporan') ? 'text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-200' }}">Laporan Prediksi</a></li> --}}
                     </ul>
                 </li>
            </ul>
        </nav>
    </div> {{-- Akhir div pembungkus navigasi --}}
</aside>