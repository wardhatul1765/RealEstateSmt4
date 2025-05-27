<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Properti') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Form Pencarian --}}
                <div class="mb-6">
                    <form method="GET" action="{{ route('manajemen-properti.index') }}">
                        <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <div class="flex-grow w-full sm:w-auto">
                                <label for="search" class="sr-only">{{ __('Cari Properti') }}</label>
                                <input type="text" name="search" id="search"
                                       class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 text-sm"
                                       placeholder="Cari berdasarkan judul atau alamat..."
                                       value="{{ request('search') }}">
                            </div>
                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cari') }}
                            </button>
                            @if(request('search'))
                                <a href="{{ route('manajemen-properti.index') }}"
                                   class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Reset') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Menampilkan pesan sukses atau info --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('info'))
                    <div class="mb-4 p-4 bg-blue-100 dark:bg-blue-700 border border-blue-400 dark:border-blue-600 text-blue-700 dark:text-blue-200 rounded text-sm">
                        {{ session('info') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif


                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                {{-- Mengubah padding dan font size header agar lebih kecil --}}
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                <th scope="col" class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bed</th>
                                <th scope="col" class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bath</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Furn.</th>
                                <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <!-- <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Added</th> -->
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">View</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Label</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Desc.</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Updated</th>
                                {{-- <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($dataProperty as $index => $property)
                                <tr>
                                    {{-- Mengubah padding dan font size data sel agar lebih kecil --}}
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">{{ $dataProperty->firstItem() + $index }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{$property->title}}">{{ Str::limit($property->title, 15) }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{$property->Address}}">{{ Str::limit($property->Address, 20) }}</td>
                                    <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $property->bedrooms }}</td>
                                    <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $property->bathrooms }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                                        @php
                                            $displayFurnishing = '-';
                                            if (!empty($property->furnishing)) {
                                                $furnishingValue = strtoupper($property->furnishing);
                                                if ($furnishingValue === 'YES') $displayFurnishing = 'Y';
                                                elseif ($furnishingValue === 'NO') $displayFurnishing = 'N';
                                                elseif ($furnishingValue === 'PARTLY') $displayFurnishing = 'P';
                                                else $displayFurnishing = Str::limit(ucfirst(strtolower($property->furnishing)), 1);
                                            }
                                        @endphp
                                        {{ $displayFurnishing }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100 text-center">
                                        @if($property->status === 'verified' || $property->status === true || $property->status === 1 || $property->status === '1')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                Verified
                                            </span>
                                        @elseif($property->status === 'pending')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                                Pending
                                            </span>
                                        @elseif($property->status === 'rejected')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                                {{ $property->status ? Str::limit(ucfirst(strtolower($property->status)),3) : '-' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{$property->propertyType}}">{{ Str::limit($property->propertyType,10) }}</td>
                                    <!-- <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                                        @if($property->addedOn)
                                            {{ \Carbon\Carbon::parse($property->addedOn)->format('d/m/y') }}
                                        @else
                                            -
                                        @endif
                                    </td> -->
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->mainView }}">{{ Str::limit($property->mainView,10) ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->propertyLabel }}">{{ Str::limit($property->propertyLabel,10) ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                                        @php
                                            $images = [];
                                            if (is_string($property->image)) {
                                                $decodedImages = json_decode($property->image, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                                                    $images = $decodedImages;
                                                } elseif (!empty($property->image)) {
                                                    if (strpos($property->image, ',') !== false) {
                                                        $images = array_map('trim', explode(',', $property->image));
                                                    } else {
                                                         $images = [$property->image];
                                                    }
                                                }
                                            } elseif (is_array($property->image)) {
                                                $images = $property->image;
                                            }
                                            $images = array_values(array_filter($images)); // Re-index after filter
                                        @endphp

                                        @if(!empty($images))
                                            <div class="flex items-center">
                                                <img src="{{ $images[0] }}" alt="Thumb"
                                                     class="h-6 w-6 object-cover rounded" {{-- DIHAPUS: cursor-pointer open-image-gallery --}}
                                                     {{-- DIHAPUS: data-images="{{ htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8') }}" --}}
                                                     onerror="this.style.display='none'; if(this.nextElementSibling && this.nextElementSibling.tagName === 'SPAN') { this.nextElementSibling.classList.remove('ml-1'); this.nextElementSibling.textContent = 'N/A'; } else { const naSpan = document.createElement('span'); naSpan.textContent = ' N/A'; this.parentElement.appendChild(naSpan); }">
                                                {{-- Tombol diganti dengan span untuk count --}}
                                                <span class="text-xs ml-1">({{ count($images) }})</span>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100" title="{{ $property->description }}">{{ Str::limit($property->description, 15) ?? '-' }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                                        @if($property->created_at)
                                            {{ \Carbon\Carbon::parse($property->created_at)->format('d/m/y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                                        @if($property->updated_at)
                                            {{ \Carbon\Carbon::parse($property->updated_at)->format('d/m/y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    {{-- <td class="px-2 py-1 whitespace-nowrap text-xs font-medium">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        @if(request('search'))
                                            Pencarian untuk "{{ request('search') }}" tidak menemukan hasil.
                                        @else
                                            Tidak ada data properti.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $dataProperty->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Galeri Gambar DIHAPUS/DIKOMENTARI --}}
    {{--
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
    --}}
</x-app-layout>

@push('scripts')
{{-- JavaScript untuk modal galeri DIHAPUS/DIKOMENTARI --}}
{{--
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('imageGalleryModal');
    const galleryImage = document.getElementById('galleryImage');
    const closeButton = document.getElementById('closeGalleryModal');
    const prevButton = document.getElementById('prevImage');
    const nextButton = document.getElementById('nextImage');
    const imageCounter = document.getElementById('imageCounter');
    const galleryTriggers = document.querySelectorAll('.open-image-gallery');

    let currentImages = [];
    let currentIndex = 0;

    function showImage(index) {
        if (currentImages.length === 0 || index < 0 || index >= currentImages.length) {
            // console.warn('Invalid index or no images to show:', index, currentImages);
            if (currentImages.length === 0 && !modal.classList.contains('hidden')) {
                closeModal();
            }
            return;
        }
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

    galleryTriggers.forEach(trigger => {
        trigger.addEventListener('click', function () {
            const imagesData = this.dataset.images;
            if (imagesData) {
                try {
                    currentImages = JSON.parse(imagesData);
                    if (Array.isArray(currentImages) && currentImages.length > 0) {
                        currentIndex = 0;
                        showImage(currentIndex);
                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    } else {
                        // console.warn('No images found or data is not a valid array after parsing.');
                        currentImages = [];
                    }
                } catch (e) {
                    // console.error('Error parsing image data:', e, "\nData:", imagesData);
                    currentImages = [];
                }
            } else {
                // console.warn('No image data found on trigger element.');
            }
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        currentImages = [];
        galleryImage.src = ""; // Clear image source
    }

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
--}}
@endpush