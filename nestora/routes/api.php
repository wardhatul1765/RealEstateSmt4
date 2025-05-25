<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIAuthController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\APIPropertyController;
use App\Http\Controllers\APIImageController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // Pastikan ini di-import di bagian atas file
use Illuminate\Support\Facades\Storage; // Bisa juga menggunakan Storage facade

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/login', [APIAuthController::class, 'apiLogin']);

// // routes/api.php
// Route::post('/register', [APIAuthController::class, 'apiRegister']);

Route::post('/register', [APIAuthController::class, 'register']);
Route::post('/login', [APIAuthController::class, 'login']);
Route::post('/refresh', [APIAuthController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [APIAuthController::class, 'profile']);
    Route::post('/logout', [APIAuthController::class, 'logout']);
    Route::post('/properties', [APIPropertyController::class, 'store']);
    Route::post('/properties/{id}/upload-image', [APIPropertyController::class, 'uploadImage']);
    Route::put('/properties/{id}', [APIPropertyController::class, 'update']);
    Route::get('/user/properties', [APIPropertyController::class, 'index']);
    // Route::post('/upload-images', [APIImageController::class, 'upload']);

});

Route::get('/serve-image/properties/{filename}', function ($filename) {
    $cleanedFilename = basename($filename);
    $path = storage_path('app/public/properties/' . $cleanedFilename);

    Log::info('Serve Image Route Hit:');
    Log::info('Original Filename from URL: ' . $filename);
    Log::info('Cleaned Filename: ' . $cleanedFilename);
    Log::info('Attempting to serve file from path: ' . $path);
    Log::info('Does file exist at path? ' . (File::exists($path) ? 'Yes' : 'No'));

    if (!File::exists($path)) {
        Log::error('File not found for serve-image: ' . $path);
        abort(404, 'Image not found via serve-image route.');
    }

    $file = File::get($path);
    $type = File::mimeType($path);
    Log::info('File found. Type: ' . $type);

    $response = response($file, 200)->header('Content-Type', $type);
    return $response;
})->where('filename', '.*')->name('property.image.serve');

// Log::info('Request payload:', $request->all());

Route::middleware('jwt.auth')->get('/test', function () {
    return response()->json(['message' => 'Authenticated!']);
});


Route::middleware('auth:sanctum')->post('/predict-price', [PredictionController::class, 'predictPrice']);