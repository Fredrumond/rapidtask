<?php

use App\Models\Tarefa;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public function recuperar(int $id): void
    {
        $tarefa = Tarefa::query()->findOrFail($id);
        $this->authorize('update', $tarefa);
        $tarefa->update(['status' => 0]);
        session()->flash('status', 'Tarefa recuperada.');
    }

    public function with(): array
    {
        $this->authorize('viewAny', Tarefa::class);

        return [
            'tarefas' => Tarefa::query()
                ->with(['projeto', 'situacao', 'prioridade'])
                ->where('status', 1)
                ->latest()
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tarefas arquivadas</h2>
            <a href="{{ route('tarefas.index') }}" wire:navigate class="text-sm text-indigo-600 hover:underline">Voltar</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Situação</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridade</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($tarefas as $tarefa)
                                <tr>
                                    <td class="px-4 py-3 text-sm">{{ $tarefa->titulo }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $tarefa->situacao?->nome }}</td>
                                    <td class="px-4 py-3 text-sm"><span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700">{{ $tarefa->prioridade?->nome }}</span></td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <button wire:click="recuperar({{ $tarefa->id }})" class="text-indigo-600 hover:underline">Recuperar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Nenhuma tarefa arquivada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $tarefas->links() }}</div>
            </div>
        </div>
    </div>
</div>
