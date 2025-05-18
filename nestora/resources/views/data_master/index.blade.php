<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Properti') }}
        </h2>
    </x-slot>

    {{-- Alpine.js Data Scope untuk halaman ini --}}
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
                                <th class="px-4 py-2 text-left text-xs font-semibold">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Address</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Bedrooms</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Bathrooms</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Price (AED)</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Size (sqft)</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Furnishing</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold">Added On</th>
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
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($property->addedOn)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <button @click="openModal('edit', '{{ $property->id }}')"
                                           class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Edit
                                        </button>
                                        <form action="{{ route('data-master.properti.destroy', $property->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-2 text-center">Tidak ada data properti.</td>
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

        {{-- Modal Component --}}
        <x-modal name="propertyFormModal" :maxWidth="'2xl'">
            <div class="p-6 bg-gray-800 text-white">
                <h2 class="text-lg font-medium mb-4" x-text="modalTitle"></h2>

                <div x-show="Object.keys(ajaxErrors).length > 0" class="mb-4 p-3 bg-red-700 text-white rounded-md">
                    <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                    <ul class="list-disc list-inside mt-1">
                        <template x-for="(errorMessages, field) in ajaxErrors" :key="field">
                            <template x-for="message in errorMessages" :key="message">
                                <li x-text="message"></li>
                            </template>
                        </template>
                    </ul>
                </div>

                <form @submit.prevent="submitForm" id="propertyForm" method="POST" action="#">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="grid gap-4">
                        <div>
                            <label for="title" class="block text-sm text-gray-300">Title</label>
                            <input type="text" name="title" id="title" x-model="formData.title"
                                class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="bedrooms" class="block text-sm text-gray-300">Bedrooms</label>
                            <input type="number" name="bedrooms" id="bedrooms" x-model="formData.bedrooms"
                                class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="bathrooms" class="block text-sm text-gray-300">Bathrooms</label>
                            <input type="number" name="bathrooms" id="bathrooms" x-model="formData.bathrooms"
                                class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="price" class="block text-sm text-gray-300">Price (AED)</label>
                            <input type="number" name="price" id="price" x-model="formData.price"
                                class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="furnishing" class="block text-sm text-gray-300">Furnishing</label>
                            <select name="furnishing" id="furnishing" x-model="formData.furnishing"
                                class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih...</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label for="displayAddress" class="block text-sm text-gray-300">Address</label>
                            <input type="text" name="displayAddress" id="displayAddress" x-model="formData.displayAddress"
                                class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="sizeMin" class="block text-sm text-gray-300">Size (sqft)</label>
                            <input type="number" name="sizeMin" id="sizeMin" x-model="formData.sizeMin"
                                class="w-full bg-gray-700 border border-gray-600 rounded p-2 text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="verified" id="verified" value="1" x-model="formData.verified"
                                   class="rounded border-gray-600 text-purple-600 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                            <label for="verified" class="text-sm text-gray-300">Verified</label>
                        </div>
                        <input type="hidden" name="type" x-model="formData.type" value="Residential for Sale">
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button type="button" @click="closeModal()">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button class="ms-3" type="submit" x-text="submitButtonText"
                                          x-bind:disabled="isSubmitting">
                        </x-primary-button>
                    </div>
                </form>
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
                ajaxErrors: {},
                currentPropertyId: null,
                formData: {
                    title: '', bedrooms: '', bathrooms: '', price: '',
                    furnishing: '', displayAddress: '', sizeMin: '',
                    verified: false, type: 'Residential for Sale'
                },

                init() {
                    // Tidak perlu console.log di sini jika tidak untuk debugging aktif
                },

                openModal(mode, propertyId = null) {
                    this.ajaxErrors = {};
                    this.currentPropertyId = propertyId;
                    document.getElementById('propertyForm').reset();
                    this.resetAlpineFormData();

                    if (mode === 'add') {
                        this.modalTitle = 'Tambah Properti Baru';
                        this.submitButtonText = 'Simpan';
                        this.formAction = '{{ route("data-master.properti.store") }}';
                        document.getElementById('formMethod').value = 'POST';
                    } else if (mode === 'edit' && propertyId) {
                        this.modalTitle = 'Edit Properti';
                        this.submitButtonText = 'Update';
                        this.formAction = {{ url('data-master/properti') }}/${propertyId};
                        document.getElementById('formMethod').value = 'PUT';
                        this.fetchPropertyData(propertyId); // fetchPropertyData akan menangani isSubmitting
                    }
                    this.$dispatch('open-modal', 'propertyFormModal');
                },

                closeModal() {
                    this.$dispatch('close-modal', 'propertyFormModal');
                    this.resetAlpineFormData();
                    this.currentPropertyId = null;
                    this.ajaxErrors = {};
                },

                resetAlpineFormData() {
                    this.formData = {
                        title: '', bedrooms: '', bathrooms: '', price: '',
                        furnishing: '', displayAddress: '', sizeMin: '',
                        verified: false, type: 'Residential for Sale'
                    };
                },

                async fetchPropertyData(id) {
                    this.isSubmitting = true;
                    try {
                        let url = {{ route('data-master.properti.edit-data', ['id' => '_PLACEHOLDER_']) }};
                        url = url.replace('PLACEHOLDER', id);
                        const response = await fetch(url);
                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({ message: 'Gagal mengambil data properti (respons server tidak valid atau bukan JSON).' }));
                            throw new Error(errorData.message || HTTP error! status: ${response.status});
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
                    } catch (error) {
                        console.error('Error fetching property data:', error);
                        // Tidak menampilkan alert di sini agar tidak mengganggu jika fetch gagal saat buka modal
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async submitForm() {
                    this.isSubmitting = true;
                    this.ajaxErrors = {}; // Kosongkan error validasi sebelumnya
                    const formElement = document.getElementById('propertyForm');
                    const csrfToken = formElement.querySelector('input[name="_token"]').value;
                    let dataToSend = new FormData();

                    for (const key in this.formData) {
                        if (key === 'verified') {
                            dataToSend.append(key, this.formData[key] ? '1' : '0');
                        } else {
                            dataToSend.append(key, this.formData[key] === null ? '' : this.formData[key]);
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

                        // Jika respons BUKAN JSON (misalnya halaman error HTML dari server), .json() akan error
                        // Kita akan langsung reload jika status bukan 422 (error validasi)
                        if (response.status === 422) {
                            const responseData = await response.json(); // Hanya parse JSON jika 422
                            this.ajaxErrors = responseData.errors;
                            this.isSubmitting = false;
                            return; // Hentikan eksekusi agar modal tetap terbuka dan error tampil
                        }

                        // Untuk status sukses (200-299) atau error server lain (500, dll)
                        // kita akan langsung reload halaman.
                        // Tidak perlu alert sukses lagi, langsung reload.
                        this.closeModal(); // Tutup modal sebelum reload
                        window.location.reload();

                    } catch (error) {
                        // Ini akan menangkap error jaringan atau jika .json() gagal (misalnya server tidak return JSON)
                        console.error('Submit error:', error);
                        this.isSubmitting = false;
                        // Untuk error jaringan atau server yang tidak terduga, kita tetap reload
                        // agar pengguna tidak terjebak di modal.
                        this.closeModal();
                        window.location.reload();
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
