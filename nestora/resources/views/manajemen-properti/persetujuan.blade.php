<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Persetujuan Iklan Properti') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Form Pencarian --}}
                <div class="mb-6">
                    <form method="GET" action="{{ route('manajemen-properti.persetujuan') }}">
                        <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <div class="flex-grow w-full sm:w-auto">
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
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">No</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Address</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Bedrooms</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Bathrooms</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price (AED)</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Size (sqft)</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Furnishing</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Added On</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($properties as $index => $property)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $properties->firstItem() + $index }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $property->title }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $property->address }}</td>
                                    <td class="px-4 py-2 text-sm text-center text-gray-900 dark:text-gray-100">{{ $property->bedrooms }}</td>
                                    <td class="px-4 py-2 text-sm text-center text-gray-900 dark:text-gray-100">{{ $property->bathrooms }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">AED {{ number_format($property->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-center text-gray-900 dark:text-gray-100">{{ $property->size ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $property->furnishing ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $property->type }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($property->created_at)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-yellow-500 dark:text-yellow-400">{{ ucfirst($property->status) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <div class="flex space-x-2">
                                            <form action="{{ route('manajemen-properti.approve', $property->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded">Approve</button>
                                            </form>
                                            <form action="{{ route('manajemen-properti.reject', $property->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        @if(request('search'))
                                            Tidak ditemukan properti untuk pencarian: "{{ request('search') }}"
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
