<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ManajemenPropertiController; // <-- Pastikan ini ada

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Auth Routes (Login, Register, dll) ---
Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('register', function () {
    return redirect()->route('login');
})->middleware('guest')->name('register');

Route::post('register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

require __DIR__ . '/auth.php';

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () { // Sesuaikan middleware jika perlu

    // Route Profile
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

    // Data Master (Disarankan untuk digrup juga nanti)
    Route::get('/data-master', [DataMasterController::class, 'index'])
        ->name('data-master.index');
    Route::get('/data-master/properti', [DataMasterController::class, 'propertiIndex'])
        ->name('data-master.properti.index');

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

}); // Akhir grup middleware 'auth' 