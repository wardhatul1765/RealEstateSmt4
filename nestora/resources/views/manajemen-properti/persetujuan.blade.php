<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Persetujuan Iklan Properti') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Menampilkan pesan sukses atau info --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('info'))
                    <div class="mb-4 p-4 bg-blue-100 dark:bg-blue-700 border border-blue-400 dark:border-blue-600 text-blue-700 dark:text-blue-200 rounded">
                        {{ session('info') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Form Pencarian --}}
                <div class="mb-6">
                    <form method="GET" action="{{ route('manajemen-properti.persetujuan') }}">
                        <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <div class="flex-grow w-full sm:w-auto">
                                <label for="search" class="sr-only">{{ __('Cari Properti') }}</label>
                                <input type="text" name="search" id="search"
                                    class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
                                    placeholder="Cari berdasarkan judul atau alamat..."
                                    value="{{ request('search') }}">
                            </div>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300">
                                {{ __('Cari') }}
                            </button>
                            @if(request('search'))
                                <a href="{{ route('manajemen-properti.persetujuan') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300">
                                    {{ __('Reset') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Beds</th>
                                <th class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Baths</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Furn.</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">View</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Label</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Desc.</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($properties as $index => $property)
                                <tr>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $properties->firstItem() + $index }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->title }}">{{ Str::limit($property->title, 20) }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->address }}">{{ Str::limit($property->address, 22) }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->propertyType }}">{{ Str::limit($property->propertyType, 12) }}</td>
                                    <td class="px-1 py-1 whitespace-nowrap text-xs text-center text-gray-900 dark:text-gray-100">{{ $property->bedrooms }}</td>
                                    <td class="px-1 py-1 whitespace-nowrap text-xs text-center text-gray-900 dark:text-gray-100">{{ $property->bathrooms }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-center text-gray-900 dark:text-gray-100">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                                        @php
                                            $displayFurnishing = '-';
                                            if (!empty($property->furnishing)) {
                                                $furnishingValue = strtoupper($property->furnishing);
                                                if ($furnishingValue === 'YES') $displayFurnishing = 'Y';
                                                elseif ($furnishingValue === 'NO') $displayFurnishing = 'N';
                                                elseif ($furnishingValue === 'PARTLY') $displayFurnishing = 'P';
                                                else $displayFurnishing = Str::limit($property->furnishing, 1);
                                            }
                                        @endphp
                                        {{ $displayFurnishing }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->mainView }}">{{ Str::limit($property->mainView, 10) ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->propertyLabel }}">{{ Str::limit($property->propertyLabel, 10) ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->description }}">
                                        {{ Str::limit($property->description, 15) ?? '-' }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                                        @php
                                            $images = [];
                                            if (is_string($property->image)) {
                                                $decodedImages = json_decode($property->image, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                                                    $images = $decodedImages;
                                                } elseif (!empty($property->image)) {
                                                    $images = [$property->image];
                                                }
                                            } elseif (is_array($property->image)) {
                                                $images = $property->image;
                                            }
                                        @endphp

                                        @if(!empty($images))
                                            <div class="flex items-center space-x-2">
                                                <img 
                                                    src="{{ asset('storage/properties/' . $images[0]) }}" 
                                                    alt="Property image thumbnail"
                                                    class="h-8 w-8 object-cover rounded cursor-pointer open-image-gallery"
                                                    data-images="{{ htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8') }}">
                                                
                                                <button type="button"
                                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 text-xs open-image-gallery"
                                                        data-images="{{ htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8') }}">
                                                    ({{ count($images) }})
                                                </button>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-yellow-600 dark:text-yellow-400">
                                        Pending
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-1">
                                            <form action="{{ route('manajemen-properti.approve', $property->id ?? $property->_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI properti ini?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-2 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">Appr</button>
                                            </form>
                                            <form action="{{ route('manajemen-properti.reject', $property->id ?? $property->_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK properti ini?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">Rej</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        @if(request('search'))
                                            Tidak ditemukan properti untuk pencarian: "{{ request('search') }}" yang menunggu persetujuan.
                                        @else
                                            Belum ada properti yang menunggu persetujuan.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $properties->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Galeri Gambar (Tidak ada perubahan) --}}
    <div id="imageGalleryModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-xl relative max-w-xl w-full max-h-[90vh] flex flex-col">
            <button id="closeGalleryModal" class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 text-2xl leading-none">&times;</button>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4" id="galleryTitle">Galeri Gambar</h3>
            <div class="flex-grow overflow-hidden flex items-center justify-center mb-4">
                <img id="galleryImage" src="" alt="Property Image" class="max-w-full max-h-[60vh] object-contain">
            </div>
            <div class="flex justify-between items-center">
                <button id="prevImage" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">&lt; Prev</button>
                <span id="imageCounter" class="text-sm text-gray-700 dark:text-gray-300"></span>
                <button id="nextImage" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Next &gt;</button>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')

{{-- === [START] JAVASCRIPT DENGAN PERBAIKAN === --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('imageGalleryModal');
    const galleryImage = document.getElementById('galleryImage');
    const closeButton = document.getElementById('closeGalleryModal');
    const prevButton = document.getElementById('prevImage');
    const nextButton = document.getElementById('nextImage');
    const imageCounter = document.getElementById('imageCounter');

    let currentImages = [];
    let currentIndex = 0;

    function showImage(index) {
        if (!currentImages || currentImages.length === 0) return;
        
        galleryImage.src = currentImages[index];
        imageCounter.textContent = `${index + 1} / ${currentImages.length}`;
        prevButton.disabled = index === 0;
        nextButton.disabled = index === currentImages.length - 1;

        if (currentImages.length <= 1) {
            prevButton.classList.add('hidden');
            nextButton.classList.add('hidden');
            imageCounter.classList.add('hidden');
        } else {
            prevButton.classList.remove('hidden');
            nextButton.classList.remove('hidden');
            imageCounter.classList.remove('hidden');
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        currentImages = [];
    }

    // === PERBAIKAN: Menggunakan Event Delegation ===
    document.body.addEventListener('click', function(event) {
        // Cek apakah elemen yang di-klik atau parent-nya memiliki kelas .open-image-gallery
        const trigger = event.target.closest('.open-image-gallery');

        if (trigger) {
            event.preventDefault(); // Mencegah aksi default jika trigger adalah link
            const imagesData = trigger.dataset.images;
            
            if (imagesData) {
                try {
                    currentImages = JSON.parse(imagesData);
                    if (Array.isArray(currentImages) && currentImages.length > 0) {
                        currentIndex = 0;
                        showImage(currentIndex);
                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    }
                } catch (e) {
                    console.error('Gagal mem-parsing data gambar:', e);
                }
            }
        }
    });

    // Listener untuk kontrol modal (Tombol close, prev, next, dll.)
    closeButton.addEventListener('click', closeModal);

    prevButton.addEventListener('click', function () {
        if (currentIndex > 0) {
            currentIndex--;
            showImage(currentIndex);
        }
    });

    nextButton.addEventListener('click', function () {
        if (currentIndex < currentImages.length - 1) {
            currentIndex++;
            showImage(currentIndex);
        }
    });

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
{{-- === [END] JAVASCRIPT DENGAN PERBAIKAN === --}}
@endpush