<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Prediksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- Hapus 'max-w-4xl' dan 'mx-auto' agar mengisi lebar, tapi pertahankan padding --}}
        <div class="sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Judul dan Subjudul --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                            Prediksi Hasil Listing Properti
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Masukkan detail properti di bawah ini untuk mendapatkan prediksi berdasarkan model.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('prediksi.store') }}">
                        @csrf

                        {{-- Grid untuk layout input --}}
                        {{-- Pertimbangkan untuk menambah kolom jika layar lebar, misal md:grid-cols-3 atau 4 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6"> {{-- Contoh: Tambah lg:grid-cols-3 --}}
                            {{-- Input Bathrooms --}}
                            <div>
                                <x-input-label for="bathrooms" :value="__('Jumlah Kamar Mandi')" class="mb-1" />
                                <x-text-input id="bathrooms"
                                    class="block w-full focus:border-nestora-lime focus:ring-nestora-lime dark:bg-gray-700 dark:border-gray-600"
                                    type="number" step="1" name="bathrooms"
                                    :value="old('bathrooms')" required placeholder="Contoh: 2" />
                                <x-input-error :messages="$errors->get('bathrooms')" class="mt-2" />
                            </div>

                            {{-- Input Bedrooms --}}
                            <div>
                                <x-input-label for="bedrooms" :value="__('Jumlah Kamar Tidur')" class="mb-1" />
                                <x-text-input id="bedrooms"
                                    class="block w-full focus:border-nestora-lime focus:ring-nestora-lime dark:bg-gray-700 dark:border-gray-600"
                                    type="number" step="1" name="bedrooms"
                                    :value="old('bedrooms')" required placeholder="Contoh: 3" />
                                <x-input-error :messages="$errors->get('bedrooms')" class="mt-2" />
                            </div>

                            {{-- Input Furnishing (Select) --}}
                            <div>
                                <x-input-label for="furnishing" :value="__('Kondisi Furnishing')" class="mb-1" />
                                <select id="furnishing" name="furnishing" required
                                    class="block w-full border-gray-300 focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    <option value="" disabled {{ old('furnishing') === null ? 'selected' : '' }}>Pilih Kondisi...</option>
                                    <option value="0" {{ old('furnishing') == '0' ? 'selected' : '' }}>Unfurnished (0)</option>
                                    <option value="1" {{ old('furnishing') == '1' ? 'selected' : '' }}>Furnished (1)</option>
                                </select>
                                <x-input-error :messages="$errors->get('furnishing')" class="mt-2" />
                            </div>

                            {{-- Input Verified (Select) --}}
                            <div>
                                <x-input-label for="verified" :value="__('Terverifikasi')" class="mb-1" />
                                <select id="verified" name="verified" required
                                    class="block w-full border-gray-300 focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    <option value="" disabled {{ old('verified') === null ? 'selected' : '' }}>Pilih Status...</option>
                                    <option value="0" {{ old('verified') == '0' ? 'selected' : '' }}>Tidak (0)</option>
                                    <option value="1" {{ old('verified') == '1' ? 'selected' : '' }}>Ya (1)</option>
                                </select>
                                <x-input-error :messages="$errors->get('verified')" class="mt-2" />
                            </div>

                            {{-- Input SizeMin --}}
                            <div>
                                <x-input-label for="sizeMin" :value="__('Ukuran Minimal (m²)')" class="mb-1" />
                                <x-text-input id="sizeMin"
                                    class="block w-full focus:border-nestora-lime focus:ring-nestora-lime dark:bg-gray-700 dark:border-gray-600"
                                    type="number" name="sizeMin" :value="old('sizeMin')" required
                                    placeholder="Contoh: 50" />
                                <x-input-error :messages="$errors->get('sizeMin')" class="mt-2" />
                            </div>

                            {{-- Input View Type (Select) --}}
                            <div>
                                <x-input-label for="view_type" :value="__('Pemandangan Utama:')" class="mb-1" />
                                <select id="view_type" name="view_type" required
                                    class="block w-full border-gray-300 focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    <option value="" disabled {{ old('view_type') === null ? 'selected' : '' }}>Pilih Tipe...</option>
                                    <option value="1" {{ old('view_type') == '1' ? 'selected' : '' }}>Sea View (1)</option>
                                    <option value="2" {{ old('view_type') == '2' ? 'selected' : '' }}>Burj Khalifa View (2)</option>
                                    <option value="3" {{ old('view_type') == '3' ? 'selected' : '' }}>Golf Course View (3)</option>
                                    <option value="4" {{ old('view_type') == '4' ? 'selected' : '' }}>Community View (4)</option>
                                    <option value="5" {{ old('view_type') == '5' ? 'selected' : '' }}>City View (5)</option>
                                    <option value="6" {{ old('view_type') == '6' ? 'selected' : '' }}>Lake View (6)</option>
                                    <option value="7" {{ old('view_type') == '7' ? 'selected' : '' }}>Pool View (7)</option>
                                    <option value="8" {{ old('view_type') == '8' ? 'selected' : '' }}>Canal View (8)</option>
                                    <option value="0" {{ old('view_type') == '0' ? 'selected' : '' }}>Lainnya / Tidak Ada (0)</option>
                                </select>
                                <x-input-error :messages="$errors->get('view_type')" class="mt-2" />
                            </div>

                           {{-- Input Listing Age Category (Dropdown) --}}
                            <div>
                                <x-input-label for="listing_age_category" :value="__('Kategori Usia Listing')" class="mb-1" />
                                <select id="listing_age_category" name="listing_age_category" required
                                        class="block w-full border-gray-300 focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    <option value="" disabled {{ old('listing_age_category') === null ? 'selected' : '' }}>Pilih Kategori Usia...</option>
                                    {{--
                                        Sesuaikan 'value' di bawah ini (0, 1, 2)
                                        agar cocok dengan encoding numerik yang diharapkan backend Anda
                                        untuk 'baru', 'cukup_lama', dan 'lama'.
                                    --}}
                                    <option value="0" {{ old('listing_age_category') == '0' ? 'selected' : '' }}>Baru (Kurang dari 3 bulan)</option>
                                    <option value="1" {{ old('listing_age_category') == '1' ? 'selected' : '' }}>Cukup Lama (3-6 bulan)</option>
                                    <option value="2" {{ old('listing_age_category') == '2' ? 'selected' : '' }}>Lama (Lebih dari 6 bulan)</option>
                                </select>
                                <x-input-error :messages="$errors->get('listing_age_category')" class="mt-2" />
                            </div>

                            {{-- Input Title Keyword (Select) --}}
                            <div>
                                <x-input-label for="title_keyword" :value="__('Label Properti / Tag Properti:')" class="mb-1" />
                                <select id="title_keyword" name="title_keyword" required
                                    class="block w-full border-gray-300 focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    <option value="" disabled {{ old('title_keyword') === null ? 'selected' : '' }}>Pilih Kata Kunci...</option>
                                    <option value="0" {{ old('title_keyword') == '0' ? 'selected' : '' }}>Tidak Ada Keyword Spesifik (0)</option>
                                    <option value="1" {{ old('title_keyword') == '1' ? 'selected' : '' }}>Luxury (1)</option>
                                    <option value="2" {{ old('title_keyword') == '2' ? 'selected' : '' }}>Furnished (2)</option>
                                    <option value="3" {{ old('title_keyword') == '3' ? 'selected' : '' }}>Spacious (3)</option>
                                    <option value="4" {{ old('title_keyword') == '4' ? 'selected' : '' }}>Prime (4)</option>
                                    <option value="5" {{ old('title_keyword') == '5' ? 'selected' : '' }}>Studio (5)</option>
                                    <option value="6" {{ old('title_keyword') == '6' ? 'selected' : '' }}>Penthouse (6)</option>
                                    <option value="7" {{ old('title_keyword') == '7' ? 'selected' : '' }}>Investment (7)</option>
                                    <option value="8" {{ old('title_keyword') == '8' ? 'selected' : '' }}>Villa (8)</option>
                                    <option value="9" {{ old('title_keyword') == '9' ? 'selected' : '' }}>Downtown (9)</option>
                                </select>
                                <x-input-error :messages="$errors->get('title_keyword')" class="mt-2" />
                            </div>

                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex items-center justify-end mt-8"> {{-- Rata kanan --}}
                            <x-primary-button
                                class="px-6 py-3 text-base bg-nestora-lime hover:bg-lime-500 focus:bg-lime-500 active:bg-lime-600 text-gray-800 font-semibold focus:ring-lime-400 dark:text-gray-900">
                                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                                </svg>
                                {{ __('Prediksi Sekarang') }}
                            </x-primary-button>
                        </div>
                    </form>

                    {{-- Hasil Prediksi atau Error --}}
                    <div class="mt-8 space-y-4">
                        {{-- Bagian Tampilan Hasil Prediksi --}}
                        @if(session()->has('prediction_result_aed'))
                            <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-300 dark:border-green-600 rounded-lg">
                                <div class="flex items-center mb-3">
                                     <svg class="w-6 h-6 mr-3 flex-shrink-0 text-green-500 dark:text-green-400"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">Prediksi Harga Properti Berhasil</h3>
                                </div>
                                <div class="pl-9 space-y-2"> {{-- Indentasi agar sejajar dengan teks judul --}}
                                    {{-- Tampilkan nilai AED --}}
                                    <div>
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Estimasi Harga (AED):</span>
                                        <span class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                            AED {{ number_format(session('prediction_result_aed'), 2, ',', '.') }}
                                        </span>
                                    </div>
                                    {{-- Tampilkan nilai IDR (jika ada) --}}
                                    @if(session()->has('prediction_result_idr'))
                                        <div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Estimasi Harga (IDR):</span>
                                            <span class="text-xl font-semibold text-gray-700 dark:text-gray-200">
                                                IDR {{ number_format(session('prediction_result_idr'), 0, ',', '.') }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 italic">(perkiraan)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Pesan Error (tetap sama) --}}
                        @if(session('error'))
                            <div
                                class="flex items-start p-4 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-600 text-red-800 dark:text-red-200 rounded-lg">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-red-500 dark:text-red-400"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1.293-7.707a1 1 0 011.414 0L10 11.586l.293-.293a1 1 0 011.414 1.414L11.414 13l.293.293a1 1 0 01-1.414 1.414L10 14.414l-.293.293a1 1 0 01-1.414-1.414L8.586 13l-.293-.293a1 1 0 010-1.414zm1.707-4.707a1 1 0 00-1.414 0L9 6.586l-.293-.293a1 1 0 10-1.414 1.414L7.586 8l-.293.293a1 1 0 001.414 1.414L9 8.414l.293.293a1 1 0 001.414-1.414L10.414 8l.293-.293a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div><span class="font-semibold">Terjadi Kesalahan:</span> {{ session('error') }}</div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
