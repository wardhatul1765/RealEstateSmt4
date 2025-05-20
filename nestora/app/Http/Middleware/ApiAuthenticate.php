<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            // Jika user tidak autentikasi, kembalikan JSON error
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Anda harus login untuk mengakses API ini.'
            ], 401);
        }

        return $next($request);
    }
}
