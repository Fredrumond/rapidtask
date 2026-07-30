<?php

use App\Models\Projeto;
use App\Models\Situacao;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Projeto $projeto;

    public function mount(Projeto $projeto): void
    {
        $this->authorize('view', $projeto);
        $this->projeto = $projeto->load(['cliente', 'tarefas.prioridade', 'tarefas.situacao']);
    }

    public function with(): array
    {
        $situacoes = Situacao::query()->orderBy('id')->get();
        $tarefasPorSituacao = $this->projeto->tarefas
            ->where('status', 0)
            ->groupBy('situacao_id');

        return compact('situacoes', 'tarefasPorSituacao');
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $projeto->sigla }} — {{ $projeto->nome }}</h2>
                <p class="text-sm text-gray-500">Cliente: {{ $projeto->cliente?->nome }}</p>
            </div>
            <a href="{{ route('projetos.edit', $projeto) }}" wire:navigate class="text-indigo-600 text-sm hover:underline">Editar</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <p class="text-gray-700">{{ $projeto->descricao }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($situacoes as $situacao)
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h3 class="font-medium text-sm text-gray-700 mb-3">{{ $situacao->nome }}</h3>
                        <div class="space-y-2">
                            @forelse (($tarefasPorSituacao[$situacao->id] ?? collect()) as $tarefa)
                                <a href="{{ route('tarefas.edit', $tarefa) }}" wire:navigate
                                   data-id="{{ $tarefa->id }}"
                                   class="block bg-white rounded-md p-3 shadow-sm border border-gray-100 hover:border-indigo-300">
                                    <p class="text-sm font-medium text-gray-900">{{ $tarefa->titulo }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $tarefa->prioridade?->nome }}</p>
                                </a>
                            @empty
                                <p class="text-xs text-gray-400">Sem tarefas</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
