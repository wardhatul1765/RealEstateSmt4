<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIAuthController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\APIPropertyController;
use App\Http\Controllers\APIImageController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [APIAuthController::class, 'register']);
Route::post('/login', [APIAuthController::class, 'login']);
Route::post('/refresh', [APIAuthController::class, 'refresh']); // Pastikan method refresh ada dan berfungsi
Route::get('/properties/public', [APIPropertyController::class, 'getPublicProperties']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [APIAuthController::class, 'profile']);
    Route::put('/profile', [APIAuthController::class, 'updateUserProfile']); // Untuk update profil (nama, bio, dll)
    Route::post('/logout', [APIAuthController::class, 'logout']);
    Route::post('/profile/change-password', [APIAuthController::class, 'changePassword']);

    // Routes untuk Property
    Route::post('/properties', [APIPropertyController::class, 'store']);
    Route::post('/properties/{id}/upload-image', [APIPropertyController::class, 'uploadImage']);
    Route::post('/properties/{id}', [APIPropertyController::class, 'update']); // Sebaiknya gunakan PUT atau PATCH untuk update
    Route::get('/user/properties', [APIPropertyController::class, 'index']);
    // Route::post('/upload-images', [APIImageController::class, 'upload']); // Pastikan controller dan method ini ada jika diaktifkan
});

Route::get('/serve-image/properties/{filename}', function ($filename) {
    $cleanedFilename = basename($filename);
    $path = storage_path('app/public/properties/' . $cleanedFilename);

    // Log::info('Serve Image Route Hit:'); // Kurangi logging jika sudah production
    // Log::info('Original Filename from URL: ' . $filename);
    // Log::info('Cleaned Filename: ' . $cleanedFilename);
    // Log::info('Attempting to serve file from path: ' . $path);
    // Log::info('Does file exist at path? ' . (File::exists($path) ? 'Yes' : 'No'));

    if (!File::exists($path)) {
        // Log::error('File not found for serve-image: ' . $path);
        abort(404, 'Image not found.'); // Pesan lebih singkat untuk produksi
    }

    $file = File::get($path);
    $type = File::mimeType($path);
    // Log::info('File found. Type: ' . $type);

    $response = response($file, 200)->header('Content-Type', $type);
    return $response;
})->where('filename', '.*')->name('property.image.serve');


// Route::middleware('jwt.auth')->get('/test', function () { // 'jwt.auth' adalah alias, 'auth:api' lebih standar jika guard api Anda adalah jwt
//     return response()->json(['message' => 'Authenticated!']);
// });


// Route untuk prediksi harga, pastikan middleware sesuai kebutuhan (mungkin auth:api juga?)
Route::middleware('auth:api')->post('/predict-price', [PredictionController::class, 'predictPrice']); // Menggunakan auth:api agar hanya user terotentikasi