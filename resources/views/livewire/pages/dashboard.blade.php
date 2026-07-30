<?php

use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\Tarefa;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public int $clientes = 0;
    public int $projetos = 0;
    public int $tarefasAbertas = 0;
    public int $tarefasArquivadas = 0;

    public function mount(): void
    {
        if (! auth()->user()->times()->exists()) {
            $this->redirect(route('times.index'), navigate: true);

            return;
        }

        $this->clientes = Cliente::query()->count();
        $this->projetos = Projeto::query()->count();
        $this->tarefasAbertas = Tarefa::query()->where('status', 0)->count();
        $this->tarefasArquivadas = Tarefa::query()->where('status', 1)->count();
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Clientes</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $clientes }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Projetos</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $projetos }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tarefas abertas</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $tarefasAbertas }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tarefas arquivadas</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $tarefasArquivadas }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
