<?php

use App\Models\Prioridade;
use App\Models\Projeto;
use App\Models\Situacao;
use App\Models\Tarefa;
use App\Models\Tipo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $busca = '';

    public function arquivar(int $id): void
    {
        $tarefa = Tarefa::query()->findOrFail($id);
        $this->authorize('update', $tarefa);
        $tarefa->update(['status' => 1]);
        session()->flash('status', 'Tarefa arquivada.');
    }

    public function with(): array
    {
        $this->authorize('viewAny', Tarefa::class);

        return [
            'tarefas' => Tarefa::query()
                ->with(['projeto', 'situacao', 'prioridade', 'tipo'])
                ->where('status', 0)
                ->when($this->busca, fn ($q) => $q->where('titulo', 'like', '%'.$this->busca.'%'))
                ->latest()
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tarefas</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <input type="search" wire:model.live.debounce.300ms="busca" placeholder="Buscar..." class="w-full sm:w-80 rounded-md border-gray-300 shadow-sm" />
                <div class="flex items-center gap-3">
                    <a href="{{ route('tarefas.arquivadas') }}" wire:navigate class="text-sm text-gray-600 hover:underline">Arquivadas</a>
                    <a href="{{ route('tarefas.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500">
                        Nova tarefa
                    </a>
                </div>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Projeto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Situação</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridade</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($tarefas as $tarefa)
                                <tr>
                                    <td class="px-4 py-3 text-sm">{{ $tarefa->titulo }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $tarefa->projeto?->sigla }}</td>
                                    <td class="px-4 py-3 text-sm"><span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-blue-50 text-blue-700">{{ $tarefa->situacao?->nome }}</span></td>
                                    <td class="px-4 py-3 text-sm"><span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700">{{ $tarefa->prioridade?->nome }}</span></td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('tarefas.edit', $tarefa) }}" wire:navigate class="text-indigo-600 hover:underline">Editar</a>
                                        <button wire:click="arquivar({{ $tarefa->id }})" class="text-gray-600 hover:underline">Arquivar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Nenhuma tarefa.
                                        <a href="{{ route('tarefas.create') }}" wire:navigate class="block mt-2 text-indigo-600 hover:underline">Criar a primeira tarefa</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $tarefas->links() }}</div>
            </div>
        </div>
    </div>
</div>
