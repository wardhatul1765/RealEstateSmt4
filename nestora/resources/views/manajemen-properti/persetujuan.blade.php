<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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

                {{-- Form Pencarian (Opsional untuk halaman persetujuan, bisa disesuaikan jika diperlukan) --}}
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bedrooms</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bathrooms</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price (AED)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size (sqft)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Furnishing</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Added On</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Menggunakan $properties dari controller, yang seharusnya berisi properti dengan status false --}}
                            @forelse ($properties as $index => $property)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $properties->firstItem() + $index }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($property->title, 30) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($property->Address, 35) }}</td> {{-- Disesuaikan dari address ke Address --}}
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $property->bedrooms }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $property->bathrooms }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $property->sizeMin ?? '-' }}</td> {{-- Disesuaikan dari size ke sizeMin --}}
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @php
                                            $displayFurnishing = '-'; // Default value
                                            if (!empty($property->furnishing)) {
                                                $furnishingValue = strtoupper($property->furnishing);
                                                if ($furnishingValue === 'YES') {
                                                    $displayFurnishing = 'Yes';
                                                } elseif ($furnishingValue === 'NO') {
                                                    $displayFurnishing = 'No';
                                                } elseif ($furnishingValue === 'PARTLY') {
                                                    $displayFurnishing = 'Partly';
                                                } else {
                                                    $displayFurnishing = $property->furnishing; // Tampilkan nilai asli jika tidak dikenali
                                                }
                                            }
                                        @endphp
                                        {{ $displayFurnishing }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $property->propertyType }}</td> {{-- Disesuaikan dari type ke propertyType --}}
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{-- Menggunakan addedOn jika ada, jika tidak created_at --}}
                                        @if($property->addedOn)
                                            {{ \Carbon\Carbon::parse($property->addedOn)->format('d M Y H:i') }}
                                        @elseif($property->created_at)
                                            {{ \Carbon\Carbon::parse($property->created_at)->format('d M Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-yellow-600 dark:text-yellow-400">
                                        {{-- Karena halaman ini untuk persetujuan, statusnya diasumsikan 'Menunggu Persetujuan' --}}
                                        {{-- Controller akan memfilter status=false --}}
                                        Menunggu Persetujuan
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <form action="{{ route('manajemen-properti.approve', $property->id ?? $property->_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH') {{-- Atau PUT, sesuaikan dengan route Anda --}}
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">Approve</button>
                                            </form>
                                            <form action="{{ route('manajemen-properti.reject', $property->id ?? $property->_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH') {{-- Atau PUT, sesuaikan dengan route Anda --}}
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
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
</x-app-layout>
