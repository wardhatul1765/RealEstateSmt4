<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Properti') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('data_master.index') }}"
                   class="inline-block px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                    ← Kembali
                </a>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('data_master.store') }}" method="POST">
                    @csrf
                    @include('data_master._form', ['button' => 'Simpan'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
