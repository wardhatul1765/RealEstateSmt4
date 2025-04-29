<x-app-layout>
    {{-- Header (Opsional, bisa dihapus jika tidak perlu judul khusus di sini) --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="space-y-6">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card 1: Jumlah Properti Terdaftar --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-5">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Jumlah Properti Terdaftar</h3>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">100</div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Unit</p>
            </div>

            {{-- Card 2: Jumlah Kunjungan Properti --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-5">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Jumlah Kunjungan Properti</h3>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">3000</div>
                 <p class="text-sm text-gray-500 dark:text-gray-400">Views</p>
            </div>

            {{-- Card 3: Properti Terjual Bulan Ini --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-5">
                 <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Properti Terjual Bulan Ini</h3>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">85</div>
                 <p class="text-sm text-gray-500 dark:text-gray-400">Unit</p>
            </div>

             {{-- Card 4: Agen Aktif --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-5">
                 <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Agen Aktif</h3>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">3</div>
                 <p class="text-sm text-gray-500 dark:text-gray-400">Agen</p>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Chart Penjualan Per-Bulan --}}
            <div class="bg-white dark:bg-white overflow-hidden shadow-md rounded-lg p-5">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-900 mb-4">Penjualan Per-Bulan</h3>
                {{-- Placeholder untuk chart --}}
                <div class="h-64 bg-gray-200 dark:bg-gray-300 rounded flex items-center justify-center">
                    <span class="text-gray-500">Chart Penjualan (Bar)</span>
                </div>
            </div>

            {{-- Container untuk dua chart bawah --}}
             <div class="space-y-6">
                 {{-- Chart Properti Terjual (Pie) --}}
                <div class="bg-white dark:bg-white overflow-hidden shadow-md rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-900 mb-4">Properti Terjual</h3>
                    {{-- Placeholder untuk chart --}}
                    <div class="h-48 bg-gray-200 dark:bg-gray-300 rounded flex items-center justify-center">
                        <span class="text-gray-500">Chart Properti (Pie)</span>
                    </div>
                </div>

                 {{-- Chart Tren Pengunjung Website (Line) --}}
                 <div class="bg-white dark:bg-white overflow-hidden shadow-md rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-900 mb-4">Tren Pengunjung Website</h3>
                    {{-- Placeholder untuk chart --}}
                    <div class="h-48 bg-gray-200 dark:bg-gray-300 rounded flex items-center justify-center">
                         <span class="text-gray-500">Chart Pengunjung (Line)</span>
                    </div>
                </div>
             </div>
        </div>
    </div>
</x-app-layout>