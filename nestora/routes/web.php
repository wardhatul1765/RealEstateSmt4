<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Landing ke Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Auth routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// Dashboard (butuh login)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Data Master
Route::get('/input-data-master', [DataMasterController::class, 'inputDataMaster'])->middleware('auth')->name('input-data-master');
Route::get('/laporan-data-master', [DataMasterController::class, 'laporanDataMaster'])->middleware('auth')->name('laporan-data-master');

// Pengguna
// Route::get('/pengguna', [PenggunaController::class, 'index'])->middleware('auth')->name('pengguna.index');
