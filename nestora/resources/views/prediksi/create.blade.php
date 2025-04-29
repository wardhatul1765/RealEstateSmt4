<x-app-layout>
    {{-- Header Tetap Sama --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-black-300 leading-tight">
            {{ __('Prediksi Harga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8"> {{-- Max-width bisa disesuaikan --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                {{-- Padding utama untuk card --}}
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Judul dan Subjudul --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                            Prediksi Hasil Listing Properti
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Masukkan detail properti di bawah ini untuk mendapatkan prediksi.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('prediksi.store') }}">
                        @csrf

                        {{-- Gunakan Grid untuk Layout Input yang Berelasi --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Input Bathrooms --}}
                            <div>
                                <x-input-label for="bathrooms" :value="__('Jumlah Kamar Mandi')" class="mb-1" />
                                <x-text-input id="bathrooms"
                                    class="block w-full focus:border-nestora-lime focus:ring-nestora-lime dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                    type="number" step="0.1" {{-- Atau step="1" jika hanya bilangan bulat --}}
                                    name="bathrooms" :value="old('bathrooms')" required placeholder="Contoh: 2" />
                                <x-input-error :messages="$errors->get('bathrooms')" class="mt-2" />
                            </div>

                            {{-- Input Bedrooms --}}
                            <div>
                                <x-input-label for="bedrooms" :value="__('Jumlah Kamar Tidur')" class="mb-1" />
                                <x-text-input id="bedrooms"
                                    class="block w-full focus:border-nestora-lime focus:ring-nestora-lime dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                    type="number" step="0.1" {{-- Atau step="1" jika hanya bilangan bulat --}}
                                    name="bedrooms" :value="old('bedrooms')" required placeholder="Contoh: 3" />
                                <x-input-error :messages="$errors->get('bedrooms')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Input Furnishing (Ganti ke Select) --}}
                        <div class="mb-6">
                            <x-input-label for="furnishing" :value="__('Kondisi Furnishing')" class="mb-1" />
                            {{-- Menggunakan Select lebih user-friendly daripada angka 0/1 --}}
                            <select id="furnishing" name="furnishing" required
                                class="block w-full border-gray-300 focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                <option value="" disabled {{ old('furnishing') == '' ? 'selected' : '' }}>Pilih Kondisi...
                                </option>
                                <option value="0" {{ old('furnishing') == '0' ? 'selected' : '' }}>Unfurnished (Kosong)
                                </option>
                                <option value="1" {{ old('furnishing') == '1' ? 'selected' : '' }}>Furnished (Terisi)
                                </option>
                                {{-- Tambahkan '2' jika ada opsi Semi-Furnished, sesuaikan validasi di Controller --}}
                                {{-- <option value="2" {{ old('furnishing')=='2' ? 'selected' : '' }}>Semi-Furnished
                                </option> --}}
                            </select>
                            <x-input-error :messages="$errors->get('furnishing')" class="mt-2" />
                        </div>


                        {{-- Input SizeMin --}}
                        <div class="mb-8"> {{-- Beri margin bawah lebih sebelum tombol --}}
                            <x-input-label for="sizeMin" :value="__('Ukuran Minimal Properti (m²)')" class="mb-1" />
                            <x-text-input id="sizeMin"
                                class="block w-full focus:border-nestora-lime focus:ring-nestora-lime dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                type="number" name="sizeMin" :value="old('sizeMin')" required
                                placeholder="Contoh: 50" />
                            <x-input-error :messages="$errors->get('sizeMin')" class="mt-2" />
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex items-center justify-center mt-6"> {{-- Tombol di tengah --}}
                            {{-- Tombol utama dengan warna tema --}}
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

                    {{-- Hasil Prediksi atau Error (dengan styling lebih baik) --}}
                    <div class="mt-8 space-y-4">
                        @if(session('prediction_result'))
                            {{-- Hasil Sukses --}}
                            <div
                                class="flex items-start p-4 bg-green-50 dark:bg-green-900/30 border border-green-300 dark:border-green-600 text-green-800 dark:text-green-200 rounded-lg">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-green-500 dark:text-green-400"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <span class="font-semibold">Hasil Prediksi:</span> {{ session('prediction_result') }}
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            {{-- Pesan Error --}}
                            <div
                                class="flex items-start p-4 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-600 text-red-800 dark:text-red-200 rounded-lg">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-red-500 dark:text-red-400"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1.293-7.707a1 1 0 011.414 0L10 11.586l.293-.293a1 1 0 011.414 1.414L11.414 13l.293.293a1 1 0 01-1.414 1.414L10 14.414l-.293.293a1 1 0 01-1.414-1.414L8.586 13l-.293-.293a1 1 0 010-1.414zm1.707-4.707a1 1 0 00-1.414 0L9 6.586l-.293-.293a1 1 0 10-1.414 1.414L7.586 8l-.293.293a1 1 0 001.414 1.414L9 8.414l.293.293a1 1 0 001.414-1.414L10.414 8l.293-.293a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <span class="font-semibold">Terjadi Kesalahan:</span> {{ session('error') }}
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>