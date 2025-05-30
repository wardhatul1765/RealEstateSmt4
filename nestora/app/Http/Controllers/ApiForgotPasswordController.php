<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;

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

        // Hapus token lama jika ada
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Buat kode acak 6 digit
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan kode ke database (Laravel menggunakan tabel `password_reset_tokens` secara default)
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $code, // Kita simpan kode di kolom token
            'created_at' => Carbon::now()
        ]);

        // Kirim email berisi kode (bukan link)
    //   Kirim email berisi kode
        try {
            Mail::raw(
                "Halo,\n\n" .
                "Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda di $appName.\n\n" .
                "Kode reset password Anda adalah: $code\n\n" .
                "Silakan masukkan kode ini di aplikasi untuk melanjutkan proses reset password. " .
                "Kode ini akan kedaluwarsa dalam $codeExpirationMinutes menit.\n\n" .
                "Jika Anda tidak meminta reset password, abaikan email ini.\n\n" .
                "Terima kasih,\n" .
                "Tim $appName",
                function ($message) use ($request, $appName) {
                    $message->to($request->email)
                            ->subject("[$appName] Kode Reset Password Anda");
                }
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email reset password. Silakan coba lagi nanti.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode reset password telah berhasil dikirim ke email Anda.'
        ], 200);
    }

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

        // Jika tidak ada record atau record lebih dari 10 menit
        if (!$record || Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
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

        // Verifikasi sekali lagi untuk keamanan
        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->code)
                    ->first();
        
        if (!$record || Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Kode tidak valid atau telah kedaluwarsa.'], 400);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token setelah berhasil digunakan
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Password Anda telah berhasil direset.'], 200);
    }
}