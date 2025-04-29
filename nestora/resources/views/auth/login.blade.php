<x-guest-layout>
    {{-- Tambahkan x-data untuk mengelola state form mana yang aktif --}}
    {{-- Tambahkan overflow-x-hidden pada card utama untuk mencegah scroll horizontal saat animasi --}}
    <div x-data="{ isLogin: true }"
        class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden flex flex-col md:flex-row min-h-[600px] overflow-x-hidden">

        {{-- Kolom Kiri (Informasi) --}}
        {{-- Tetap gunakan flex justify-center di parent, tambahkan relative overflow-hidden --}}
        <div
            class="w-full md:w-1/2 bg-nestora-lime p-0 flex flex-col justify-center items-center relative overflow-hidden">
            {{-- Padding dihapus, tambah relative overflow-hidden --}}

            {{-- Konten untuk State Login Aktif --}}
            {{-- Tambahkan flexbox untuk centering di dalam div ini juga --}}
            <div x-show="isLogin" x-transition:enter="transition ease-out duration-500 transform"
                x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300 transform absolute inset-0" {{-- Ganti w-full ke
                inset-0 --}} x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-full" {{-- Tambahkan flex & justify-center agar konten
                tetap tengah saat absolute --}}
                class="p-8 sm:p-12 box-border text-center md:text-left flex flex-col justify-center">

                <h1 class="text-4xl font-bold text-gray-800 mb-4">
                    Nestora
                </h1>
                <p class="text-lg text-gray-800 mb-2">
                    Selamat Datang Kembali!
                </p>
                <p class="text-gray-700 leading-relaxed mb-6">
                    Silakan masuk untuk mengakses dashboard Anda.
                </p>
                <p class="text-gray-800 mb-2">
                    Belum punya akun?
                </p>
                <button @click="isLogin = false"
                    class="inline-block px-6 py-2 border-2 border-gray-800 text-gray-800 font-semibold rounded-lg hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-opacity-50 transition duration-150 ease-in-out self-center md:self-start">
                    {{-- Sesuaikan self-alignment --}}
                    DAFTAR SEKARANG
                </button>
            </div>

            {{-- Konten untuk State Register Aktif --}}
            {{-- Tambahkan flexbox untuk centering di dalam div ini juga --}}
            <div x-show="!isLogin" x-transition:enter="transition ease-out duration-500 transform"
                x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300 transform absolute inset-0" {{-- Ganti w-full ke
                inset-0 --}} x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-full" {{-- Tambahkan flex & justify-center agar konten
                tetap tengah saat absolute --}}
                class="p-8 sm:p-12 box-border text-center md:text-left flex flex-col justify-center">

                <h1 class="text-4xl font-bold text-gray-800 mb-4">
                    Bergabung dengan Nestora
                </h1>
                <p class="text-lg text-gray-800 mb-2">
                    Buat Akun Baru
                </p>
                <p class="text-gray-700 leading-relaxed mb-6">
                    Isi form di sebelah kanan untuk memulai.
                </p>
                <p class="text-gray-800 mb-2">
                    Sudah punya akun?
                </p>
                <button @click="isLogin = true"
                    class="inline-block px-6 py-2 border-2 border-gray-800 text-gray-800 font-semibold rounded-lg hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-opacity-50 transition duration-150 ease-in-out self-center md:self-start">
                    {{-- Sesuaikan self-alignment --}}
                    MASUK DI SINI
                </button>
            </div>
        </div>

        {{-- Kolom Kanan (Form - Container) --}}
        {{-- (Kode kolom kanan tetap sama seperti revisi sebelumnya) --}}
        <div class="w-full md:w-1/2 p-0 relative overflow-hidden">

            {{-- Container untuk Form Login --}}
            <div x-show="isLogin" x-transition:enter="transition ease-out duration-500 transform"
                x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300 transform absolute w-full"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-full" class="top-0 left-0 p-8 sm:p-12 w-full box-border">

                {{-- Konten Form Login tidak berubah --}}
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">MASUK</h2>
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <form method="POST" action="{{ route('login') }}">@csrf
                    <div class="mb-4">
                        <x-input-label for="login_email" :value="__('Email')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="login_email"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-nestora-lime focus:ring focus:ring-nestora-lime focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                            placeholder="email@gmail.com" />
                        <x-input-error :messages="$errors->login->get('email')" class="mt-2" />
                        @if(!$errors->login->has('email')) <x-input-error :messages="$errors->get('email')"
                        class="mt-2" /> @endif
                    </div>
                    <div class="mb-4">
                        <x-input-label for="login_password" :value="__('Password')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="login_password"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-nestora-lime focus:ring focus:ring-nestora-lime focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->login->get('password')" class="mt-2" />
                        @if(!$errors->login->has('password')) <x-input-error :messages="$errors->get('password')"
                        class="mt-2" /> @endif
                    </div>
                    <div class="flex items-center justify-between mb-6 text-sm">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 dark:border-gray-600 text-nestora-lime shadow-sm focus:ring-nestora-lime dark:focus:ring-offset-gray-800"
                                name="remember">
                            <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="font-medium text-lime-600 hover:text-lime-800 dark:text-lime-400 dark:hover:text-lime-300 rounded-md focus:outline-none"
                                href="{{ route('password.request') }}">
                                {{ __('Lupa password?') }}
                            </a>
                        @endif
                    </div>
                    <div class="mb-6">
                        <x-primary-button
                            class="w-full justify-center py-3 text-base bg-nestora-lime hover:bg-lime-500 focus:bg-lime-500 active:bg-lime-600 text-gray-800 font-semibold focus:ring-lime-400 dark:text-gray-900">
                            {{ __('MASUK') }}
                        </x-primary-button>
                    </div>
                    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Belum punya akun?') }}
                        <a href="#" @click.prevent="isLogin = false"
                            class="font-medium text-lime-600 hover:text-lime-800 dark:text-lime-400 dark:hover:text-lime-300 focus:outline-none">
                            {{ __('Daftar sekarang') }}
                        </a>
                    </div>
                </form>
            </div>

            {{-- Container untuk Form Register --}}
            <div x-show="!isLogin" x-transition:enter="transition ease-out duration-500 transform"
                x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300 transform absolute w-full"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-full" class="top-0 left-0 p-8 sm:p-12 w-full box-border">

                {{-- Konten Form Register tidak berubah --}}
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">DAFTAR AKUN BARU</h2>
                <form method="POST" action="{{ route('register') }}">@csrf
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Lengkap')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="name"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-nestora-lime focus:ring focus:ring-nestora-lime focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
                            type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                            placeholder="Nama Anda" />
                        <x-input-error :messages="$errors->register->get('name')" class="mt-2" />
                        @if(!$errors->register->has('name')) <x-input-error :messages="$errors->get('name')"
                        class="mt-2" /> @endif
                    </div>
                    <div class="mb-4">
                        <x-input-label for="register_email" :value="__('Email')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="register_email"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-nestora-lime focus:ring focus:ring-nestora-lime focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
                            type="email" name="email" :value="old('email')" required autocomplete="email"
                            placeholder="email@anda.com" />
                        <x-input-error :messages="$errors->register->get('email')" class="mt-2" />
                        @if(!$errors->register->has('email')) <x-input-error :messages="$errors->get('email')"
                        class="mt-2" /> @endif
                    </div>
                    <div class="mb-4">
                        <x-input-label for="register_password" :value="__('Password')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="register_password"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-nestora-lime focus:ring focus:ring-nestora-lime focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
                            type="password" name="password" required autocomplete="new-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->register->get('password')" class="mt-2" />
                        @if(!$errors->register->has('password')) <x-input-error :messages="$errors->get('password')"
                        class="mt-2" /> @endif
                    </div>
                    <div class="mb-6">
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="password_confirmation"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-nestora-lime focus:ring focus:ring-nestora-lime focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"
                            type="password" name="password_confirmation" required autocomplete="new-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->register->get('password_confirmation')" class="mt-2" />
                        @if(!$errors->register->has('password_confirmation')) <x-input-error
                        :messages="$errors->get('password_confirmation')" class="mt-2" /> @endif
                    </div>
                    <div class="mb-6">
                        <x-primary-button
                            class="w-full justify-center py-3 text-base bg-nestora-lime hover:bg-lime-500 focus:bg-lime-500 active:bg-lime-600 text-gray-800 font-semibold focus:ring-lime-400 dark:text-gray-900">
                            {{ __('DAFTAR') }}
                        </x-primary-button>
                    </div>
                    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Sudah punya akun?') }}
                        <a href="#" @click.prevent="isLogin = true"
                            class="font-medium text-lime-600 hover:text-lime-800 dark:text-lime-400 dark:hover:text-lime-300 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nestora-lime dark:focus:ring-offset-gray-800 rounded-sm">
                            {{ __('Masuk di sini') }}
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-guest-layout>