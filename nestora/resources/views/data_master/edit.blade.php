<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Properti') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('data-master.properti.update', $property->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('data_master._form', ['button' => 'Update', 'property' => $property])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
