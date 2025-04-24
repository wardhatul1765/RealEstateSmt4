<?php

use App\Http\Controllers\dashboardcontroller;
use App\Http\Controllers\penggunacontroller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/main', function () {
    return view('konten');
});

route::get('/dashboard', [dashboardcontroller::class, 'index']);
route::get('/pengguna', [penggunacontroller::class, 'index']);
