<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Properti') }}
        </h2>
    </x-slot>

    {{-- Lingkup Data Alpine.js untuk halaman ini --}}
    <div class="py-6" x-data="masterProperti()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

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
                 <div x-show="predictionError" class="mb-4 p-4 bg-yellow-500 text-black rounded-lg" role="alert">
                    <span class="font-bold">Info Prediksi:</span> <span x-text="predictionError"></span>
                </div>
                <div x-show="deleteError" class="mb-4 p-4 bg-red-700 text-white rounded-md" role="alert">
                    <span class="font-bold">Error Hapus:</span> <span x-text="deleteError"></span>
                </div>


                <div class="flex justify-between items-center mb-4">
                    <form action="{{ route('data-master.properti.index') }}" method="GET" class="flex-grow md:flex-grow-0">
                        <div class="flex">
                            <input type="text" name="search" placeholder="Cari berdasarkan judul atau alamat..."
                                   value="{{ request('search') }}"
                                   class="px-4 py-2 rounded-l-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600 w-full md:w-auto">
                            <button type="submit"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700 transition">
                                Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('data-master.properti.index') }}"
                                   class="ml-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                    <button @click="openModal('add')"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        + Tambah Properti
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold">No</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Judul</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Alamat</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Kamar Tidur</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Kamar Mandi</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Harga (AED)</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Luas (sqft)</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Perabotan</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Tipe</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Pemandangan Utama</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Label Properti</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Ditambahkan Pada</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 text-gray-200 divide-y divide-gray-700">
                            @forelse ($dataProperty as $index => $property)
                                <tr :key="'prop-' + '{{ $property->id }}'">
                                    <td class="px-4 py-2">{{ $dataProperty->firstItem() + $index }}</td>
                                    <td class="px-4 py-2">{{ $property->title }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($property->displayAddress, 35) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $property->bedrooms }}</td>
                                    <td class="px-4 py-2 text-center">{{ $property->bathrooms }}</td>
                                    <td class="px-4 py-2">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-center">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $property->furnishing }}</td>
                                    <td class="px-4 py-2">{{ $property->type }}</td>
                                    <td class="px-4 py-2">{{ $property->view_type ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($property->keyword_flags, 20) ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($property->addedOn)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <button @click="openModal('edit', '{{ $property->id }}')"
                                                class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Edit
                                        </button>
                                        <button @click="openDeleteModal('{{ route('data-master.properti.destroy', $property->id) }}', '{{ $property->id }}', '{{ addslashes($property->title) }}')"
                                                type="button"
                                                class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="px-4 py-2 text-center">Tidak ada data properti.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-white">
                    {{ $dataProperty->links() }}
                </div>
            </div>
        </div>

        {{-- Komponen Modal Tambah/Edit Properti --}}
        <x-modal name="propertyFormModal" :maxWidth="'2xl'">
            <div class="p-6 bg-gray-800 text-white">
                <h2 class="text-lg font-medium mb-4" x-text="modalTitle"></h2>

                <div x-show="Object.keys(ajaxErrors).length > 0 && !ajaxErrors.delete" class="mb-4 p-3 bg-red-700 text-white rounded-md">
                    <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                    <ul class="list-disc list-inside mt-1">
                        <template x-for="(errorMessages, field) in ajaxErrors" :key="field">
                            <template x-if="field !== 'delete'">
                                <template x-for="message in errorMessages" :key="message">
                                    <li x-text="message"></li>
                                </template>
                            </template>
                        </template>
                    </ul>
                </div>

                <form @submit.prevent="submitForm" id="propertyForm" method="POST" action="#">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="addedOn" x-model="formData.addedOn">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"> 
                        <div>
                            <label for="title" class="block text-sm text-gray-300">Judul</label>
                            <input type="text" name="title" id="title" x-model="formData.title"
                                   class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="bedrooms" class="block text-sm text-gray-300">Kamar Tidur</label>
                            <input type="number" name="bedrooms" id="bedrooms" x-model="formData.bedrooms"
                                   class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="bathrooms" class="block text-sm text-gray-300">Kamar Mandi</label>
                            <input type="number" name="bathrooms" id="bathrooms" x-model="formData.bathrooms"
                                   class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="furnishing" class="block text-sm text-gray-300">Perabotan</label>
                            <select name="furnishing" id="furnishing" x-model="formData.furnishing"
                                    class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih...</option>
                                <option value="Yes">Ya (Yes)</option>
                                <option value="No">Tidak (No)</option>
                                <option value="Partly">Sebagian (Partly)</option> {{-- OPSI BARU --}}
                            </select>
                        </div>
                        <div>
                            <label for="displayAddress" class="block text-sm text-gray-300">Alamat</label>
                            <input type="text" name="displayAddress" id="displayAddress" x-model="formData.displayAddress"
                                   class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="sizeMin" class="block text-sm text-gray-300">Luas (sqft)</label>
                            <input type="number" name="sizeMin" id="sizeMin" x-model="formData.sizeMin"
                                   class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="view_type" class="block text-sm text-gray-300">Pemandangan Utama</label>
                            <select name="view_type" id="view_type" x-model="formData.view_type"
                                    class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Tipe Pemandangan...</option>
                                <template x-for="option in viewTypeOptions" :key="option.value">
                                    <option :value="option.value" x-text="option.text"></option>
                                </template>
                            </select>
                        </div>
                        <div> 
                            <label for="keyword_flags" class="block text-sm text-gray-300">Label Properti</label>
                            <select name="keyword_flags" id="keyword_flags" x-model="formData.keyword_flags"
                                     class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Penanda Kata Kunci...</option>
                                <template x-for="option in keywordOptions" :key="option.value">
                                    <option :value="option.value" x-text="option.text"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label for="price" class="block text-sm text-gray-300">Harga (AED)</label>
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="radio" id="price_manual" value="manual" x-model="formData.price_mode" name="price_mode_option" class="text-purple-600 focus:ring-purple-500">
                                <label for="price_manual" class="text-sm">Manual</label>
                                <input type="radio" id="price_predict" value="predict" x-model="formData.price_mode" name="price_mode_option" class="text-purple-600 focus:ring-purple-500">
                                <label for="price_predict" class="text-sm">Gunakan Prediksi</label>
                            </div>
                            <div class="flex">
                                <input type="number" name="price" id="price" x-model="formData.price"
                                       :disabled="formData.price_mode === 'predict'"
                                       class="w-full bg-gray-700 border border-gray-600 rounded-l-lg p-2 text-white focus:ring-purple-500 focus:border-purple-500"
                                       :class="{'bg-gray-600 cursor-not-allowed': formData.price_mode === 'predict'}">
                                <button type="button" @click="getPredictedPrice" x-show="formData.price_mode === 'predict'"
                                        class="px-3 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 transition text-sm"
                                        :disabled="isPredicting">
                                    <span x-show="!isPredicting">Dapatkan Prediksi</span>
                                    <span x-show="isPredicting">Memprediksi...</span>
                                </button>
                            </div>
                        </div>
                         <div class="flex items-center space-x-2 mt-2">
                            <input type="checkbox" name="verified" id="verified" value="1" x-model="formData.verified"
                                   class="rounded border-gray-600 text-purple-600 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                            <label for="verified" class="text-sm text-gray-300">Terverifikasi</label>
                        </div>
                        <input type="hidden" name="type" x-model="formData.type" value="Residential for Sale">
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button type="button" @click="closeModal()">
                            Batal
                        </x-secondary-button>
                        <x-primary-button class="ms-3" type="submit" x-text="submitButtonText"
                                        x-bind:disabled="isSubmitting">
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Modal Konfirmasi Hapus --}}
        <x-modal name="deleteConfirmModal" :maxWidth="'md'">
            <div class="p-6 bg-gray-800 text-white">
                <h2 class="text-lg font-medium mb-2">Konfirmasi Hapus</h2>
                <p class="mb-1 text-sm text-gray-300">Anda yakin ingin menghapus properti:</p>
                <p class="mb-6 font-semibold" x-text="propertyToDeleteName"></p>
                <p class="text-sm text-gray-400">Tindakan ini tidak dapat diurungkan.</p>
                
                <div x-show="ajaxErrors.delete" class="mt-4 mb-4 p-3 bg-red-700 text-white rounded-md">
                     <p x-text="ajaxErrors.delete"></p>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeDeleteModal()" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500">
                        Batal
                    </button>
                    <button @click="confirmDelete()" type="button"
                            class="ms-3 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-red-500"
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
            
            showDeleteModalState: false, 
            propertyToDeleteId: null,
            propertyToDeleteUrl: '',
            propertyToDeleteName: '', 
            isDeleting: false,

            viewTypeMapForPrediction: { 
                '': null, 
                'sea view': 0, 
                'burj khalifa view': 1,
                'golf course view': 2,
                'community view': 3,
                'city view': 4,
                'lake view': 5,
                'pool view': 6,
                'canal view': 7
            },
            keywordMapForPrediction: {
                '': null, 
                'luxury': 0,
                'furnished': 1,
                'spacious': 2,
                'prime': 3,
                'studio': 4,
                'penthouse': 5,
                'investment': 6,
                'villa': 7,
                'downtown': 8
            },
            // Pemetaan baru untuk furnishing sesuai dengan model Python
            furnishingMapForPrediction: {
                '': null,     // Jika tidak dipilih
                'Yes': 0,     // Sesuai dengan 'YES': 0 di Python
                'No': 1,      // Sesuai dengan 'NO': 1 di Python
                'Partly': 2   // Sesuai dengan 'PARTLY': 2 di Python
            },
            keywordOptions: [ 
                { value: 'luxury', text: 'Mewah' }, { value: 'furnished', text: 'Berperabot' },
                { value: 'spacious', text: 'Luas' }, { value: 'prime', text: 'Utama' },
                { value: 'studio', text: 'Studio' }, { value: 'penthouse', text: 'Penthouse' },
                { value: 'investment', text: 'Investasi' }, { value: 'villa', text: 'Vila' },
                { value: 'downtown', text: 'Pusat Kota' }
            ],
            viewTypeOptions: [ 
                { value: 'sea view', text: 'Pemandangan Laut' }, { value: 'burj khalifa view', text: 'Pemandangan Burj Khalifa' },
                { value: 'golf course view', text: 'Pemandangan Lapangan Golf' }, { value: 'community view', text: 'Pemandangan Komunitas' },
                { value: 'city view', text: 'Pemandangan Kota' }, { value: 'lake view', text: 'Pemandangan Danau' },
                { value: 'pool view', text: 'Pemandangan Kolam Renang' }, { value: 'canal view', text: 'Pemandangan Kanal' }
            ],
            formData: { 
                title: '', bedrooms: '', bathrooms: '', price: '',
                furnishing: '', displayAddress: '', sizeMin: '', // furnishing akan menyimpan "Yes", "No", atau "Partly"
                verified: true, type: 'Residential for Sale',
                view_type: '', 
                keyword_flags: '',
                addedOn: '', 
                price_mode: 'manual'
            },

            init() {
                const serverDeleteError = '{{ session("error_delete") }}';
                if (serverDeleteError) {
                    this.deleteError = serverDeleteError;
                    setTimeout(() => this.deleteError = '', 5000);
                }

                const fieldsToWatchForPrediction = ['bedrooms', 'bathrooms', 'furnishing', 'sizeMin', 'view_type', 'keyword_flags'];
                fieldsToWatchForPrediction.forEach(field => {
                    this.$watch(`formData.${field}`, (newValue, oldValue) => {
                        if (this.formData.price_mode === 'predict' && this.formData.price !== '') {
                            if (this.isPredicting === false) { 
                                this.formData.price = '';
                                this.predictionError = 'Data input berubah. Klik "Dapatkan Prediksi" untuk harga terbaru.';
                            }
                        }
                    });
                });

                this.$watch('formData.price_mode', (newValue, oldValue) => {
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
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0'); 
                const day = String(today.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },

            openModal(mode, propertyId = null) { 
                this.ajaxErrors = {};
                this.predictionError = ''; 
                this.currentPropertyId = propertyId;
                this.resetAlpineFormData(); 

                if (mode === 'add') {
                    this.modalTitle = 'Tambah Properti Baru';
                    this.submitButtonText = 'Simpan';
                    this.formAction = '{{ route("data-master.properti.store") }}';
                    document.getElementById('formMethod').value = 'POST';
                    this.formData.verified = true; 
                    this.formData.type = 'Residential for Sale';
                    this.formData.keyword_flags = '';
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
                this.resetAlpineFormData(); 
                this.currentPropertyId = null;
                this.ajaxErrors = {};
                this.predictionError = '';
            },

            resetAlpineFormData() { 
                const today = this.getTodayDate();
                this.formData = {
                    title: '', bedrooms: '', bathrooms: '', price: '',
                    furnishing: '', displayAddress: '', sizeMin: '',
                    verified: false, type: 'Residential for Sale', 
                    view_type: '', keyword_flags: '',
                    addedOn: today, price_mode: 'manual'
                };
                if (document.getElementById('propertyForm')) {
                    document.getElementById('propertyForm').reset(); 
                    this.formData.price_mode = 'manual';
                    this.formData.verified = false; 
                    this.formData.addedOn = today;
                }
            },

            async fetchPropertyData(id) { 
                this.isSubmitting = true; 
                try {
                    let url = `{{ route('data-master.properti.edit-data', ['id' => '_PLACEHOLDER_']) }}`;
                    url = url.replace('_PLACEHOLDER_', id);
                    const response = await fetch(url);
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({ message: 'Gagal mengambil data properti.' }));
                        throw new Error(errorData.message || `Kesalahan HTTP! status: ${response.status}`);
                    }
                    const data = await response.json();
                    this.formData.title = data.title || '';
                    this.formData.bedrooms = data.bedrooms || '';
                    this.formData.bathrooms = data.bathrooms || '';
                    this.formData.price = data.price || ''; 
                    this.formData.furnishing = data.furnishing || '';
                    this.formData.displayAddress = data.displayAddress || '';
                    this.formData.sizeMin = data.sizeMin || '';
                    this.formData.verified = !!data.verified; 
                    this.formData.type = data.type || 'Residential for Sale';
                    this.formData.view_type = data.view_type || ''; 
                    this.formData.keyword_flags = data.keyword_flags || ''; 
                    this.formData.addedOn = data.addedOn ? new Date(data.addedOn).toISOString().split('T')[0] : this.getTodayDate();
                    this.formData.price_mode = 'manual'; 
                } catch (error) {
                    console.error('Kesalahan saat mengambil data properti:', error);
                    this.ajaxErrors = { general: [error.message] };
                } finally {
                    this.isSubmitting = false;
                }
            },

            async getPredictedPrice() { 
                this.isPredicting = true;
                this.predictionError = ''; 
                this.ajaxErrors = {}; 

                const requiredForPrediction = ['bathrooms', 'bedrooms', 'furnishing', 'sizeMin', 'view_type', 'keyword_flags'];
                for (const field of requiredForPrediction) {
                    const value = this.formData[field];
                    const isNumericField = ['bathrooms', 'bedrooms', 'sizeMin'].includes(field);

                    if (isNumericField) {
                        if (value === '' || value === null || value === undefined || isNaN(parseFloat(value))) {
                            this.predictionError = `Field '${field}' harus berupa angka yang valid untuk prediksi.`;
                            this.isPredicting = false;
                            return;
                        }
                    } else { 
                        if (value === '' || value === null || value === undefined) {
                            this.predictionError = `Field '${field}' diperlukan untuk prediksi.`;
                            this.isPredicting = false;
                            return;
                        }
                    }
                }
                
                const dataForPrediction = {
                    bathrooms: parseFloat(this.formData.bathrooms),
                    bedrooms: parseFloat(this.formData.bedrooms),
                    furnishing: this.furnishingMapForPrediction[this.formData.furnishing], // <-- MENGGUNAKAN PEMETAAN BARU
                    sizeMin: parseFloat(this.formData.sizeMin),
                    verified: this.formData.verified ? 1 : 0,
                    listing_age_category: 0, 
                    view_type: this.viewTypeMapForPrediction[this.formData.view_type],
                    title_keyword: this.keywordMapForPrediction[this.formData.keyword_flags]
                };

                // Validasi tambahan untuk hasil pemetaan furnishing
                if (dataForPrediction.furnishing === null || dataForPrediction.furnishing === undefined) {
                     this.predictionError = "Status Perabotan tidak valid atau belum dipilih untuk prediksi.";
                     this.isPredicting = false;
                     return;
                }
                if (dataForPrediction.view_type === null || dataForPrediction.view_type === undefined) {
                    this.predictionError = "Tipe Pemandangan tidak valid atau belum dipilih untuk prediksi.";
                    this.isPredicting = false;
                    return;
                }
                if (dataForPrediction.title_keyword === null || dataForPrediction.title_keyword === undefined) {
                    this.predictionError = "Penanda Kata Kunci tidak valid atau belum dipilih untuk prediksi.";
                    this.isPredicting = false;
                    return;
                }

                try {
                    const response = await fetch('http://localhost:5000/prediksi/create', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(dataForPrediction)
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        this.predictionError = result.error || `Gagal mendapatkan prediksi (Status: ${response.status})`;
                        this.formData.price = ''; 
                    } else {
                        this.formData.price = parseFloat(result.prediction_result).toFixed(0); 
                        this.predictionError = 'Prediksi harga berhasil dimuat.';
                    }
                } catch (error) {
                    console.error('Error calling prediction API:', error);
                    this.predictionError = 'Gagal terhubung ke layanan prediksi. Pastikan layanan aktif.';
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
                        if (key === 'verified') {
                            dataToSend.append(key, payload[key] ? '1' : '0');
                        } else if (key === 'view_type') {
                            const selectedValue = payload[key];
                            dataToSend.append(key, selectedValue === null || selectedValue === undefined ? '' : selectedValue);
                        } else if (key === 'keyword_flags') {
                            dataToSend.append(key, payload[key] === null || payload[key] === undefined ? '' : payload[key]);
                        } else { // Termasuk 'furnishing' akan dikirim sebagai string "Yes", "No", atau "Partly"
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

                    if (response.status === 422) {
                        const responseData = await response.json();
                        this.ajaxErrors = responseData.errors;
                        this.isSubmitting = false;
                        return; 
                    }
                    
                    if (!response.ok) {
                        let errorMessage = `Terjadi kesalahan server (Status: ${response.status}). Coba lagi nanti.`;
                        try { const errorData = await response.json(); if (errorData.message) errorMessage = errorData.message; } catch (e) {}
                        this.ajaxErrors = { general: [errorMessage] };
                        this.isSubmitting = false;
                        console.error('Kesalahan server:', errorMessage);
                        return;
                    }

                    this.closeModal();
                    window.location.reload();

                } catch (error) { 
                    console.error('Kesalahan pengiriman:', error);
                    this.ajaxErrors = { general: ['Terjadi kesalahan jaringan atau respons tidak valid. Silakan coba lagi.'] };
                    this.isSubmitting = false;
                }
            },

            openDeleteModal(url, id, name) {
                this.propertyToDeleteUrl = url;
                this.propertyToDeleteId = id;
                this.propertyToDeleteName = name || 'properti ini'; 
                this.ajaxErrors = {}; 
                this.deleteError = ''; 
                this.$dispatch('open-modal', 'deleteConfirmModal');
            },

            closeDeleteModal() {
                this.$dispatch('close-modal', 'deleteConfirmModal');
                this.propertyToDeleteUrl = '';
                this.propertyToDeleteId = null;
                this.propertyToDeleteName = '';
                this.isDeleting = false;
            },

            async confirmDelete() {
                this.isDeleting = true;
                this.ajaxErrors = {}; 
                this.deleteError = ''; 

                const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenElement) {
                    this.ajaxErrors = { ...this.ajaxErrors, delete: 'CSRF token tidak ditemukan. Harap muat ulang halaman.' };
                    this.isDeleting = false;
                    return;
                }
                const csrfToken = csrfTokenElement.getAttribute('content');


                try {
                    const response = await fetch(this.propertyToDeleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json' 
                        },
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({ message: 'Gagal menghapus properti. Respons tidak valid.' }));
                        this.ajaxErrors = { ...this.ajaxErrors, delete: errorData.message || `Kesalahan HTTP: ${response.status}` };
                        console.error('Kesalahan saat menghapus:', errorData);
                        this.isDeleting = false;
                        return;
                    }
                    
                    this.closeDeleteModal();
                    window.location.reload(); 

                } catch (error) {
                    console.error('Kesalahan jaringan saat menghapus:', error);
                    this.ajaxErrors = { ...this.ajaxErrors, delete: 'Terjadi kesalahan jaringan. Silakan coba lagi.' };
                    this.isDeleting = false;
                }
            }
        }
    }
</script>
@endpush
</x-app-layout>
