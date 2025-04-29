<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route Dashboard 
Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('register', function () {
    return redirect()->route('login');
})->middleware('guest')->name('register');

Route::post('register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::middleware('auth')->group(function () {
    // Route Profile 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ROUTE GET 

    // Data Master
    Route::get('/data-master', [DataMasterController::class, 'index'])
        ->name('data-master.index');

    // Sub-item Data Master
    Route::get('/data-master/properti', [DataMasterController::class, 'propertiIndex'])
        ->name('data-master.properti.index');

    // Data User
    Route::get('/data-user', [DataUserController::class, 'index'])
        ->name('data-user.index');

    // Prediksi
    Route::get('/prediksi/create', [PredictionController::class, 'create'])
        ->name('prediksi.create');

    Route::post('/prediksi', [PredictionController::class, 'store'])->name('prediksi.store');
});

require __DIR__ . '/auth.php';
