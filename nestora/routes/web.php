<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\Auth\RegisteredUserController;
// Hapus 'use Illuminate\Http\Request;' jika tidak digunakan di route lain
// Hapus 'use Illuminate\Support\Facades\Http;' jika tidak digunakan di route lain

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route Dashboard
Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Auth Routes ---
Route::get('register', function () {
    return redirect()->route('login');
})->middleware('guest')->name('register'); // Redirect GET /register ke login

Route::post('register', [RegisteredUserController::class, 'store'])
    ->middleware('guest'); // POST /register tetap ada

// Route bawaan auth (login, logout, forgot password, dll)
require __DIR__ . '/auth.php'; // Pastikan file auth.php disertakan

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    // Route Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Data Master
    Route::get('/data-master', [DataMasterController::class, 'index'])
        ->name('data-master.index');
    Route::get('/data-master/properti', [DataMasterController::class, 'propertiIndex'])
        ->name('data-master.properti.index');
    // TODO: Tambahkan route CRUD untuk data master lainnya jika diperlukan

    // Data User
    Route::get('/data-user', [DataUserController::class, 'index'])
        ->name('data-user.index');
     // TODO: Tambahkan route CRUD untuk data user jika diperlukan

    // Prediksi
    Route::get('/prediksi/create', [PredictionController::class, 'create'])
        ->name('prediksi.create'); // Menampilkan form

    // Route::post('/prediksi/create', function (Request $request) { ... }); // <-- HAPUS ROUTE CLOSURE INI

    Route::post('/prediksi', [PredictionController::class, 'store'])
        ->name('prediksi.store'); // Memproses form prediksi

});