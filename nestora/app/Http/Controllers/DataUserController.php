<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User; // <-- Gunakan model User yang sudah dikonfigurasi untuk MongoDB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; // Untuk logging jika perlu

class DataUserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     * Mengambil data dari collection 'users' MongoDB.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Ambil data pengguna dengan pagination
        $users = User::latest()->paginate(15); // Urutkan berdasarkan _id atau created_at (jika ada)

        return view('pengguna.index', compact('users')); // <-- Diubah ke pengguna.index
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        // Menampilkan view 'pengguna.create'
        // Pastikan view ini ada di resources/views/pengguna/create.blade.php
        return view('pengguna.create'); // <-- Diubah ke pengguna.create
    }

    /**
     * Menyimpan pengguna baru ke collection 'users' MongoDB.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input dari form
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Pastikan email unik di collection 'users' MongoDB
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Memerlukan field 'password_confirmation'
            // Tambahkan validasi untuk role jika diperlukan
            // 'role' => ['required', 'string', 'exists:roles,name'] // Contoh jika pakai Spatie Roles
        ]);

        try {
            // Buat user baru menggunakan Model User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash password sebelum disimpan
                // Tambahkan field lain jika ada, misal 'created_at', 'updated_at' akan otomatis
            ]);

            // Opsional: Assign role default jika menggunakan Spatie Roles
            // $user->assignRole('User'); // Atau role lain dari input form

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('data-user.index') // Nama route tetap 'admin.data-user.index'
                         ->with('success', 'Pengguna baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan pengguna baru: ' . $e->getMessage());
            // Redirect kembali ke form create dengan pesan error
            return redirect()->route('data-user.create') // Nama route tetap 'admin.data-user.create'
                         ->with('error', 'Gagal menambahkan pengguna. Terjadi kesalahan.')
                         ->withInput(); // Kembalikan input sebelumnya
        }
    }

    /**
     * Menampilkan form untuk mengedit pengguna yang ada.
     * Menggunakan route model binding untuk mencari user berdasarkan _id MongoDB.
     *
     * @param  \App\Models\User  $user // Laravel otomatis mencari user berdasarkan _id
     * @return \Illuminate\View\View
     */
    public function edit(User $user): View // Type hint User akan melakukan pencarian otomatis
    {
        // Kirim data user yang ditemukan ke view 'pengguna.edit'
        // Pastikan view ini ada di resources/views/pengguna/edit.blade.php
        return view('pengguna.edit', compact('user')); // <-- Diubah ke pengguna.edit
    }

    /**
     * Memperbarui data pengguna di collection 'users' MongoDB.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user // User yang akan diupdate (ditemukan otomatis)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Validasi input, abaikan email unik jika email tidak berubah
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Pastikan email unik, kecuali untuk user ini sendiri (_id nya)
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id . ',_id'], // Tambahkan pengecualian _id
            // Password hanya divalidasi jika diisi
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            // Tambahkan validasi role jika ada
        ]);

        try {
            // Siapkan data untuk diupdate
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                // Tambahkan field lain jika ada
            ];

            // Hanya update password jika field password diisi
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Lakukan update menggunakan metode Eloquent
            $user->update($updateData);

            // Opsional: Update role jika menggunakan Spatie Roles
            // if ($request->has('role')) {
            //     $user->syncRoles([$request->role]);
            // }

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('data-user.index') // Nama route tetap 'admin.data-user.index'
                         ->with('success', 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Gagal memperbarui pengguna (ID: {$user->id}): " . $e->getMessage());
            // Redirect kembali ke form edit dengan pesan error
            return redirect()->route('data-user.edit', $user) // Nama route tetap 'admin.data-user.edit'
                         ->with('error', 'Gagal memperbarui data . Terjadi kesalahan.')
                         ->withInput();
        }
    }

    /**
     * Menghapus pengguna dari collection 'users' MongoDB.
     *
     * @param  \App\Models\User  $user // User yang akan dihapus (ditemukan otomatis)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // Jangan hapus admin yang sedang login (opsional tapi disarankan)
            if (auth()->id() === $user->id) {
                 return redirect()->route('data-user.index') // Nama route tetap 'admin.data-user.index'
                              ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            }

            // Lakukan penghapusan menggunakan metode Eloquent
            $user->delete();

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('data-user.index') // Nama route tetap 'admin.data-user.index'
                         ->with('success', 'Pengguna berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal menghapus pengguna (ID: {$user->id}): " . $e->getMessage());
            // Redirect ke halaman index dengan pesan error
            return redirect()->route('data-user.index') // Nama route tetap 'admin.data-user.index'
                         ->with('error', 'Gagal menghapus pengguna. Terjadi kesalahan.');
        }
    }
}
