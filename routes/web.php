<?php

use App\Http\Controllers\ArquivoDownloadController;
use App\Http\Controllers\ConviteController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::get('/convites/{convite}/aceitar', [ConviteController::class, 'aceitar'])
    ->middleware(['signed', 'auth'])
    ->name('convites.aceitar');

Route::get('/convites/{convite}/recusar', [ConviteController::class, 'recusar'])
    ->middleware(['signed', 'auth'])
    ->name('convites.recusar');

Route::middleware(['auth'])->group(function () {
    Volt::route('dashboard', 'pages.dashboard')->name('dashboard');

    Volt::route('clientes', 'pages.clientes.index')->name('clientes.index');
    Volt::route('clientes/criar', 'pages.clientes.create')->name('clientes.create');
    Volt::route('clientes/{cliente}/editar', 'pages.clientes.edit')->name('clientes.edit');

    Volt::route('projetos', 'pages.projetos.index')->name('projetos.index');
    Volt::route('projetos/criar', 'pages.projetos.create')->name('projetos.create');
    Volt::route('projetos/{projeto}', 'pages.projetos.show')->name('projetos.show');
    Volt::route('projetos/{projeto}/editar', 'pages.projetos.edit')->name('projetos.edit');

    Volt::route('tarefas', 'pages.tarefas.index')->name('tarefas.index');
    Volt::route('tarefas/criar', 'pages.tarefas.create')->name('tarefas.create');
    Volt::route('tarefas/arquivadas', 'pages.tarefas.arquivadas')->name('tarefas.arquivadas');
    Volt::route('tarefas/{tarefa}', 'pages.tarefas.show')->name('tarefas.show');
    Volt::route('tarefas/{tarefa}/editar', 'pages.tarefas.edit')->name('tarefas.edit');

    Volt::route('times', 'pages.times.index')->name('times.index');
    Volt::route('times/{time}', 'pages.times.show')->name('times.show');

    Volt::route('contas/{conta}/editar', 'pages.contas.edit')->name('contas.edit');

    Volt::route('versoes', 'pages.versoes.index')->name('versoes.index');
    Route::redirect('versions', 'versoes');

    Route::view('profile', 'profile')->name('profile');

    Route::get('/arquivos/{arquivo}/download', ArquivoDownloadController::class)
        ->name('arquivos.download');
});

require __DIR__.'/auth.php';
