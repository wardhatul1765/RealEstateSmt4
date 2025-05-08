<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Properti') }}
            </h2>
            {{-- Tombol Tambah Properti bisa ditambahkan di sini jika diperlukan --}}
            {{-- <a href="{{ route('manajemen-properti.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Tambah Properti') }}
            </a> --}}
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
                                       class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
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


                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bedrooms</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bathrooms</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price (AED)</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size (sqft)</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Furnishing</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Added On</th>
                                {{-- Kolom Aksi bisa ditambahkan di sini jika diperlukan untuk CRUD --}}
                                {{-- <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($dataProperty as $index => $property)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $dataProperty->firstItem() + $index }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($property->title, 30) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($property->displayAddress, 35) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">{{ $property->bedrooms }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">{{ $property->bathrooms }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{-- Menggunakan accessor getFurnishingAttribute dari model Property --}}
                                        {{ $property->furnishing ? 'Yes' : 'No' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $property->type }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{-- Pastikan addedOn adalah instance Carbon atau bisa di-parse --}}
                                        @if($property->addedOn instanceof \Carbon\Carbon)
                                            {{ $property->addedOn->format('d M Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($property->addedOn)->format('d M Y') }}
                                        @endif
                                    </td>
                                    {{-- Kolom Aksi bisa ditambahkan di sini --}}
                                    {{-- <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('manajemen-properti.show', $property->_id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">Lihat</a>
                                        <a href="{{ route('manajemen-properti.edit', $property->_id) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-200 mr-2">Edit</a>
                                        <form action="{{ route('manajemen-properti.destroy', $property->_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus properti ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">Hapus</button>
                                        </form>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
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
                    {{-- Pastikan pagination links menyertakan query pencarian --}}
                    {{ $dataProperty->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
