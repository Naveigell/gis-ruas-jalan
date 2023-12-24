<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\LoginController::class, 'index'])->name('login.index');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login.store');
Route::get('/logout', [\App\Http\Controllers\LogoutController::class, 'store'])->name('logout.store');

Route::resource('register', \App\Http\Controllers\RegisterController::class)->only('index', 'store');

Route::middleware('auth')->group(function () {
    Route::resource('roads', \App\Http\Controllers\RoadController::class);
});
