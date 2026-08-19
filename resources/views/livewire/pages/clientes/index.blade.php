<?php

use App\Models\Cliente;
use App\Services\ClienteService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $busca = '';

    public function delete(int $id, ClienteService $service): void
    {
        $cliente = Cliente::query()->findOrFail($id);
        $this->authorize('delete', $cliente);
        $service->delete($id);
        session()->flash('status', 'Cliente excluído.');
    }

    public function with(): array
    {
        $this->authorize('viewAny', Cliente::class);

        return [
            'clientes' => Cliente::query()
                ->when($this->busca, fn ($q) => $q->where('nome', 'like', '%'.$this->busca.'%'))
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clientes</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <input type="search" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nome..." class="w-full sm:w-80 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                <a href="{{ route('clientes.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500">
                    Novo cliente
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $cliente->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $cliente->telefone }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('clientes.edit', $cliente) }}" wire:navigate class="text-indigo-600 hover:underline">Editar</a>
                                        <button wire:click="delete({{ $cliente->id }})" wire:confirm="Excluir este cliente?" class="text-red-600 hover:underline">Excluir</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Nenhum cliente encontrado.
                                        <a href="{{ route('clientes.create') }}" wire:navigate class="block mt-2 text-indigo-600 hover:underline">Criar o primeiro cliente</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $clientes->links() }}</div>
            </div>
        </div>
    </div>
</div>
