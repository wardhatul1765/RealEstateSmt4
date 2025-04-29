{{-- Hapus x-data dari nav jika state dikelola di app.blade.php --}}
<nav class="bg-gray-800 border-b border-gray-700">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <button @click="isSidebarOpen = !isSidebarOpen"
                        class="mr-3 text-gray-400 hover:text-white focus:outline-none focus:text-white md:block"> {{-- Tampilkan di semua ukuran layar --}}
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

            </div>

            {{-- Bagian Kanan Navbar (Contoh: Dropdown User) --}}
             <div class="hidden sm:flex sm:items-center sm:ml-6">
                  @auth
                    <div class="ml-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                    <button class="flex items-center text-sm text-white font-medium rounded-md hover:text-gray-300 focus:outline-none focus:text-gray-300 transition duration-150 ease-in-out">
                                        {{-- Ganti dengan avatar admin --}}
                                        <img class="h-8 w-8 rounded-full object-cover mr-2" src="https://via.placeholder.com/150" alt="User Avatar">
                                        <div>{{ Auth::user()->name ?? 'User' }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}"> @csrf <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link></form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                 @endauth
             </div>

             <div class="-mr-2 flex items-center sm:hidden">
                 <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                     <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                         <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                         <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                     </svg>
                 </button>
             </div>

        </div>
    </div>

    {{-- 'open' di sini perlu di-scope ke x-data="{ open: false }" di elemen parent jika belum --}}
    <div x-data="{ open: false }" :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link href="#" :active="request()->is('data-master*')">
                 Data Master
             </x-responsive-nav-link>
             <x-responsive-nav-link href="#" :active="request()->is('data-user*')">
                 Data User
             </x-responsive-nav-link>
              <x-responsive-nav-link href="#" :active="request()->is('prediksi.*')">
                  Menu Prediksi
              </x-responsive-nav-link>
        </div>
        @auth
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="flex items-center px-4">
                {{-- <div class="shrink-0 mr-3"><img class="h-10 w-10 rounded-full object-cover" src="..." alt="User Avatar"></div> --}}
                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                 <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                 <form method="POST" action="{{ route('logout') }}">@csrf<x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link></form>
            </div>
        </div>
        @endauth
    </div>
</nav>