<x-guest-layout>
    {{-- Teks Pembuka --}}
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 tracking-wider">MULAI GRATIS</p>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Buat Akun Baru</h2>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="name" :value="__('Nama Lengkap')" class="mb-1" />
            <x-text-input id="name" class="block w-full px-4 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-nestora-lime dark:focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm"
                          type="text"
                          name="name"
                          :value="old('name')"
                          required autofocus
                          autocomplete="name"
                          placeholder="Nama Anda"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="mb-1" />
            <x-text-input id="email" class="block w-full px-4 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-nestora-lime dark:focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autocomplete="username"
                          placeholder="email@anda.com"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" class="mb-1" />
            <x-text-input id="password" class="block w-full px-4 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-nestora-lime dark:focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="mb-1" />
            <x-text-input id="password_confirmation" class="block w-full px-4 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-nestora-lime dark:focus:border-nestora-lime focus:ring-nestora-lime rounded-md shadow-sm"
                            type="password"
                            name="password_confirmation" required
                            autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Tombol Register --}}
        <div class="flex items-center justify-center mb-6">
             {{-- Button dibuat gelap seperti referensi --}}
            <x-primary-button class="w-full justify-center py-3 text-base bg-gray-800 hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:active:bg-gray-500 focus:ring-nestora-lime">
                {{ __('DAFTAR') }}
            </x-primary-button>
        </div>

         {{-- Hapus bagian "Or" dan tombol social login --}}

        {{-- Link ke Login --}}
         <div class="text-center text-sm text-gray-600 dark:text-gray-400">
            Sudah punya akun?
            <a class="font-semibold text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nestora-lime dark:focus:ring-offset-gray-800 rounded-sm" href="{{ route('login') }}">
                MASUK
            </a>
        </div>
    </form>
</x-guest-layout>