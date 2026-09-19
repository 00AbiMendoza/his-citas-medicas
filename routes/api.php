<?php

use App\Http\Controllers\Api\CitaController;
use Illuminate\Support\Facades\Route;

Route::prefix('citas')->group(function () {
    Route::get('/', [CitaController::class, 'index']);
    Route::post('/', [CitaController::class, 'store']);
    Route::get('/{cita}', [CitaController::class, 'show']);
    Route::put('/{cita}', [CitaController::class, 'update']);
    Route::patch('/{cita}/estado', [CitaController::class, 'cambiarEstado']);
});
