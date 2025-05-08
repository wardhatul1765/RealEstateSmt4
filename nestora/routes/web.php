<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\Auth\RegisteredUserController;
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
// Hapus route register yang redirect ke login jika ingin mengaktifkan registrasi dari landing page
// Jika tetap ingin registrasi diarahkan ke login, biarkan saja.
// Route::get('register', function () {
//     return redirect()->route('login');
// })->middleware('guest')->name('register');

// Route::post('register', [RegisteredUserController::class, 'store'])
//     ->middleware('guest');

// Memuat route otentikasi standar (login, logout, password reset, etc.)
require __DIR__ . '/auth.php';

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {

    // --- Dashboard Route ---
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
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

    Route::prefix('data-master/properti')->name('data-master.properti.')->group(function () {
        Route::get('/', [DataMasterController::class, 'propertiIndex'])->name('index');
        Route::get('/create', [DataMasterController::class, 'create'])->name('create');
        Route::post('/', [DataMasterController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DataMasterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DataMasterController::class, 'update'])->name('update');
        Route::delete('/{id}', [DataMasterController::class, 'destroy'])->name('destroy');
    });
    


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

    

});