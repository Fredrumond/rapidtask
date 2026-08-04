<?php

use App\Http\Controllers\TarefaController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/tokens', [TokenController::class, 'store']);
    Route::delete('/tokens', [TokenController::class, 'destroy']);

    Route::middleware('api.team')->prefix('tarefas')->group(function (): void {
        Route::get('/', [TarefaController::class, 'index']);
        Route::post('/', [TarefaController::class, 'store']);
        Route::get('/{tarefa_id}', [TarefaController::class, 'show'])->whereNumber('tarefa_id');
        Route::put('/{tarefa_id}', [TarefaController::class, 'update'])->whereNumber('tarefa_id');
        Route::delete('/{tarefa_id}', [TarefaController::class, 'destroy'])->whereNumber('tarefa_id');
    });
});
