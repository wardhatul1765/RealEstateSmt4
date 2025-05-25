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

                {{-- Notifikasi --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                         class="mb-4 p-4 bg-green-500 text-white rounded-lg text-sm" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                         class="mb-4 p-4 bg-red-500 text-white rounded-lg text-sm" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <div x-show="predictionError && predictionError.length > 0" class="mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 dark:bg-yellow-700 dark:text-yellow-100 dark:border-yellow-600 rounded-lg text-sm" role="alert" x-cloak>
                    <span class="font-bold">Info Prediksi:</span> <span x-text="predictionError"></span>
                </div>
                <div x-show="deleteError && deleteError.length > 0" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 dark:bg-red-700 dark:text-red-100 dark:border-red-600 rounded-lg text-sm" role="alert" x-cloak>
                    <span class="font-bold">Error Hapus:</span> <span x-text="deleteError"></span>
                </div>


                {{-- Baris Pencarian dan Tombol Tambah --}}
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <form action="{{ route('data-master.properti.index') }}" method="GET" class="flex-grow sm:flex-grow-0 w-full sm:w-auto">
                        <div class="flex">
                            <input type="text" name="search" placeholder="Cari judul atau alamat..."
                                   value="{{ request('search') }}"
                                   class="px-3 py-2 text-sm rounded-l-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 w-full">
                            <button type="submit"
                                    class="px-3 py-2 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700 transition whitespace-nowrap text-sm">
                                Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('data-master.properti.index') }}"
                                   class="ml-2 px-3 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition whitespace-nowrap text-sm">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                    <button @click="openModal('add')"
                            class="w-full sm:w-auto px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition whitespace-nowrap text-sm">
                        + Tambah Properti
                    </button>
                </div>

                {{-- Tabel Data Properti --}}
                <div class="overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">No</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Judul</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Alamat</th>
                                <th class="px-1 py-2 text-center text-xs font-medium uppercase tracking-wider">KT</th>
                                <th class="px-1 py-2 text-center text-xs font-medium uppercase tracking-wider">KM</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Harga</th>
                                <th class="px-1 py-2 text-center text-xs font-medium uppercase tracking-wider">Luas</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Furn.</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Tipe</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">View</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Label</th>
                                <th class="px-2 py-2 text-center text-xs font-medium uppercase tracking-wider">Img</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Deskripsi</th>
                                <th class="px-2 py-2 text-center text-xs font-medium uppercase tracking-wider">Status</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Added</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($dataProperty as $index => $property)
                                <tr :key="'prop-' + '{{ $property->id }}'" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 text-xs">
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $dataProperty->firstItem() + $index }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap" title="{{$property->title}}">{{ Str::limit($property->title, 20) }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap" title="{{$property->Address}}">{{ Str::limit($property->Address, 25) }}</td>
                                    <td class="px-1 py-1 text-center whitespace-nowrap">{{ $property->bedrooms }}</td>
                                    <td class="px-1 py-1 text-center whitespace-nowrap">{{ $property->bathrooms }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-1 py-1 text-center whitespace-nowrap">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        @php
                                            $furnish = $property->furnishing;
                                            if (strtoupper($furnish) === 'YES') $furnish = 'Y';
                                            elseif (strtoupper($furnish) === 'NO') $furnish = 'N';
                                            elseif (strtoupper($furnish) === 'PARTLY') $furnish = 'P';
                                            else $furnish = Str::limit(ucfirst(strtolower($furnish ?? '-')),1);
                                        @endphp
                                        {{ $furnish }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap" title="{{$property->propertyType}}">{{ Str::limit($property->propertyType, 15) }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap" title="{{$property->mainView}}">{{ Str::limit($property->mainView, 10) ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap" title="{{$property->propertyLabel}}">{{ Str::limit($property->propertyLabel, 10) ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        @php
                                            $imagesArr = []; // Menggunakan nama variabel yang berbeda untuk kejelasan
                                            if ($property->image) {
                                                $imagesArr = is_array($property->image) ? $property->image : json_decode($property->image, true);
                                                if (json_last_error() !== JSON_ERROR_NONE && is_string($property->image) && !empty($property->image)) {
                                                    $imagesArr = array_map('trim', explode(',', $property->image));
                                                } elseif (!is_array($imagesArr)) {
                                                    $imagesArr = [];
                                                }
                                            }
                                            $imagesArr = array_values(array_filter($imagesArr)); // Membersihkan nilai kosong/null
                                        @endphp
                                        @if(!empty($imagesArr))
                                            <div class="flex items-center">
                                                {{-- Pastikan $imagesArr[0] adalah URL yang valid dan symlink storage berfungsi --}}
                                                {{-- Jika $imagesArr[0] tidak ada, gunakan placeholder. Anda perlu membuat file placeholder.png di public/images --}}
                                                <img 
                                                    src="{{ $imagesArr[0] ?? asset('images/placeholder.png') }}" 
                                                    alt="Thumb"
                                                    class="h-6 w-6 object-cover rounded cursor-pointer open-image-gallery"
                                                    data-images='@json($imagesArr)'
                                                    onerror="handleImageError(this)">
                                                <button type="button"
                                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 text-xs open-image-gallery ml-1"
                                                        data-images='{{ json_encode($imagesArr) }}'> {{-- PERUBAHAN DI SINI --}}
                                                    ({{ count($imagesArr) }})
                                                </button>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap" title="{{$property->description}}">{{ Str::limit($property->description, 20) ?? '-' }}</td>
                                    <td class="px-2 py-1 text-center whitespace-nowrap">
                                        @if($property->status)
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">Ver</span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">Not</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $property->addedOn ? Carbon\Carbon::parse($property->addedOn)->format('d/m/y') : ($property->created_at ? Carbon\Carbon::parse($property->created_at)->format('d/m/y') : '-') }}</td>
                                    <td class="px-2 py-1 flex space-x-1 whitespace-nowrap">
                                        <button @click="openModal('edit', {{ Js::from($property->id) }})"
                                                class="px-2 py-0.5 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                            Edit
                                        </button>
                                        <button @click="openDeleteModal('{{ route('data-master.properti.destroy', $property->id) }}', {{ Js::from($property->id) }}, {{ Js::from($property->title) }})"
                                                type="button"
                                                class="px-2 py-0.5 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">Tidak ada data properti yang ditemukan.</td>
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
        <x-modal name="propertyFormModal" :maxWidth="'3xl'">
            <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <h2 class="text-lg font-medium mb-4" x-text="modalTitle"></h2>

                {{-- Error Validasi AJAX --}}
                <div x-show="Object.keys(ajaxErrors).length > 0 && !ajaxErrors.delete" class="mb-4 p-3 bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-md text-sm" x-cloak>
                    <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                    <ul class="list-disc list-inside mt-1">
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

                <form @submit.prevent="submitForm" id="propertyForm" method="POST" action="#" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="addedOn" x-model="formData.addedOn">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        {{-- Baris 1 --}}
                        <div>
                            <label for="title_form" class="block font-medium text-gray-700 dark:text-gray-300">Judul</label>
                            <input type="text" name="title" id="title_form" x-model="formData.title"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="Address_form" class="block font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                            <input type="text" name="Address" id="Address_form" x-model="formData.Address"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        {{-- Baris 2 --}}
                        <div>
                            <label for="bedrooms_form" class="block font-medium text-gray-700 dark:text-gray-300">Kamar Tidur</label>
                            <input type="number" name="bedrooms" id="bedrooms_form" x-model="formData.bedrooms" min="0"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="bathrooms_form" class="block font-medium text-gray-700 dark:text-gray-300">Kamar Mandi</label>
                            <input type="number" name="bathrooms" id="bathrooms_form" x-model="formData.bathrooms" min="0"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="sizeMin_form" class="block font-medium text-gray-700 dark:text-gray-300">Luas (sqft)</label>
                            <input type="number" name="sizeMin" id="sizeMin_form" x-model="formData.sizeMin" min="0"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        {{-- Baris 3 --}}
                        <div>
                            <label for="furnishing_form" class="block font-medium text-gray-700 dark:text-gray-300">Perabotan</label>
                            <select name="furnishing" id="furnishing_form" x-model="formData.furnishing"
                                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih...</option> <option value="Yes">Ya (Yes)</option> <option value="No">Tidak (No)</option> <option value="Partly">Sebagian (Partly)</option>
                            </select>
                        </div>
                        <div>
                            <label for="mainView_form" class="block font-medium text-gray-700 dark:text-gray-300">Pemandangan Utama</label>
                            <select name="mainView" id="mainView_form" x-model="formData.mainView"
                                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                                <template x-for="option in viewTypeOptions" :key="option.value"> <option :value="option.value" x-text="option.text"></option> </template>
                            </select>
                        </div>
                        <div>
                            <label for="propertyLabel_form" class="block font-medium text-gray-700 dark:text-gray-300">Label Properti</label>
                            <select name="propertyLabel" id="propertyLabel_form" x-model="formData.propertyLabel"
                                     class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                                <template x-for="option in keywordOptions" :key="option.value"> <option :value="option.value" x-text="option.text"></option> </template>
                            </select>
                        </div>
                        
                        <!-- {{-- Baris Tambahan untuk Tipe Properti --}}
                        <div>
                            <label for="propertyType_form" class="block font-medium text-gray-700 dark:text-gray-300">Tipe Properti</label>
                            <input type="text" name="propertyType" id="propertyType_form" x-model="formData.propertyType"
                                   placeholder="Mis: Residential for Sale"
                                   class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500">
                        </div> -->


                        {{-- Baris Harga (dibuat span lebih lebar) --}}
                        <div class="md:col-span-2 lg:col-span-2"> {{-- Sesuaikan span jika perlu --}}
                            <label for="price_form_input" class="block font-medium text-gray-700 dark:text-gray-300">Harga (AED)</label>
                            <div class="flex items-center space-x-4 my-1">
                                <label class="flex items-center">
                                    <input type="radio" value="manual" x-model="formData.price_mode" name="price_mode_option_form" class="text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
                                    <span class="ms-2">Manual</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" value="predict" x-model="formData.price_mode" name="price_mode_option_form" class="text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
                                    <span class="ms-2">Gunakan Prediksi</span>
                                </label>
                            </div>
                            <div class="flex">
                                <input type="number" name="price" id="price_form_input" x-model="formData.price" min="0"
                                       :disabled="formData.price_mode === 'predict'"
                                       class="block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-l-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500"
                                       :class="{'bg-gray-200 dark:bg-gray-500 cursor-not-allowed': formData.price_mode === 'predict'}">
                                <button type="button" @click="getPredictedPrice" x-show="formData.price_mode === 'predict'"
                                        class="px-3 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 transition whitespace-nowrap"
                                        :disabled="isPredicting">
                                    <span x-show="!isPredicting">Dapatkan Prediksi</span> <span x-show="isPredicting">Memprediksi...</span>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Baris Deskripsi --}}
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="description_form" class="block font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea name="description" id="description_form" x-model="formData.description" rows="3"
                                      class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                        </div>

                        {{-- Baris Upload Gambar --}}
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="images_upload_input" class="block font-medium text-gray-700 dark:text-gray-300">Gambar Properti</label>
                            <input type="file" name="images[]" id="images_upload_input" multiple @change="handleImageFiles($event)"
                                   accept="image/jpeg,image/png,image/webp,image/gif"
                                   class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-700 file:text-purple-700 dark:file:text-purple-100 hover:file:bg-purple-100 dark:hover:file:bg-purple-600">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar baru akan menggantikan gambar lama (jika ada saat edit). Max 2MB per file.</p>
                            
                            <div x-show="currentPropertyId && formData.existingImages && formData.existingImages.length > 0" class="mt-3" x-cloak>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Gambar Saat Ini:</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <template x-for="(imageUrl, index) in formData.existingImages" :key="'existing-' + index">
                                        <img :src="imageUrl" class="h-16 w-16 object-cover rounded border dark:border-gray-600" :alt="'Gambar Lama ' + (index + 1)">
                                    </template>
                                </div>
                            </div>
                            <div x-show="formData.images && formData.images.length > 0" class="mt-3" x-cloak>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Gambar Baru Dipilih (<span x-text="formData.images.length"></span>):</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <template x-for="(file, index) in formData.images" :key="'new-' + index">
                                        <div class="relative">
                                            <img :src="URL.createObjectURL(file)" class="h-16 w-16 object-cover rounded border dark:border-gray-600" :alt="file.name">
                                            <button @click="removeImage(index)" type="button" title="Hapus gambar ini"
                                                    class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full h-4 w-4 flex items-center justify-center text-xs leading-none">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Status Terverifikasi --}}
                        <div class="lg:col-span-1 flex items-end pb-1">
                            <label for="status_form_input" class="flex items-center font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="status" id="status_form_input" value="1" x-model="formData.status"
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
                                x-text="isSubmitting ? (currentPropertyId ? 'Memperbarui...' : 'Menyimpan...') : (currentPropertyId ? 'Perbarui' : 'Simpan')"
                                :disabled="isSubmitting">
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Modal Konfirmasi Hapus --}}
        <x-modal name="deleteConfirmModal" :maxWidth="'md'">
           {{-- ... (Konten Modal Hapus sama seperti sebelumnya) ... --}}
           <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <h2 class="text-lg font-medium mb-2">Konfirmasi Hapus</h2>
                <p class="mb-1 text-sm text-gray-600 dark:text-gray-300">Anda yakin ingin menghapus properti:</p>
                <p class="mb-6 font-semibold" x-text="propertyToDeleteName"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat diurungkan.</p>
                <div x-show="ajaxErrors.delete" class="mt-4 mb-4 p-3 bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-md" x-cloak>
                    <p x-text="ajaxErrors.delete"></p>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="closeDeleteModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-indigo-500">Batal</button>
                    <button @click="confirmDelete()" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-red-500" :disabled="isDeleting">
                        <span x-show="!isDeleting">Hapus Properti</span> <span x-show="isDeleting">Menghapus...</span>
                    </button>
                </div>
            </div>
        </x-modal>

    </div>

    {{-- Modal Galeri Gambar --}}
    <div id="imageGalleryModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-[9999] hidden p-4">
        {{-- ... (Konten Modal Galeri Gambar sama seperti sebelumnya) ... --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-xl relative max-w-xl w-full max-h-[90vh] flex flex-col">
            <button id="closeGalleryModal" class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 text-2xl leading-none">&times;</button>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4" id="galleryTitleModal">Galeri Gambar</h3>
            <div class="flex-grow overflow-hidden flex items-center justify-center mb-4">
                <img id="galleryImageModalDisplay" src="" alt="Property Image" class="max-w-full max-h-[60vh] object-contain">
            </div>
            <div class="flex justify-between items-center">
                <button id="prevImageModal" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">&lt; Prev</button>
                <span id="imageCounterModal" class="text-sm text-gray-700 dark:text-gray-300"></span>
                <button id="nextImageModal" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Next &gt;</button>
            </div>
        </div>
    </div>

@push('scripts')
{{-- Script untuk Galeri Gambar (Vanilla JS) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const imageGalleryModalElement = document.getElementById('imageGalleryModal');
    if (imageGalleryModalElement) { 
        const galleryImageElement = document.getElementById('galleryImageModalDisplay');
        const closeButtonElement = document.getElementById('closeGalleryModal');
        const prevButtonElement = document.getElementById('prevImageModal');
        const nextButtonElement = document.getElementById('nextImageModal');
        const imageCounterElement = document.getElementById('imageCounterModal');
        
        let currentGalleryImages = [];
        let currentGalleryIndex = 0;

        function showGalleryImage(index) {
            if (!galleryImageElement || !imageCounterElement || !prevButtonElement || !nextButtonElement) return;
            if (currentGalleryImages.length === 0 || index < 0 || index >= currentGalleryImages.length) return;
            galleryImageElement.src = currentGalleryImages[index];
            imageCounterElement.textContent = `${index + 1} / ${currentGalleryImages.length}`;
            prevButtonElement.disabled = index === 0;
            nextButtonElement.disabled = index === currentGalleryImages.length - 1;

            if (currentGalleryImages.length <= 1) {
                prevButtonElement.classList.add('hidden');
                nextButtonElement.classList.add('hidden');
                imageCounterElement.classList.add('hidden');
            } else {
                prevButtonElement.classList.remove('hidden');
                nextButtonElement.classList.remove('hidden');
                imageCounterElement.classList.remove('hidden');
            }
        }

        function openImageGalleryModal(imagesArray) {
             currentGalleryImages = imagesArray;
            if (Array.isArray(currentGalleryImages) && currentGalleryImages.length > 0) {
                currentGalleryIndex = 0;
                showGalleryImage(currentGalleryIndex);
                imageGalleryModalElement.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }
        
        function closeImageGalleryModal() {
            imageGalleryModalElement.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentGalleryImages = [];
            if(galleryImageElement) galleryImageElement.src = "";
        }

        document.body.addEventListener('click', function(event) {
            const trigger = event.target.closest('.open-image-gallery');
            if (trigger) {
                const imagesData = trigger.dataset.images;
                if (imagesData) {
                    try {
                        const parsedImages = JSON.parse(imagesData);
                        openImageGalleryModal(parsedImages);
                    } catch (e) {
                        console.error('Error parsing image data for gallery:', e, imagesData);
                    }
                }
            }
        });

        if(closeButtonElement) closeButtonElement.addEventListener('click', closeImageGalleryModal);
        if(prevButtonElement) prevButtonElement.addEventListener('click', () => { 
            if (currentGalleryIndex > 0) { currentGalleryIndex--; showGalleryImage(currentGalleryIndex); }
        });
        if(nextButtonElement) nextButtonElement.addEventListener('click', () => { 
            if (currentGalleryIndex < currentGalleryImages.length - 1) { currentGalleryIndex++; showGalleryImage(currentGalleryIndex); }
        });
        if(imageGalleryModalElement) imageGalleryModalElement.addEventListener('click', (event) => { 
            if (event.target === imageGalleryModalElement) closeImageGalleryModal();
        });
        document.addEventListener('keydown', (event) => { 
            if (event.key === 'Escape' && imageGalleryModalElement && !imageGalleryModalElement.classList.contains('hidden')) {
                closeImageGalleryModal();
            }
        });
    }
});
</script>

{{-- Script untuk Alpine.js Komponen masterProperti --}}
<script>
function masterProperti() {
    return {
        modalTitle: 'Form Properti',
        formAction: '', // URL untuk submit form (store atau update)
        isSubmitting: false, // Status apakah form sedang disubmit
        isPredicting: false, // Status untuk prediksi harga
        ajaxErrors: {}, // Untuk menyimpan error validasi dari server
        predictionError: '', // Pesan error khusus untuk prediksi harga
        deleteError: '', // Pesan error khusus untuk proses hapus
        currentPropertyId: null, // ID properti yang sedang diedit
        propertyToDeleteId: null,
        propertyToDeleteUrl: '',
        propertyToDeleteName: '',
        isDeleting: false, // Status apakah proses hapus sedang berjalan
        csrfToken: '', // CSRF token Laravel

        // Opsi-opsi untuk dropdown (sesuai kode Abang)
        keywordOptions: [
            { value: '', text: 'Pilih Label Properti...' }, { value: 'luxury', text: 'Mewah (Luxury)' },
            { value: 'furnished', text: 'Berperabot (Furnished)' }, { value: 'spacious', text: 'Luas (Spacious)' },
            { value: 'prime', text: 'Utama (Prime)' }, { value: 'studio', text: 'Studio' },
            { value: 'penthouse', text: 'Penthouse' }, { value: 'investment', text: 'Investasi (Investment)' },
            { value: 'villa', text: 'Vila' }, { value: 'downtown', text: 'Pusat Kota (Downtown)' }
        ],
        viewTypeOptions: [
            { value: '', text: 'Pilih Tipe Pemandangan...' }, { value: 'sea view', text: 'Pemandangan Laut (Sea View)' },
            { value: 'burj khalifa view', text: 'Pemandangan Burj Khalifa' }, { value: 'golf course view', text: 'Pemandangan Lapangan Golf' },
            { value: 'community view', text: 'Pemandangan Komunitas' }, { value: 'city view', text: 'Pemandangan Kota (City View)' },
            { value: 'lake view', text: 'Pemandangan Danau (Lake View)' },{ value: 'pool view', text: 'Pemandangan Kolam Renang (Pool View)' },
            { value: 'canal view', text: 'Pemandangan Kanal (Canal View)' }
        ],
        // Mapping untuk API prediksi harga (sesuai kode Abang)
        viewTypeMapForPrediction: { '': null, 'sea view': 0, 'burj khalifa view': 1, 'golf course view': 2, 'community view': 3, 'city view': 4, 'lake view': 5, 'pool view': 6, 'canal view': 7 },
        keywordMapForPrediction: { '': null, 'luxury': 0, 'furnished': 1, 'spacious': 2, 'prime': 3, 'studio': 4, 'penthouse': 5, 'investment': 6, 'villa': 7, 'downtown': 8 },
        furnishingMapForPrediction: { '': null, 'Yes': 0, 'No': 1, 'Partly': 2 },

        // Data utama untuk form, di-bind dengan x-model
        formData: {
            title: '', Address: '', bedrooms: '', bathrooms: '', price: '',
            sizeMin: '', furnishing: '', propertyType: 'Residential for Sale', // Default tipe properti
            status: true, mainView: '', propertyLabel: '', description: '',
            addedOn: '', price_mode: 'manual',
            images: [], // Untuk menyimpan file gambar BARU yang dipilih (objek File)
            existingImages: [] // Untuk menyimpan URL gambar LAMA (pas mode edit)
        },

        // Inisialisasi komponen
        init() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('#propertyForm input[name="_token"]')?.value;
            const serverDeleteError = '{{ session("error_delete") }}'; // Ambil error hapus dari session (jika ada)
            if (serverDeleteError) { this.deleteError = serverDeleteError; setTimeout(() => this.deleteError = '', 5000); }

            // Watcher untuk field yang mempengaruhi prediksi harga (sesuai kode Abang)
            const fieldsToWatch = ['bedrooms', 'bathrooms', 'furnishing', 'sizeMin', 'mainView', 'propertyLabel', 'status'];
            fieldsToWatch.forEach(field => {
                this.$watch(`formData.${field}`, (newValue, oldValue) => {
                    if (this.formData.price_mode === 'predict' && this.formData.price !== '' && !this.isPredicting) {
                        this.formData.price = '';
                        this.predictionError = 'Data input berubah. Klik "Dapatkan Prediksi" untuk harga terbaru.';
                    }
                });
            });
            this.$watch('formData.price_mode', (newValue) => {
                if (newValue === 'manual') this.predictionError = '';
                else if (newValue === 'predict') {
                    this.formData.price = '';
                    this.predictionError = 'Mode prediksi aktif. Isi/sesuaikan data dan klik "Dapatkan Prediksi".';
                }
            });
        },

        getTodayDate() { return new Date().toISOString().slice(0,10); }, // Format YYYY-MM-DD

        // Fungsi untuk menangani pemilihan file gambar
        handleImageFiles(event) {
            const files = Array.from(event.target.files);
            // Filter hanya file gambar dan batasi jumlahnya (misal maks 10)
            this.formData.images = files.filter(file => file.type.startsWith('image/')).slice(0, 10);
            if (files.length !== this.formData.images.length) {
                this.ajaxErrors = { ...this.ajaxErrors, images: ['Beberapa file bukan gambar atau melebihi batas unggah (maks 10).'] };
            } else { if (this.ajaxErrors.images) delete this.ajaxErrors.images; }
            event.target.value = null; // Reset input file agar bisa pilih file yang sama lagi
        },

        // Fungsi untuk menghapus gambar dari daftar preview gambar BARU
        removeImage(index) { this.formData.images.splice(index, 1); },

        // Fungsi untuk membuka modal (tambah atau edit)
        openModal(mode, propertyId = null) {
            console.log('Buka modal. Mode:', mode, 'ID:', propertyId);
            this.ajaxErrors = {};
            this.predictionError = '';
            this.currentPropertyId = propertyId; // Set ID properti saat ini

            if (mode === 'add') {
                this.resetAlpineFormData(); // Kosongkan form
                this.modalTitle = 'Tambah Properti Baru';
                this.formAction = '{{ route("data-master.properti.store") }}'; // URL untuk simpan baru
                const formMethodEl = document.getElementById('formMethod');
                if(formMethodEl) formMethodEl.value = 'POST'; // Method untuk form
                this.$dispatch('open-modal', 'propertyFormModal');
            } else if (mode === 'edit' && propertyId) {
                this.resetAlpineFormData(); // Kosongkan form dulu
                this.modalTitle = 'Edit Properti';
                this.formAction = `{{ url('data-master/properti') }}/${propertyId}`; // URL untuk update
                const formMethodEl = document.getElementById('formMethod');
                if(formMethodEl) formMethodEl.value = 'PUT'; // Method untuk form (Laravel akan baca _method)
                this.$dispatch('open-modal', 'propertyFormModal'); // Buka modalnya dulu
                this.fetchPropertyData(propertyId); // Baru ambil data properti untuk diisi ke form
            } else {
                console.warn('Panggilan openModal tidak valid:', mode, propertyId);
            }
        },

        // Fungsi untuk menutup modal utama
        closeModal() {
            this.$dispatch('close-modal', 'propertyFormModal');
            this.currentPropertyId = null; // Reset ID properti
        },

        // Fungsi untuk mengosongkan/mereset data form di Alpine
        resetAlpineFormData() {
            const today = this.getTodayDate();
            this.formData = {
                title: '', Address: '', bedrooms: '', bathrooms: '', price: '',
                sizeMin: '', furnishing: '', propertyType: 'Residential for Sale',
                status: true, mainView: '', propertyLabel: '', description: '',
                addedOn: today, price_mode: 'manual', images: [], existingImages: []
            };
            const imagesUploadInput = document.getElementById('images_upload_input');
            if (imagesUploadInput) imagesUploadInput.value = ''; // Kosongkan juga input file asli
        },

        // Fungsi untuk mengambil data properti dari server (untuk mode edit)
        async fetchPropertyData(id) {
            this.isSubmitting = true; // Bisa pakai loading state sendiri kalau mau
            
            try {
                const baseUrl = '{{ url("data-master/properti") }}';
                const url = `${baseUrl}/${id}/edit-data`; // Route GET untuk ambil data by ID
                console.log('Ambil data edit dari URL:', url);

                const response = await fetch(url);
                if (!response.ok) {
                    let errorData;
                    try { errorData = await response.json(); }
                    catch (e) { errorData = { message: `Gagal mengambil data properti. Status: ${response.status} - ${response.statusText}` }; }
                    throw new Error(errorData.message || `Kesalahan HTTP! status: ${response.status}`);
                }
                const data = await response.json(); // Data properti dari server

                // Isi this.formData dengan data dari server
                this.formData.title = data.title || '';
                this.formData.Address = data.Address || '';
                this.formData.bedrooms = data.bedrooms === null ? '' : data.bedrooms;
                this.formData.bathrooms = data.bathrooms === null ? '' : data.bathrooms;
                this.formData.price = data.price === null ? '' : data.price;
                this.formData.sizeMin = data.sizeMin === null ? '' : data.sizeMin;
                this.formData.furnishing = data.furnishing || '';
                this.formData.propertyType = data.propertyType || 'Residential for Sale';
                this.formData.status = !!data.status; // Konversi 0/1 dari server jadi true/false untuk checkbox
                this.formData.mainView = data.mainView || '';
                this.formData.propertyLabel = data.propertyLabel || '';
                this.formData.description = data.description || '';
                this.formData.addedOn = data.addedOn ? new Date(data.addedOn).toISOString().split('T')[0] : this.getTodayDate();
                this.formData.price_mode = 'manual'; // Default ke manual pas edit

                // Proses gambar yang sudah ada (existingImages)
                let existingImagesFromServer = [];
                if (data.image) { // 'image' dari server bisa jadi array, JSON string, atau string biasa
                    if (Array.isArray(data.image)) { existingImagesFromServer = data.image; }
                    else if (typeof data.image === 'string') {
                        try { existingImagesFromServer = JSON.parse(data.image); }
                        catch (e) { // Kalau bukan JSON valid, coba split pakai koma atau anggap string tunggal
                            if (data.image.includes(',')) { existingImagesFromServer = data.image.split(',').map(s => s.trim()).filter(s => s); }
                            else if (data.image.trim() !== '') { existingImagesFromServer = [data.image.trim()]; }
                        }
                    }
                }
                this.formData.existingImages = existingImagesFromServer.filter(imgUrl => typeof imgUrl === 'string' && imgUrl.length > 0);
                this.formData.images = []; // Kosongkan pilihan gambar baru
            } catch (error) {
                console.error('Error di fetchPropertyData:', error);
                this.ajaxErrors.general.push(error.message || 'Tidak dapat memuat data properti.');
            }
            finally { this.isSubmitting = false; }
        },

        // Fungsi untuk mendapatkan prediksi harga (sesuai kode Abang)
        async getPredictedPrice() {
            this.isPredicting = true;
            this.predictionError = '';
            this.ajaxErrors = {};
            const requiredFieldsForApi = {
                bathrooms: 'bathrooms', bedrooms: 'bedrooms', furnishing: 'furnishing',
                sizeMin: 'sizeMin', verified: 'status', view_type: 'mainView', title_keyword: 'propertyLabel'
            };
            let dataForPrediction = { listing_age_category: 0 };
            let isValid = true;
            let missingFieldsDisplay = [];

            for (const apiKey in requiredFieldsForApi) {
                const formKey = requiredFieldsForApi[apiKey];
                const value = this.formData[formKey];
                if (value === '' || value === null || value === undefined) {
                    const fieldNameMap = { bathrooms: 'Kamar Mandi', bedrooms: 'Kamar Tidur', furnishing: 'Perabotan', sizeMin: 'Luas', status: 'Status Verifikasi', mainView: 'Pemandangan Utama', propertyLabel: 'Label Properti'};
                    missingFieldsDisplay.push(fieldNameMap[formKey] || formKey);
                    isValid = false;
                }
                if (!isValid) continue;
                if (['bathrooms', 'bedrooms', 'sizeMin'].includes(formKey)) {
                    if (isNaN(parseFloat(value))) { this.predictionError = `Field '${formKey}' harus angka.`; isValid = false; break; }
                    dataForPrediction[apiKey] = parseFloat(value);
                } else if (formKey === 'status') {
                    dataForPrediction.verified = this.formData.status ? 1 : 0;
                } else if (formKey === 'furnishing') {
                    dataForPrediction.furnishing = this.furnishingMapForPrediction[value];
                    if (dataForPrediction.furnishing === undefined) { this.predictionError = "Status Perabotan tidak valid."; isValid = false; break; }
                } else if (formKey === 'mainView') {
                    dataForPrediction.view_type = this.viewTypeMapForPrediction[value];
                    if (dataForPrediction.view_type === undefined) { this.predictionError = "Tipe Pemandangan tidak valid."; isValid = false; break; }
                } else if (formKey === 'propertyLabel') {
                    dataForPrediction.title_keyword = this.keywordMapForPrediction[value];
                    if (dataForPrediction.title_keyword === undefined) { this.predictionError = "Label Properti tidak valid."; isValid = false; break; }
                }
            }
            if (missingFieldsDisplay.length > 0) {
                this.predictionError = `Field berikut diperlukan untuk prediksi: ${missingFieldsDisplay.join(', ')}.`;
                isValid = false;
            }
            if (!isValid) { this.isPredicting = false; return; }

            console.log('Kirim data untuk prediksi:', JSON.stringify(dataForPrediction));
            try {
                const response = await fetch('http://localhost:5000/prediksi/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(dataForPrediction)
                });
                const result = await response.json();
                console.log('Respons API prediksi:', result);
                if (!response.ok) {
                    this.predictionError = result.error || result.message || `Gagal mendapatkan prediksi (Status: ${response.status})`;
                    this.formData.price = '';
                } else {
                    this.formData.price = parseFloat(result.prediction_result).toFixed(0);
                    this.predictionError = 'Prediksi harga berhasil dimuat.';
                }
            } catch (error) {
                console.error('Error panggil API prediksi:', error);
                this.predictionError = 'Gagal terhubung ke layanan prediksi. Pastikan layanan aktif, CORS dikonfigurasi, dan semua field terisi benar.';
                this.formData.price = '';
            } finally {
                this.isPredicting = false;
            }
        },

        // *** INI FUNGSI UTAMA UNTUK SIMPAN/UPDATE DATA ***
        async submitForm() {
            this.isSubmitting = true;
            this.ajaxErrors = {};
            // this.predictionError = ''; // Bisa dikosongkan juga di sini
            // this.deleteError = '';

            const formElement = document.getElementById('propertyForm');
            const formDataToSend = new FormData(formElement); // Ambil _token dan _method dari form

            // Set field lain dari this.formData ke formDataToSend
            // Ini lebih aman karena this.formData adalah "single source of truth"
            formDataToSend.set('title', this.formData.title);
            formDataToSend.set('Address', this.formData.Address);
            formDataToSend.set('bedrooms', this.formData.bedrooms);
            formDataToSend.set('bathrooms', this.formData.bathrooms);
            formDataToSend.set('price', this.formData.price);
            formDataToSend.set('sizeMin', this.formData.sizeMin);
            formDataToSend.set('furnishing', this.formData.furnishing);
            formDataToSend.set('propertyType', this.formData.propertyType);
            formDataToSend.set('mainView', this.formData.mainView);
            formDataToSend.set('propertyLabel', this.formData.propertyLabel);
            formDataToSend.set('description', this.formData.description);
            formDataToSend.set('addedOn', this.formData.addedOn);

            // Konversi status boolean ke '1' atau '0'
            formDataToSend.set('status', this.formData.status ? '1' : '0');

            // Hapus dulu file 'images[]' yang mungkin sudah ada di formDataToSend dari input file asli
            // karena kita akan append dari this.formData.images
            formDataToSend.delete('images[]');

            // Tambahkan file gambar BARU yang dipilih
            if (this.formData.images && this.formData.images.length > 0) {
                this.formData.images.forEach((file) => {
                    formDataToSend.append('images[]', file, file.name);
                });
            }

            // Untuk mode edit, pastikan _method adalah PUT (sudah di-set di hidden input oleh openModal)
            // Jika mau lebih eksplisit:
            // if (this.currentPropertyId) {
            //     formDataToSend.set('_method', 'PUT');
            // } else {
            //     formDataToSend.set('_method', 'POST');
            // }
            // Tapi karena `new FormData(formElement)` sudah mengambilnya, ini tidak wajib.

            console.log('Data yang akan dikirim ke server:', Object.fromEntries(formDataToSend));
            // Untuk debug file:
            // for (let pair of formDataToSend.entries()) {
            //    console.log(pair[0]+ ', ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
            // }

            try {
                const response = await fetch(this.formAction, {
                    method: 'POST', // Selalu POST untuk FormData dengan _method
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formDataToSend,
                });

                const result = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && result.errors) {
                        this.ajaxErrors = result.errors;
                        if (result.message) this.ajaxErrors.general = [result.message];
                         // Cari field error pertama dan fokus ke sana (opsional)
                        const firstErrorField = Object.keys(result.errors)[0];
                        if (firstErrorField) {
                            const el = document.getElementById(firstErrorField + '_form'); // Asumsi ID input: namafield_form
                            if (el) el.focus();
                        }
                    } else {
                        this.ajaxErrors = { general: [result.message || `Terjadi error (Status: ${response.status}). Coba lagi.`] };
                    }
                    return; // Stop kalau ada error
                }

                // Jika SUKSES
                this.closeModal();
                // Notifikasi sukses bisa pakai session flash Laravel dengan reload, atau notif Alpine.
                // Untuk sementara, reload saja:
                window.location.reload();
                // Contoh notifikasi Alpine (perlu komponen notifikasi terpisah):
                // this.$dispatch('shownotification', { message: result.message || 'Data berhasil disimpan!', type: 'success' });


            } catch (error) {
                console.error('Error submit form:', error);
                this.ajaxErrors = { general: ['Tidak bisa terhubung ke server. Cek koneksi dan coba lagi.'] };
            } finally {
                this.isSubmitting = false;
            }
        },

        // Fungsi untuk membuka modal konfirmasi hapus
        openDeleteModal(url, id, name) {
            this.propertyToDeleteUrl = url;
            this.propertyToDeleteId = id;
            this.propertyToDeleteName = name || 'Properti Ini';
            this.deleteError = ''; // Kosongkan error sebelumnya
            this.ajaxErrors = {}; // Kosongkan error validasi AJAX sebelumnya
            this.$dispatch('open-modal', 'deleteConfirmModal');
        },

        closeDeleteModal() {
            this.$dispatch('close-modal', 'deleteConfirmModal');
        },

        // Fungsi untuk konfirmasi dan eksekusi hapus
        async confirmDelete() {
            if (!this.propertyToDeleteUrl) return;
            this.isDeleting = true;
            this.deleteError = '';
            this.ajaxErrors = {};

            try {
                const response = await fetch(this.propertyToDeleteUrl, {
                    method: 'POST', // Gunakan POST dan _method: 'DELETE' jika form based
                                   // atau bisa langsung 'DELETE' jika server support
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json', // Jika kirim body JSON
                    },
                    body: JSON.stringify({ // Kirim _method sebagai JSON body jika pakai POST
                        _method: 'DELETE'
                    })
                    // Jika pakai FormData:
                    // body: new FormData(document.getElementById('deleteForm')) // jika ada form khusus delete
                    // atau buat FormData manual:
                    // const deleteFormData = new FormData();
                    // deleteFormData.append('_method', 'DELETE');
                    // body: deleteFormData,
                });

                const result = await response.json();

                if (!response.ok) {
                    this.deleteError = result.message || `Gagal menghapus (Status: ${response.status})`;
                    // Jika ada error validasi dari backend (jarang untuk delete, tapi mungkin)
                    if (result.errors) this.ajaxErrors = result.errors;
                    return;
                }

                // Jika SUKSES HAPUS
                this.closeDeleteModal();
                // Reload halaman untuk update daftar
                window.location.reload();
                // Atau bisa update UI secara manual & tampilkan notifikasi
                // this.$dispatch('shownotification', { message: result.message || 'Data berhasil dihapus!', type: 'success' });

            } catch (error) {
                console.error('Error hapus properti:', error);
                this.deleteError = 'Tidak bisa terhubung ke server untuk menghapus data.';
            } finally {
                this.isDeleting = false;
            }
        }
    }
}
</script>
@endpush
</x-app-layout>