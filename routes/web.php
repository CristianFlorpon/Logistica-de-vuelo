<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Landing (página bonita Aerobooking)
Route::get('/', function () {
    return view('inicio');
});

// 🔹 Sistema principal (tu simulación)
Route::get('/inicio', function () {
    return view('simulacion'); // ✅ tu sistema real
})->middleware(['auth'])->name('inicio');

// 🔹 Dashboard (puedes dejarlo o ignorarlo)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// 🔹 Perfil (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔹 Auth (login, register, etc.)
require __DIR__.'/auth.php';