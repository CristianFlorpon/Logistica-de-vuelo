<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AeropuertoController;
use App\Http\Controllers\Api\VueloController;
use App\Http\Controllers\Api\PasajeroController;
use App\Http\Controllers\Api\AerolineaController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FlotaController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\RutaController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('dashboard', [DashboardController::class, 'index']);
Route::patch('vuelos/{vuelo}/estado', [VueloController::class, 'estado']);
Route::apiResource('vuelos', VueloController::class);
Route::apiResource('pasajeros', PasajeroController::class);
Route::apiResource('reservas', ReservaController::class);
Route::apiResource('rutas', RutaController::class);
Route::apiResource('aeropuertos', AeropuertoController::class);
Route::apiResource('flotas', FlotaController::class);
Route::apiResource('aerolineas', AerolineaController::class);
