<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProjetoArquivoController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\TarefaComentarioController;
use App\Http\Controllers\TarefaController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/tokens', [TokenController::class, 'store']);
    Route::delete('/tokens', [TokenController::class, 'destroy']);

    Route::middleware('api.team')->prefix('clientes')->group(function (): void {
        Route::get('/', [ClienteController::class, 'index']);
        Route::post('/', [ClienteController::class, 'store']);
        Route::get('/{cliente_id}', [ClienteController::class, 'show'])->whereNumber('cliente_id');
        Route::put('/{cliente_id}', [ClienteController::class, 'update'])->whereNumber('cliente_id');
        Route::delete('/{cliente_id}', [ClienteController::class, 'destroy'])->whereNumber('cliente_id');
    });

    Route::middleware('api.team')->prefix('projetos')->group(function (): void {
        Route::get('/', [ProjetoController::class, 'index']);
        Route::post('/', [ProjetoController::class, 'store']);
        Route::get('/{projeto_id}', [ProjetoController::class, 'show'])->whereNumber('projeto_id');
        Route::put('/{projeto_id}', [ProjetoController::class, 'update'])->whereNumber('projeto_id');
        Route::delete('/{projeto_id}', [ProjetoController::class, 'destroy'])->whereNumber('projeto_id');

        Route::get('/{projeto_id}/arquivos', [ProjetoArquivoController::class, 'index'])->whereNumber('projeto_id');
        Route::post('/{projeto_id}/arquivos', [ProjetoArquivoController::class, 'store'])->whereNumber('projeto_id');
        Route::delete('/{projeto_id}/arquivos/{arquivo_id}', [ProjetoArquivoController::class, 'destroy'])
            ->whereNumber(['projeto_id', 'arquivo_id']);
    });

    Route::middleware('api.team')->prefix('tarefas')->group(function (): void {
        Route::get('/', [TarefaController::class, 'index']);
        Route::post('/', [TarefaController::class, 'store']);
        Route::get('/{tarefa_id}', [TarefaController::class, 'show'])->whereNumber('tarefa_id');
        Route::put('/{tarefa_id}', [TarefaController::class, 'update'])->whereNumber('tarefa_id');
        Route::delete('/{tarefa_id}', [TarefaController::class, 'destroy'])->whereNumber('tarefa_id');

        Route::get('/{tarefa_id}/comentarios', [TarefaComentarioController::class, 'index'])->whereNumber('tarefa_id');
        Route::post('/{tarefa_id}/comentarios', [TarefaComentarioController::class, 'store'])->whereNumber('tarefa_id');
        Route::put('/{tarefa_id}/comentarios/{comentario_id}', [TarefaComentarioController::class, 'update'])
            ->whereNumber(['tarefa_id', 'comentario_id']);
        Route::delete('/{tarefa_id}/comentarios/{comentario_id}', [TarefaComentarioController::class, 'destroy'])
            ->whereNumber(['tarefa_id', 'comentario_id']);
    });
});
