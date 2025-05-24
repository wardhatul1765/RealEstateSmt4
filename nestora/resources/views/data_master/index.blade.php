<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Properti') }}
        </h2>
    </x-slot>

    {{-- Lingkup Data Alpine.js untuk halaman ini --}}
    <div class="py-6" x-data="masterProperti()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Notifikasi Sukses/Error dari Session --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                         class="mb-4 p-4 bg-green-500 text-white rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                         class="mb-4 p-4 bg-red-500 text-white rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Notifikasi dari Alpine.js --}}
                <div x-show="predictionError && predictionError.length > 0" class="mb-4 p-4 bg-yellow-500 text-black rounded-lg" role="alert" x-cloak>
                    <span class="font-bold">Info Prediksi:</span> <span x-text="predictionError"></span>
                </div>
                <div x-show="deleteError && deleteError.length > 0" class="mb-4 p-4 bg-red-700 text-white rounded-md" role="alert" x-cloak>
                    <span class="font-bold">Error Hapus:</span> <span x-text="deleteError"></span>
                </div>

                {{-- Baris Pencarian dan Tombol Tambah --}}
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <form action="{{ route('data-master.properti.index') }}" method="GET" class="flex-grow sm:flex-grow-0 w-full sm:w-auto">
                        <div class="flex">
                            <input type="text" name="search" placeholder="Cari judul atau alamat..."
                                   value="{{ request('search') }}"
                                   class="px-4 py-2 rounded-l-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600 w-full">
                            <button type="submit"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700 transition whitespace-nowrap">
                                Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('data-master.properti.index') }}"
                                   class="ml-2 px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition whitespace-nowrap">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                    <button @click="openModal('add')"
                            class="w-full sm:w-auto px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition whitespace-nowrap">
                        + Tambah Properti
                    </button>
                </div>

                {{-- Tabel Data Properti --}}
                <div class="overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Alamat</th> {{-- Perhatikan 'address' di sini --}}
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">KT</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">KM</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Harga (AED)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Luas (sqft)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Perabotan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">View</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Label</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Ditambahkan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($dataProperty as $index => $property)
                                <tr :key="'prop-' + '{{ $property->id }}'" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $dataProperty->firstItem() + $index }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ Str::limit($property->title, 25) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ Str::limit($property->Address, 30) }}</td> {{-- Data 'address' ditampilkan di sini --}}
                                    <td class="px-4 py-2 text-center whitespace-nowrap">{{ $property->bedrooms }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">{{ $property->bathrooms }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $property->furnishing }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ Str::limit($property->propertyType, 20) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ Str::limit($property->mainView, 20) ?? '-' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ Str::limit($property->propertyLabel, 20) ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">
                                        @if($property->status)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">Verified</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">Not Verified</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $property->addedOn ? \Carbon\Carbon::parse($property->addedOn)->isoFormat('D MMM YYYY') : ($property->created_at ? \Carbon\Carbon::parse($property->created_at)->isoFormat('D MMM YYYY') : '-') }}</td>
                                    <td class="px-4 py-2 flex space-x-2 whitespace-nowrap">
                                        <button @click="openModal('edit', '{{ $property->id }}')"
                                                class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                            Edit
                                        </button>
                                        <button @click="openDeleteModal('{{ route('data-master.properti.destroy', $property->id) }}', '{{ $property->id }}', '{{ addslashes(htmlspecialchars($property->title, ENT_QUOTES)) }}')"
                                                type="button"
                                                class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Tidak ada data properti yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="mt-6 text-gray-900 dark:text-white">
                    {{ $dataProperty->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        {{-- Komponen Modal Tambah/Edit Properti --}}
        <x-modal name="propertyFormModal" :maxWidth="'2xl'"> {{-- Dibuat sedikit lebih lebar --}}
            <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <h2 class="text-lg font-medium mb-4" x-text="modalTitle"></h2>

                {{-- Error Validasi AJAX --}}
                <div x-show="Object.keys(ajaxErrors).length > 0 && !ajaxErrors.delete" class="mb-4 p-3 bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-md" x-cloak>
                    <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                    <ul class="list-disc list-inside mt-1 text-sm">
                        <template x-for="(errorMessages, field) in ajaxErrors" :key="field">
                            <template x-if="field !== 'delete' && field !== 'general'">
                                <template x-for="message in errorMessages" :key="message">
                                    <li x-text="message"></li>
                                </template>
                            </template>
                        </template>
                        <template x-if="ajaxErrors.general">
                             <template x-for="message in ajaxErrors.general" :key="message">
                                <li x-text="message"></li>
                            </template>
                        </template>
                    </ul>
                </div>

                <form @submit.prevent="submitForm" id="propertyForm" method="POST" action="#">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="addedOn" x-model="formData.addedOn">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">  {{-- Grid 3 kolom untuk layar besar --}}
                        {{-- Judul --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
                            <input type="text" name="title" id="title" x-model="formData.title"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                        </div>
                        {{-- Alamat --}}
                        <div class="md:col-span-2 lg:col-span-2"> {{-- Alamat dibuat lebih lebar --}}
                            <label for="Address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                            <input type="text" name="Address" id="Address" x-model="formData.Address" {{-- Pastikan x-model="formData.address" --}}
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                        </div>
                        {{-- Kamar Tidur --}}
                        <div>
                            <label for="bedrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kamar Tidur</label>
                            <input type="number" name="bedrooms" id="bedrooms" x-model="formData.bedrooms" min="0"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                        </div>
                        {{-- Kamar Mandi --}}
                        <div>
                            <label for="bathrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kamar Mandi</label>
                            <input type="number" name="bathrooms" id="bathrooms" x-model="formData.bathrooms" min="0"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                        </div>
                         {{-- Luas --}}
                        <div>
                            <label for="sizeMin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Luas (sqft)</label>
                            <input type="number" name="sizeMin" id="sizeMin" x-model="formData.sizeMin" min="0"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                        </div>
                        {{-- Perabotan --}}
                        <div>
                            <label for="furnishing" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perabotan</label>
                            <select name="furnishing" id="furnishing" x-model="formData.furnishing"
                                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                                <option value="">Pilih...</option>
                                <option value="Yes">Ya (Yes)</option>
                                <option value="No">Tidak (No)</option>
                                <option value="Partly">Sebagian (Partly)</option>
                            </select>
                        </div>
                        {{-- Pemandangan Utama --}}
                        <div>
                            <label for="mainView" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pemandangan Utama</label>
                            <select name="mainView" id="mainView" x-model="formData.mainView"
                                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                                <option value="">Pilih Tipe Pemandangan...</option>
                                <template x-for="option in viewTypeOptions" :key="option.value">
                                    <option :value="option.value" x-text="option.text"></option>
                                </template>
                            </select>
                        </div>
                        {{-- Label Properti --}}
                        <div>
                            <label for="propertyLabel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Label Properti</label>
                            <select name="propertyLabel" id="propertyLabel" x-model="formData.propertyLabel"
                                     class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                                <option value="">Pilih Label Properti...</option>
                                <template x-for="option in keywordOptions" :key="option.value">
                                    <option :value="option.value" x-text="option.text"></option>
                                </template>
                            </select>
                        </div>
                        {{-- Harga --}}
                        <div class="lg:col-span-2"> {{-- Harga dibuat lebih lebar di LG --}}
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga (AED)</label>
                            <div class="flex items-center space-x-4 my-1">
                                <label class="flex items-center text-sm">
                                    <input type="radio" id="price_manual" value="manual" x-model="formData.price_mode" name="price_mode_option" class="text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
                                    <span class="ms-2">Manual</span>
                                </label>
                                <label class="flex items-center text-sm">
                                    <input type="radio" id="price_predict" value="predict" x-model="formData.price_mode" name="price_mode_option" class="text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
                                    <span class="ms-2">Gunakan Prediksi</span>
                                </label>
                            </div>
                            <div class="flex">
                                <input type="number" name="price" id="price" x-model="formData.price" min="0"
                                       :disabled="formData.price_mode === 'predict'"
                                       class="block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-l-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white"
                                       :class="{'bg-gray-200 dark:bg-gray-600 cursor-not-allowed': formData.price_mode === 'predict'}">
                                <button type="button" @click="getPredictedPrice" x-show="formData.price_mode === 'predict'"
                                        class="px-3 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 transition text-sm whitespace-nowrap"
                                        :disabled="isPredicting">
                                    <span x-show="!isPredicting">Dapatkan Prediksi</span>
                                    <span x-show="isPredicting">Memprediksi...</span>
                                </button>
                            </div>
                        </div>
                         {{-- Tipe Properti --}}
                        <!-- <div>
                            <label for="propertyType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Properti</label>
                            <input type="text" name="propertyType" id="propertyType" x-model="formData.propertyType"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white">
                        </div> -->
                        {{-- Status Terverifikasi --}}
                        <div class="flex items-end pb-2"> {{-- Agar sejajar dengan input lain --}}
                            <label for="status" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="status" id="status" value="1" x-model="formData.status"
                                       class="rounded border-gray-300 dark:border-gray-600 text-purple-600 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
                                <span class="ms-2">Terverifikasi</span>
                            </label>
                        </div>
                    </div>

                    {{-- Tombol Aksi Modal --}}
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-indigo-500">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-purple-500"
                                x-text="submitButtonText"
                                :disabled="isSubmitting">
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Modal Konfirmasi Hapus --}}
        <x-modal name="deleteConfirmModal" :maxWidth="'md'">
            <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <h2 class="text-lg font-medium mb-2">Konfirmasi Hapus</h2>
                <p class="mb-1 text-sm text-gray-600 dark:text-gray-300">Anda yakin ingin menghapus properti:</p>
                <p class="mb-6 font-semibold" x-text="propertyToDeleteName"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat diurungkan.</p>

                <div x-show="ajaxErrors.delete" class="mt-4 mb-4 p-3 bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-md" x-cloak>
                         <p x-text="ajaxErrors.delete"></p>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="closeDeleteModal()" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-indigo-500">
                        Batal
                    </button>
                    <button @click="confirmDelete()" type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-red-500"
                            :disabled="isDeleting">
                        <span x-show="!isDeleting">Hapus Properti</span>
                        <span x-show="isDeleting">Menghapus...</span>
                    </button>
                </div>
            </div>
        </x-modal>

    </div>

@push('scripts')
<script>
    function masterProperti() {
        return {
            modalTitle: 'Form Properti',
            submitButtonText: 'Simpan',
            formAction: '',
            isSubmitting: false,
            isPredicting: false,
            ajaxErrors: {},
            predictionError: '',
            deleteError: '',
            currentPropertyId: null,

            propertyToDeleteId: null,
            propertyToDeleteUrl: '',
            propertyToDeleteName: '',
            isDeleting: false,

            viewTypeMapForPrediction: {
                '': null, 'sea view': 0, 'burj khalifa view': 1, 'golf course view': 2,
                'community view': 3, 'city view': 4, 'lake view': 5, 'pool view': 6, 'canal view': 7
            },
            keywordMapForPrediction: {
                '': null, 'luxury': 0, 'furnished': 1, 'spacious': 2, 'prime': 3,
                'studio': 4, 'penthouse': 5, 'investment': 6, 'villa': 7, 'downtown': 8
            },
            furnishingMapForPrediction: {
                '': null, 'Yes': 0, 'No': 1, 'Partly': 2
            },

            keywordOptions: [
                { value: 'luxury', text: 'Mewah (Luxury)' }, { value: 'furnished', text: 'Berperabot (Furnished)' },
                { value: 'spacious', text: 'Luas (Spacious)' }, { value: 'prime', text: 'Utama (Prime)' },
                { value: 'studio', text: 'Studio' }, { value: 'penthouse', text: 'Penthouse' },
                { value: 'investment', text: 'Investasi (Investment)' }, { value: 'villa', text: 'Vila' },
                { value: 'downtown', text: 'Pusat Kota (Downtown)' }
            ],
            viewTypeOptions: [
                { value: 'sea view', text: 'Pemandangan Laut (Sea View)' }, { value: 'burj khalifa view', text: 'Pemandangan Burj Khalifa' },
                { value: 'golf course view', text: 'Pemandangan Lapangan Golf' }, { value: 'community view', text: 'Pemandangan Komunitas' },
                { value: 'city view', text: 'Pemandangan Kota (City View)' }, { value: 'lake view', text: 'Pemandangan Danau (Lake View)' },
                { value: 'pool view', text: 'Pemandangan Kolam Renang (Pool View)' }, { value: 'canal view', text: 'Pemandangan Kanal (Canal View)' }
            ],

            formData: {
                title: '',
                Address: '', // Pastikan ini 'address'
                bedrooms: '',
                bathrooms: '',
                price: '',
                sizeMin: '',
                furnishing: '',
                propertyType: 'Residential for Sale',
                status: true, // Checkbox "Terverifikasi"
                mainView: '',
                propertyLabel: '',
                addedOn: '',
                price_mode: 'manual'
            },

            init() {
                // Ambil CSRF token untuk operasi AJAX non-form (seperti delete)
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('#propertyForm input[name="_token"]')?.value;

                const serverDeleteError = '{{ session("error_delete") }}';
                if (serverDeleteError) {
                    this.deleteError = serverDeleteError;
                    setTimeout(() => this.deleteError = '', 5000);
                }

                const fieldsToWatchForPrediction = ['bedrooms', 'bathrooms', 'furnishing', 'sizeMin', 'mainView', 'propertyLabel', 'status'];
                fieldsToWatchForPrediction.forEach(field => {
                    this.$watch(`formData.${field}`, (newValue, oldValue) => {
                        if (this.formData.price_mode === 'predict' && this.formData.price !== '' && !this.isPredicting) {
                            this.formData.price = '';
                            this.predictionError = 'Data input berubah. Klik "Dapatkan Prediksi" untuk harga terbaru.';
                        }
                    });
                });

                this.$watch('formData.price_mode', (newValue) => {
                    if (newValue === 'manual') {
                        this.predictionError = '';
                    } else if (newValue === 'predict') {
                        this.formData.price = '';
                        this.predictionError = 'Mode prediksi aktif. Isi/sesuaikan data dan klik "Dapatkan Prediksi".';
                    }
                });
            },

            getTodayDate() {
                const today = new Date();
                return `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            },

            openModal(mode, propertyId = null) {
                this.ajaxErrors = {};
                this.predictionError = '';
                this.currentPropertyId = propertyId;
                this.resetAlpineFormData(); // Panggil reset di sini

                if (mode === 'add') {
                    this.modalTitle = 'Tambah Properti Baru';
                    this.submitButtonText = 'Simpan';
                    this.formAction = '{{ route("data-master.properti.store") }}';
                    document.getElementById('formMethod').value = 'POST';
                    this.formData.status = true;
                    this.formData.propertyType = 'Residential for Sale';
                    this.formData.addedOn = this.getTodayDate();
                    this.formData.price_mode = 'manual';
                } else if (mode === 'edit' && propertyId) {
                    this.modalTitle = 'Edit Properti';
                    this.submitButtonText = 'Perbarui';
                    this.formAction = `{{ url('data-master/properti') }}/${propertyId}`;
                    document.getElementById('formMethod').value = 'PUT';
                    this.formData.price_mode = 'manual';
                    this.fetchPropertyData(propertyId);
                }
                this.$dispatch('open-modal', 'propertyFormModal');
            },

            closeModal() {
                this.$dispatch('close-modal', 'propertyFormModal');
                // Tidak perlu resetAlpineFormData() di sini karena sudah di openModal
                this.currentPropertyId = null;
                // Biarkan ajaxErrors dan predictionError agar bisa dilihat jika ada pesan setelah submit gagal lalu batal
            },

            resetAlpineFormData() {
                const today = this.getTodayDate();
                this.formData = {
                    title: '', Address: '', bedrooms: '', bathrooms: '', price: '', // 'address'
                    sizeMin: '', furnishing: '', propertyType: 'Residential for Sale',
                    status: true, mainView: '', propertyLabel: '',
                    addedOn: today, price_mode: 'manual'
                };
                 // Mungkin tidak perlu reset form manual jika Alpine sudah reaktif
                 // if (document.getElementById('propertyForm')) {
                 // document.getElementById('propertyForm').reset();
                 // }
            },

            async fetchPropertyData(id) {
                this.isSubmitting = true;
                try {
                    const url = `{{ route('data-master.properti.edit-data', ['id' => '_PLACEHOLDER_']) }}`.replace('_PLACEHOLDER_', id);
                    const response = await fetch(url);
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({ message: 'Gagal mengambil data properti.' }));
                        throw new Error(errorData.message || `Kesalahan HTTP! status: ${response.status}`);
                    }
                    const data = await response.json();

                    this.formData.title = data.title || '';
                    this.formData.Address = data.Address || ''; // Pastikan mengambil 'address'
                    this.formData.bedrooms = data.bedrooms || '';
                    this.formData.bathrooms = data.bathrooms || '';
                    this.formData.price = data.price || '';
                    this.formData.sizeMin = data.sizeMin || '';
                    this.formData.furnishing = data.furnishing || '';
                    this.formData.propertyType = data.propertyType || 'Residential for Sale';
                    this.formData.status = !!data.status; // Konversi ke boolean
                    this.formData.mainView = data.mainView || '';
                    this.formData.propertyLabel = data.propertyLabel || '';
                    this.formData.addedOn = data.addedOn ? new Date(data.addedOn).toISOString().split('T')[0] : this.getTodayDate();
                    this.formData.price_mode = 'manual';
                } catch (error) {
                    console.error('Kesalahan saat mengambil data properti:', error);
                    this.ajaxErrors = { general: [error.message || 'Tidak dapat memuat data properti.'] };
                } finally {
                    this.isSubmitting = false;
                }
            },

            async getPredictedPrice() {
                this.isPredicting = true;
                this.predictionError = '';
                this.ajaxErrors = {};

                const requiredFieldsMap = {
                    bathrooms: 'bathrooms', bedrooms: 'bedrooms', furnishing: 'furnishing',
                    sizeMin: 'sizeMin', verified: 'status', view_type: 'mainView', title_keyword: 'propertyLabel'
                };
                let dataForPrediction = { listing_age_category: 0 };
                let isValid = true;

                for (const apiKey in requiredFieldsMap) {
                    const formKey = requiredFieldsMap[apiKey];
                    const value = this.formData[formKey];

                    if (value === '' || value === null || value === undefined) {
                        this.predictionError = `Field '${formKey}' (untuk ${apiKey}) diperlukan untuk prediksi.`; isValid = false; break;
                    }

                    if (['bathrooms', 'bedrooms', 'sizeMin'].includes(formKey)) {
                        if (isNaN(parseFloat(value))) {
                            this.predictionError = `Field '${formKey}' harus angka.`; isValid = false; break;
                        }
                        dataForPrediction[apiKey] = parseFloat(value);
                    } else if (formKey === 'status') { // Untuk 'verified' API
                        dataForPrediction.verified = this.formData.status ? 1 : 0;
                    } else if (formKey === 'furnishing') {
                        dataForPrediction.furnishing = this.furnishingMapForPrediction[value];
                        if (dataForPrediction.furnishing === null || dataForPrediction.furnishing === undefined) {
                             this.predictionError = "Status Perabotan tidak valid."; isValid = false; break;
                        }
                    } else if (formKey === 'mainView') { // Untuk 'view_type' API
                        dataForPrediction.view_type = this.viewTypeMapForPrediction[value];
                         if (dataForPrediction.view_type === null || dataForPrediction.view_type === undefined) {
                             this.predictionError = "Tipe Pemandangan tidak valid."; isValid = false; break;
                        }
                    } else if (formKey === 'propertyLabel') { // Untuk 'title_keyword' API
                        dataForPrediction.title_keyword = this.keywordMapForPrediction[value];
                        if (dataForPrediction.title_keyword === null || dataForPrediction.title_keyword === undefined) {
                             this.predictionError = "Label Properti tidak valid."; isValid = false; break;
                        }
                    }
                }

                if (!isValid) { this.isPredicting = false; return; }

                try {
                    const response = await fetch('http://localhost:5000/prediksi/create', { // Pastikan URL API Prediksi benar
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(dataForPrediction)
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        this.predictionError = result.error || result.message || `Gagal mendapatkan prediksi (Status: ${response.status})`;
                        this.formData.price = '';
                    } else {
                        this.formData.price = parseFloat(result.prediction_result).toFixed(0);
                        this.predictionError = 'Prediksi harga berhasil dimuat.';
                    }
                } catch (error) {
                    console.error('Error calling prediction API:', error);
                    this.predictionError = 'Gagal terhubung ke layanan prediksi. Pastikan layanan aktif dan CORS dikonfigurasi.';
                    this.formData.price = '';
                } finally {
                    this.isPredicting = false;
                }
            },

            async submitForm() {
                this.isSubmitting = true;
                this.ajaxErrors = {};
                this.predictionError = '';

                const formElement = document.getElementById('propertyForm');
                const csrfToken = formElement.querySelector('input[name="_token"]').value;
                let dataToSend = new FormData();
                const { price_mode, ...payload } = this.formData;

                for (const key in payload) {
                    if (payload.hasOwnProperty(key)) {
                        if (key === 'status') {
                            dataToSend.append(key, payload[key] ? '1' : '0');
                        } else {
                            dataToSend.append(key, payload[key] === null || payload[key] === undefined ? '' : payload[key]);
                        }
                    }
                }
                if (document.getElementById('formMethod').value === 'PUT') {
                    dataToSend.append('_method', 'PUT');
                }

                try {
                    const response = await fetch(this.formAction, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: dataToSend
                    });

                    if (response.status === 422) { // Error validasi
                        this.ajaxErrors = (await response.json()).errors;
                    } else if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        this.ajaxErrors = { general: [errorData.message || `Terjadi kesalahan server (Status: ${response.status}).`] };
                        if(errorData.errors) this.ajaxErrors = {...this.ajaxErrors, ...errorData.errors};
                    } else { // Sukses
                        this.closeModal();
                        window.location.reload(); // Atau update tabel secara dinamis
                        return; // Keluar dari fungsi setelah sukses
                    }
                } catch (error) {
                    console.error('Kesalahan pengiriman form:', error);
                    this.ajaxErrors = { general: ['Terjadi kesalahan jaringan. Silakan coba lagi.'] };
                } finally {
                    this.isSubmitting = false;
                }
            },

            openDeleteModal(url, id, name) {
                this.propertyToDeleteUrl = url;
                this.propertyToDeleteId = id;
                this.propertyToDeleteName = name || 'properti ini';
                this.ajaxErrors = {}; // Reset error spesifik delete
                this.deleteError = ''; // Reset error global delete
                this.$dispatch('open-modal', 'deleteConfirmModal');
            },

            closeDeleteModal() {
                this.$dispatch('close-modal', 'deleteConfirmModal');
                this.propertyToDeleteUrl = '';
                this.propertyToDeleteId = null;
                this.propertyToDeleteName = '';
                this.isDeleting = false;
                // this.ajaxErrors = {}; // Mungkin tidak perlu direset di sini agar pesan error tetap terlihat jika ada
            },

            async confirmDelete() {
                this.isDeleting = true;
                this.ajaxErrors = {};
                this.deleteError = '';

                if (!this.csrfToken) {
                     this.ajaxErrors = { delete: 'CSRF token tidak ditemukan. Harap muat ulang halaman.' };
                     this.isDeleting = false;
                     return;
                }

                try {
                    const response = await fetch(this.propertyToDeleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({ message: 'Gagal menghapus properti.' }));
                        this.ajaxErrors = { delete: errorData.message || `Kesalahan HTTP: ${response.status}` };
                    } else {
                        this.closeDeleteModal();
                        window.location.reload(); // Atau update tabel secara dinamis
                        return; // Keluar dari fungsi setelah sukses
                    }
                } catch (error) {
                    console.error('Kesalahan jaringan saat menghapus:', error);
                    this.ajaxErrors = { delete: 'Terjadi kesalahan jaringan. Silakan coba lagi.' };
                } finally {
                    this.isDeleting = false;
                }
            }
        }
    }
</script>
@endpush
</x-app-layout>