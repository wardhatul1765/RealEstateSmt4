<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIAuthController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\APIPropertyController;
use App\Http\Controllers\ApiForgotPasswordController;
// use App\Http\Controllers\APIImageController; // Hilangkan jika tidak dipakai
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/* ... */

Route::post('/register', [APIAuthController::class, 'register']);
Route::post('/login', [APIAuthController::class, 'login']);
Route::post('/refresh', [APIAuthController::class, 'refresh']);
Route::get('/properties/public', [APIPropertyController::class, 'getPublicProperties']);
// Tambahkan route untuk detail properti publik & pencatatan view
Route::get('/properties/public/{id}', [APIPropertyController::class, 'showPublicProperty']);


// Endpoint ini bisa diakses publik jika Anda ingin mencatat semua view,
// atau pindahkan ke dalam auth:api group jika hanya view dari user terautentikasi
// Jika pencatatan view sudah dihandle di showPublicProperty, route ini bisa jadi tidak diperlukan.
// Route::post('/properties/{id}/record-view', [APIPropertyController::class, 'recordView']);


Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [APIAuthController::class, 'profile']);
    Route::put('/profile', [APIAuthController::class, 'updateUserProfile']);
    Route::post('/logout', [APIAuthController::class, 'logout']);
    Route::post('/profile/change-password', [APIAuthController::class, 'changePassword']);

    // Routes untuk Property
    Route::post('/properties', [APIPropertyController::class, 'store']);
    // Route::post('/properties/{id}/upload-image', [APIPropertyController::class, 'uploadImage']); // Anda punya ini, tapi di controller Anda tidak ada methodnya
    Route::post('/properties/{id}', [APIPropertyController::class, 'update']);
    Route::get('/user/properties', [APIPropertyController::class, 'index']);

    // Route BARU untuk statistik (hanya untuk user terautentikasi & pemilik)
    Route::get('/properties/{id}/statistics', [APIPropertyController::class, 'getPropertyViewStatistics']);

    Route::post('/predict-price', [PredictionController::class, 'predictPrice']);
    // +++ TAMBAHKAN ROUTE BARU INI UNTUK DELETE +++
    Route::delete('/properties/{id}', [APIPropertyController::class, 'destroy']);

    // === ROUTE BARU UNTUK BOOKMARK ===
    Route::post('/properties/{id}/toggle-bookmark', [APIPropertyController::class, 'toggleBookmark']);
    Route::get('/bookmarks', [APIPropertyController::class, 'getBookmarkedProperties']);
    // === AKHIR ROUTE BOOKMARK ===
});

Route::post('/forgot-password', [ApiForgotPasswordController::class, 'sendResetCodeEmail']);
// 2. Memverifikasi kode yang dimasukkan pengguna di aplikasi
Route::post('/verify-password-code', [ApiForgotPasswordController::class, 'verifyCode']);
// 3. Mereset password setelah kode diverifikasi
Route::post('/reset-password-with-code', [ApiForgotPasswordController::class, 'resetPasswordWithCode']);


Route::post('/refresh', [APIAuthController::class, 'refresh']);

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

// TAMBAHKAN ROUTE BARU INI untuk menyajikan gambar profil
Route::get('/serve-image/profile/{filename}', function ($filename) {
    $cleanedFilename = basename($filename);
    // PENTING: Path disesuaikan ke direktori gambar profil
    $path = storage_path('app/public/profile_images/' . $cleanedFilename); 
    if (!File::exists($path)) {
        abort(404, 'Profile image not found.');
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    return response($file, 200)->header('Content-Type', $type);
})->where('filename', '.*')->name('profile.image.serve'); // Beri nama route