<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class APIAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Temukan user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Generate token secara manual
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'id' => (string) $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $user->profile_image ?? '',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'profile_image' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'id' => (string) $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $user->profile_image ?? '',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ], 201);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    // Optional: refresh token endpoint
    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }
}
