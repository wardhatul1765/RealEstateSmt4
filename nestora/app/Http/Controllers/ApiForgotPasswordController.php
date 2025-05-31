<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Hapus Mail karena kita akan pakai Notifikasi
// use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\SendPasswordResetCode; // <-- TAMBAHKAN IMPORT NOTIFIKASI
use Illuminate\Support\Facades\Log; 

class ApiForgotPasswordController extends Controller
{
    /**
     * Mengirim kode reset 6 digit ke email pengguna.
     */
    public function sendResetCodeEmail(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email|exists:users,email']);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Email tidak terdaftar pada sistem kami.'], 404);
        }

        $user = User::where('email', $request->email)->first(); // Ambil objek user

        // Hapus token lama jika ada
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Buat kode acak 6 digit
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $code, 
            'created_at' => Carbon::now()
        ]);

        // Kirim email menggunakan Notifikasi
        try {
            // Gunakan $user->notify() untuk mengirim notifikasi
            $user->notify(new SendPasswordResetCode($code, $user->name)); 
        } catch (\Exception $e) {
            // Tambahkan log untuk error pengiriman email
            Log::error('Failed to send password reset code email: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email reset password. Silakan coba lagi nanti.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode reset password telah berhasil dikirim ke email Anda.'
        ], 200);
    }

    // ... method verifyCode dan resetPasswordWithCode tetap sama ...
    /**
     * Memverifikasi kode reset yang dikirim dari aplikasi.
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $record = DB::table('password_reset_tokens')
                        ->where('email', $request->email)
                        ->where('token', $request->code)
                        ->first();

        if (!$record || Carbon::parse($record->created_at)->addMinutes(10)->isPast()) { // Anggap 10 menit kedaluwarsa
            return response()->json(['success' => false, 'message' => 'Kode tidak valid atau telah kedaluwarsa.'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Kode berhasil diverifikasi.'], 200);
    }

    /**
     * Mereset password setelah kode diverifikasi.
     */
    public function resetPasswordWithCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $record = DB::table('password_reset_tokens')
                        ->where('email', $request->email)
                        ->where('token', $request->code)
                        ->first();
        
        if (!$record || Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Kode tidak valid atau telah kedaluwarsa.'], 400);
        }

        $userToUpdate = User::where('email', $request->email)->first(); // Ambil user untuk diupdate
        if (!$userToUpdate) { // Seharusnya tidak terjadi karena ada 'exists:users,email'
            return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
        }
        $userToUpdate->password = Hash::make($request->password);
        $userToUpdate->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Password Anda telah berhasil direset.'], 200);
    }
}