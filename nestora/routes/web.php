<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\PredictionController;
// use App\Http\Controllers\Auth\RegisteredUserController; // Jika tidak digunakan, bisa dikomentari
use App\Http\Controllers\ManajemenPropertiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Landing Page Route (Public) ---
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// --- Auth Routes (Login, Register, dll) ---
require __DIR__ . '/auth.php';


// --- Authenticated Routes ---
// Pastikan middleware 'auth' dan 'verified' (jika perlu) diterapkan dengan benar
Route::middleware(['auth', 'verified'])->group(function () { // Menambahkan 'verified' di sini jika semua rute di dalamnya memerlukannya

    // --- Dashboard Route ---
    Route::get('/dashboard', [DashboardController::class, 'index'])
        // ->middleware('verified') // Sudah ada di group middleware di atas
        ->name('dashboard');

    // --- Route Profile ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //== MANAJEMEN PROPERTI Routes ==
    Route::prefix('manajemen-properti')->name('manajemen-properti.')->group(function () {
        Route::get('/', [ManajemenPropertiController::class, 'index'])->name('index');
        Route::get('/persetujuan', [ManajemenPropertiController::class, 'persetujuan'])->name('persetujuan');
        Route::get('/create', [ManajemenPropertiController::class, 'create'])->name('create');
        Route::post('/', [ManajemenPropertiController::class, 'store'])->name('store');
        Route::get('/{properti}', [ManajemenPropertiController::class, 'show'])->name('show');
        Route::get('/{properti}/edit', [ManajemenPropertiController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{properti}', [ManajemenPropertiController::class, 'update'])->name('update');
        Route::delete('/{properti}', [ManajemenPropertiController::class, 'destroy'])->name('destroy');
        Route::patch('/{properti}/approve', [ManajemenPropertiController::class, 'approve'])->name('approve');
        Route::patch('/{properti}/reject', [ManajemenPropertiController::class, 'reject'])->name('reject');
    });
    //== Akhir MANAJEMEN PROPERTI Routes ==

    //== DATA MASTER PROPERTI Routes (Disesuaikan untuk Modal) ==
        Route::prefix('data-master/properti')->name('data-master.properti.')->group(function () {
        Route::get('/', [DataMasterController::class, 'propertiIndex'])->name('index');
        Route::get('/{id}/edit-data', [DataMasterController::class, 'getPropertyEditData'])->name('edit-data');
        // Route untuk mengambil data JSON untuk modal edit
        // Route::get('/{id}/edit-data', [DataMasterController::class, 'getPropertyEditData'])->name('edit-data');
        // Route store dan update akan menangani AJAX dari modal
        Route::post('/', [DataMasterController::class, 'store'])->name('store');
        Route::put('/{id}', [DataMasterController::class, 'update'])->name('update'); // Menggunakan PUT untuk update
        Route::delete('/{id}', [DataMasterController::class, 'destroy'])->name('destroy');

        // Komentari atau hapus route create dan edit halaman penuh jika sudah tidak digunakan
        // Route::get('/create', [DataMasterController::class, 'create'])->name('create');
        // Route::get('/{id}/edit', [DataMasterController::class, 'edit'])->name('edit');
    });
    //== Akhir DATA MASTER PROPERTI Routes ==


    // Data User
    Route::prefix('data-user')->name('data-user.')->group(function () {
        Route::get('/', [DataUserController::class, 'index'])->name('index');
        Route::get('/create', [DataUserController::class, 'create'])->name('create');
        Route::post('/', [DataUserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [DataUserController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{user}', [DataUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [DataUserController::class, 'destroy'])->name('destroy');
    });

    // Prediksi
    Route::prefix('prediksi')->name('prediksi.')->group(function () {
        Route::get('/create', [PredictionController::class, 'create'])->name('create');
        Route::post('/', [PredictionController::class, 'store'])->name('store');
        Route::get('/history', [PredictionController::class, 'history'])->name('history');
    });

    // Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard'); // Duplikat, sudah ada di atas

});
