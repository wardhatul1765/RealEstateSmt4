<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Data Pengguna') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900 dark:text-gray-100">

                  <div class="flex justify-between items-center mb-6">
                      <h3 class="text-2xl font-semibold">Daftar Pengguna</h3>
                      {{-- Tombol Tambah Pengguna (sesuaikan route jika sudah ada) --}}
                      {{-- <a href="#">
                          <x-primary-button>
                              {{ __('Tambah Pengguna') }}
                          </x-primary-button>
                      </a> --}}
                  </div>

                  {{-- Tabel Data Pengguna --}}
                  <div class="overflow-x-auto">
                      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
                          <thead class="bg-gray-50 dark:bg-gray-700">
                              <tr>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                      No
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                      Nama
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                      Email
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                      Tanggal Bergabung
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                      Aksi
                                  </th>
                              </tr>
                          </thead>
                          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                              @forelse ($users as $index => $user)
                                  <tr>
                                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                          {{-- Hitung nomor urut berdasarkan pagination --}}
                                          {{ $users->firstItem() + $index }}
                                      </td>
                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                          {{ $user->name }}
                                      </td>
                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                          {{ $user->email }}
                                      </td>
                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                          {{ $user->created_at->format('d M Y H:i') }} {{-- Format tanggal --}}
                                      </td>
                                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                          {{-- Tombol Aksi (Edit, Hapus) - sesuaikan route jika sudah ada --}}
                                          {{-- <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">Edit</a> --}}
                                          {{-- <form action="#" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">Hapus</button>
                                          </form> --}}
                                          <span>-</span> {{-- Placeholder jika belum ada aksi --}}
                                      </td>
                                  </tr>
                              @empty
                                  <tr>
                                      <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                          Tidak ada data pengguna.
                                      </td>
                                  </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>

                  {{-- Link Pagination --}}
                  <div class="mt-4">
                      {{ $users->links() }}
                  </div>

              </div>
          </div>
      </div>
  </div>
</x-app-layout>