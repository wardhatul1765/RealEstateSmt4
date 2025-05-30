<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class APIAuthController extends Controller
{
    public function __construct()
    {
        // Middleware jwt.auth (atau auth:api) akan melindungi semua method kecuali login dan register
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        // Jika Anda menggunakan 'jwt.auth' sebagai nama middleware Anda, gunakan itu.
        // Umumnya, jika guard 'api' Anda sudah diatur untuk JWT, 'auth:api' adalah standar.
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            // Kembalikan pesan error yang lebih konsisten dengan Flutter App
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        try {
            if (!$token = JWTAuth::fromUser($user)) { // Coba generate token
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat membuat token.'
                ], 500);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat membuat token: ' . $e->getMessage()
            ], 500);
        }

        // Mengambil TTL (Time To Live) untuk token dari konfigurasi JWT atau Facade JWTAuth
        $expires_in = JWTAuth::factory()->getTTL() * 60; // Dalam detik

        return response()->json([
            // 'success' => true, // Tambahkan ini agar konsisten dengan respons lain
            // 'message' => 'Login berhasil', // Tambahkan ini
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expires_in,
            'user' => [ // Kirim data user yang relevan
                'id' => $user->_id, // atau $user->id tergantung primary key Anda di model
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $user->profile_image,
                'bio' => $user->bio ?? '', // Kirim bio jika ada, atau string kosong
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:15', // Tambahkan validasi untuk phone
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone, // Pastikan phone disimpan
            // 'profile_image' => $request->profile_image ?? '', // Jika ada saat register
            // 'bio' => '', // Default bio
        ]);

        // Setelah user dibuat, generate token untuknya
        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registrasi berhasil tetapi tidak dapat membuat token.'
                ], 500);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi berhasil tetapi tidak dapat membuat token: ' . $e->getMessage()
            ], 500);
        }


        // --- PERBAIKAN UNTUK MENGAMBIL TTL ---
        $expires_in = JWTAuth::factory()->getTTL() * 60; // Cara yang lebih aman untuk JWT
        // Atau jika Anda sudah mengkonfigurasi 'jwt.ttl' di config/jwt.php:
        // $expires_in = config('jwt.ttl') * 60;

        return response()->json([
            // 'success' => true, // Tambahkan ini agar konsisten
            // 'message' => 'Registrasi berhasil.', // Tambahkan ini
            'user' => $user, // Mengembalikan user yang baru dibuat (tanpa password ter-hash jika Anda mau)
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expires_in,
        ], 201); // HTTP 201 Created
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['success' => true, 'message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            // Jika token sudah tidak valid atau tidak ada, logout tetap dianggap berhasil di sisi client
            // Namun, server bisa merespons dengan error jika proses invalidasi token gagal
            return response()->json(['success' => false, 'error' => 'Failed to logout, token problem: ' . $e->getMessage()], 401);
        }
    }

    public function profile(Request $request) // Ini untuk GET /api/profile
    {
        // Dapatkan user yang terotentikasi via token (auth:api middleware seharusnya sudah handle ini)
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found or not authenticated'], 404);
        }

        // Pastikan semua field yang dibutuhkan Flutter ada di sini, termasuk 'bio'
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->_id, // atau $user->id
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $user->profile_image,
                'bio' => $user->bio ?? '', // Kirim bio, atau string kosong jika null
            ]
        ]);
    }

    // --- UPDATE PROFIL ---
    public function updateUserProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found or not authenticated'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:1000', // Bio bisa null atau string
            // Tambahkan validasi untuk 'phone' jika bisa diubah
            // 'phone' => 'sometimes|string|max:15',
        ]);

        // Update field hanya jika ada di request
        if ($request->filled('name')) { // 'filled' mengecek apakah ada dan tidak kosong
            $user->name = $request->name;
        }
        if ($request->has('bio')) { // 'has' mengecek apakah key 'bio' ada (bisa jadi string kosong)
            $user->bio = $request->bio;
        }
        // if ($request->filled('phone')) {
        //     $user->phone = $request->phone;
        // }

        $user->save();

        // Kembalikan data user yang sudah terupdate
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => [ // Kirim kembali objek user yang sudah diupdate
                'id' => $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $user->profile_image,
                'bio' => $user->bio ?? '',
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            // Sebenarnya middleware auth:api sudah menangani ini,
            // tapi sebagai pengaman tambahan
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed', // 'confirmed' akan mencocokkan dengan 'new_password_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid.',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        // 2. Verifikasi Password Lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama Anda salah.'
            ], 400); // 400 Bad Request
        }

        // 3. Update ke Password Baru
        $user->password = Hash::make($request->new_password);
        $user->save(); // Simpan perubahan

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ], 200); // 200 OK
    }


    // Fungsi me() dan refresh() Anda sebelumnya sudah ada
    public function me()
    {
        // Lebih baik gunakan fungsi profile() yang sudah kita standarisasi responsnya
        // atau pastikan respons ini juga konsisten
        return response()->json(Auth::user());
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            $expires_in = JWTAuth::factory()->getTTL() * 60; // Ambil TTL yang benar

            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => $expires_in,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token: ' . $e->getMessage()], 401);
        }
    }
}
