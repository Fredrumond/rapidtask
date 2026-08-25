<?php

use App\Models\Projeto;
use App\Services\ProjetoService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $busca = '';

    public function delete(int $id, ProjetoService $service): void
    {
        $projeto = Projeto::query()->findOrFail($id);
        $this->authorize('delete', $projeto);
        $service->delete($id);
        session()->flash('status', 'Projeto excluído.');
    }

    public function with(): array
    {
        $this->authorize('viewAny', Projeto::class);

        return [
            'projetos' => Projeto::query()
                ->with('cliente')
                ->when($this->busca, fn ($q) => $q->where('nome', 'like', '%'.$this->busca.'%'))
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Projetos</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <input type="search" wire:model.live.debounce.300ms="busca" placeholder="Buscar..." class="w-full sm:w-80 rounded-md border-gray-300 shadow-sm" />
                <a href="{{ route('projetos.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500">
                    Novo projeto
                </a>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sigla</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($projetos as $projeto)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium">{{ $projeto->sigla }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $projeto->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $projeto->cliente?->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('projetos.show', $projeto) }}" wire:navigate class="text-indigo-600 hover:underline">Detalhe</a>
                                        <a href="{{ route('projetos.edit', $projeto) }}" wire:navigate class="text-indigo-600 hover:underline">Editar</a>
                                        <button wire:click="delete({{ $projeto->id }})" wire:confirm="Excluir este projeto?" class="text-red-600 hover:underline">Excluir</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Nenhum projeto.
                                        <a href="{{ route('projetos.create') }}" wire:navigate class="block mt-2 text-indigo-600 hover:underline">Criar o primeiro projeto</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $projetos->links() }}</div>
            </div>
        </div>
    </div>
</div>
