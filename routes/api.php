<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\EntradaController;
use App\Http\Controllers\Api\SalidaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('ubicaciones', UbicacionController::class);
Route::apiResource('stocks', StockController::class);
Route::apiResource('entradas', EntradaController::class);
Route::apiResource('salidas', SalidaController::class);