{{-- Menggunakan layout komponen x-app-layout --}}
<x-app-layout>
    {{-- Mengatur header (jika layout Anda mendukung slot 'header') --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Prediksi Harga (MongoDB)') }}
        </h2>
    </x-slot>

    {{-- Konten utama halaman diletakkan langsung di sini (sebagai $slot default) --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> {{-- Sesuaikan max-width jika perlu --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Riwayat Prediksi Harga Properti (MongoDB)</h1>

                    {{-- Tempat untuk menampilkan pesan error dari controller jika ada --}}
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Tabel untuk menampilkan data riwayat --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                {{-- Header tabel --}}
                                <tr class="bg-gray-100 dark:bg-gray-700 text-left text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6">No</th>
                                    <th class="py-3 px-6">Tanggal Prediksi</th>
                                    <th class="py-3 px-6">Input Fitur (Ringkasan)</th>
                                    <th class="py-3 px-6 text-right">Hasil Prediksi (IDR)</th>
                                    <th class="py-3 px-6 text-right">Hasil Prediksi (AED)</th>
                                    <th class="py-3 px-6">Diprediksi Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 dark:text-gray-200 text-sm">
                                {{-- Melakukan loop pada data riwayat prediksi ($riwayat_prediksi adalah cursor/collection dari MongoDB) --}}
                                @forelse ($riwayat_prediksi as $prediksi)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    {{-- Penomoran baris menggunakan $loop variable dari Blade --}}
                                    <td class="py-3 px-6">{{ $loop->iteration }}</td>

                                    {{-- Menampilkan tanggal dibuatnya record (dari MongoDB) --}}
                                    <td class="py-3 px-6">
                                        @if(isset($prediksi->created_at))
                                            @php
                                                try {
                                                    // created_at adalah string, jadi langsung parse dengan Carbon
                                                    echo \Carbon\Carbon::parse($prediksi->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i');
                                                } catch (\Exception $e) {
                                                    echo 'Invalid Date'; // Tampilkan pesan jika format tidak dikenali
                                                }
                                            @endphp
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    {{-- Menampilkan ringkasan fitur input (akses dengan notasi objek ->) --}}
                                    <td class="py-3 px-6">
                                        Bath: {{ $prediksi->bathrooms ?? 'N/A' }},
                                        Bed: {{ $prediksi->bedrooms ?? 'N/A' }},
                                        Size: {{ $prediksi->sizeMin ?? 'N/A' }} m²,
                                        {{-- Sesuaikan perbandingan untuk string '1' atau '0' --}}
                                        Furn: {{ isset($prediksi->furnishing) && $prediksi->furnishing == '1' ? 'Yes' : 'No' }},
                                        Verif: {{ isset($prediksi->verified) && $prediksi->verified == '1' ? 'True' : 'False' }} {{-- Asumsi '1' itu True, '0' itu False --}}
                                        {{-- Contoh Tambahan jika field ada di data Anda: --}}
                                        {{-- , View: {{ $prediksi->view_type ?? 'N/A' }}, Age: {{ $prediksi->listing_age_category ?? 'N/A' }}, Key: {{ $prediksi->title_keyword ?? 'N/A' }} --}}
                                    </td>

                                    {{-- Menampilkan hasil prediksi IDR dengan Prefix "Rp." --}}
                                    <td class="py-3 px-6 text-right">
                                        Rp. {{ number_format($prediksi->hasil_prediksi_idr ?? 0, 0, ',', '.') }}
                                    </td>

                                    {{-- Menampilkan hasil prediksi AED dengan Prefix "AED" --}}
                                    <td class="py-3 px-6 text-right">
                                        AED {{ number_format($prediksi->hasil_prediksi_aed ?? 0, 2, ',', '.') }}
                                    </td>

                                    {{-- KOLOM DIPREDIKSI OLEH (DISESUAIKAN DENGAN STRUKTUR ANDA) --}}
                                    <td class="py-3 px-6">
                                        @if(isset($prediksi->source))
                                            @if($prediksi->source == 'web' && isset($prediksi->admin_name))
                                                Admin ({{ $prediksi->admin_name }})
                                            @elseif(($prediksi->source == 'mobile' || $prediksi->source == 'user') && isset($prediksi->user_name)) {{-- Ganti 'user_name' jika field untuk nama user berbeda --}}
                                                User ({{ $prediksi->user_name }})
                                            @elseif($prediksi->source == 'web')
                                                Admin (Nama tidak tersedia)
                                            @elseif($prediksi->source == 'mobile' || $prediksi->source == 'user')
                                                User (Nama tidak tersedia)
                                            @else
                                                Sumber: {{ ucfirst($prediksi->source) }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                {{-- Pesan jika tidak ada data riwayat --}}
                                <tr>
                                    <td colspan="6" class="text-center py-4 px-6 text-gray-500 dark:text-gray-400">
                                        Belum ada riwayat prediksi yang tersimpan di MongoDB.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>