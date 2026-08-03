<?php

use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/tokens', [TokenController::class, 'store']);
    Route::delete('/tokens', [TokenController::class, 'destroy']);
});
