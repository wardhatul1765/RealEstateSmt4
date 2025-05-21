<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIAuthController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\APIPropertyController;
use App\Http\Controllers\APIImageController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [APIAuthController::class, 'profile']);
    Route::post('/logout', [APIAuthController::class, 'logout']);
    Route::post('/properties', [APIPropertyController::class, 'store']);
    Route::post('/upload-images', [APIImageController::class, 'upload']);
});

Route::middleware('auth:sanctum')->post('/predict-price', [PredictionController::class, 'predictPrice']);