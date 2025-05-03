<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Menampilkan nama pengguna yang diedit --}}
            {{ __('Edit Pengguna: ') }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-6">Form Edit Pengguna</h3>

                    {{-- Form untuk mengirim data ke route 'data-user.update' --}}
                    <form method="POST" action="{{ route('data-user.update', $user) }}"> {{-- Menggunakan $user untuk route model binding --}}
                        @csrf
                        @method('PATCH') {{-- Gunakan method PATCH (atau PUT) untuk update --}}

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama')" />
                            {{-- Menampilkan nilai lama atau nilai dari $user --}}
                            <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-700 dark:border-gray-600" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4 mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                             {{-- Menampilkan nilai lama atau nilai dari $user --}}
                            <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-700 dark:border-gray-600" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <hr class="my-6 border-gray-200 dark:border-gray-700">

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Kosongkan password jika tidak ingin mengubahnya.</p>

                        <div class="mt-4 mb-4">
                            <x-input-label for="password" :value="__('Password Baru (Opsional)')" />
                            <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-700 dark:border-gray-600"
                                            type="password"
                                            name="password"
                                            autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-4 mb-6">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full dark:bg-gray-700 dark:border-gray-600"
                                            type="password"
                                            name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-4">
                             {{-- Tombol Batal --}}
                            <a href="{{ route('data-user.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Batal') }}
                            </a>
                             {{-- Tombol Update --}}
                            <x-primary-button>
                                {{ __('Update Pengguna') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
