<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Properti') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                {{-- Tombol Tambah Properti --}}
                <div class="mb-4">
                    <a href="{{ route('data-master.properti.create') }}"
                       class="inline-block px-4 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600">
                        + Tambah Properti
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bedrooms</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bathrooms</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price (AED)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Size (sqft)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Furnishing</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Added On</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($dataProperty as $index => $property)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $dataProperty->firstItem() + $index }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $property->title }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $property->displayAddress }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $property->bedrooms }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $property->bathrooms }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $property->sizeMin ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $property->furnishing ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $property->type }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($property->addedOn)->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900 space-x-2">
                                        <a href="{{ route('data-master.properti.edit', $property->id) }}"
                                           class="inline-block px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                            Edit
                                        </a>
                                        <form action="{{ route('data-master.properti.destroy', $property->id) }}" method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada data properti.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $dataProperty->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
