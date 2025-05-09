<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Properti') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                {{-- Pencarian --}}
                <div class="flex justify-between items-center mb-4">
                    <form action="{{ route('data-master.properti.index') }}" method="GET">
                        <input type="text" name="search" placeholder="Cari berdasarkan judul atau alamat..." 
                               class="px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                        <button type="submit" 
                                class="px-4 py-2 ml-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Cari
                        </button>
                    </form>
                    <a href="{{ route('data-master.properti.create') }}"
                       class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        + Tambah Properti
                    </a>
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
                                <tr>
                                    <td class="px-4 py-2">{{ $dataProperty->firstItem() + $index }}</td>
                                    <td class="px-4 py-2">{{ $property->title }}</td>
                                    <td class="px-4 py-2">{{ $property->displayAddress }}</td>
                                    <td class="px-4 py-2">{{ $property->bedrooms }}</td>
                                    <td class="px-4 py-2">{{ $property->bathrooms }}</td>
                                    <td class="px-4 py-2">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $property->furnishing ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-2">{{ $property->type }}</td>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($property->addedOn)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <a href="{{ route('data-master.properti.edit', $property->id) }}"
                                           class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Edit
                                        </a>
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

                {{-- Pagination --}}
                <div class="mt-4 text-white">
                    {{ $dataProperty->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
