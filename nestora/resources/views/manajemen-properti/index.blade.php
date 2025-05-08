<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Properti') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada data properti.</td>
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