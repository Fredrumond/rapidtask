<?php

use App\Exceptions\ProjetoDomainException;
use App\Exceptions\ProjetoException;
use App\Models\Cliente;
use App\Models\Projeto;
use App\Services\ProjetoService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $nome = '';
    public string $sigla = '';
    public string $descricao = '';
    public ?int $cliente_id = null;
    public ?string $dt_inicio = null;
    public ?string $dt_prevista = null;

    public function mount(): void
    {
        $this->authorize('create', Projeto::class);
    }

    public function save(ProjetoService $service): void
    {
        $this->authorize('create', Projeto::class);

        $data = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sigla' => ['required', 'string', 'max:20'],
            'descricao' => ['nullable', 'string'],
            'cliente_id' => ['required', 'exists:clientes,id'],
            'dt_inicio' => ['nullable', 'date'],
            'dt_prevista' => ['nullable', 'date'],
        ]);

        try {
            $service->create((int) auth()->id(), $data);
        } catch (ProjetoDomainException|ProjetoException $exception) {
            $this->addError('nome', $exception->getMessage());

            return;
        }

        session()->flash('status', 'Projeto criado.');
        $this->redirect(route('projetos.index'), navigate: true);
    }

    public function with(): array
    {
        return ['clientes' => Cliente::query()->orderBy('nome')->get()];
    }
}; ?>

<div>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo projeto</h2></x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form wire:submit="save" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <x-input-label for="nome" value="Nome" />
                    <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="sigla" value="Sigla" />
                    <x-text-input wire:model="sigla" id="sigla" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('sigla')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="cliente_id" value="Cliente" />
                    <select wire:model="cliente_id" id="cliente_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Selecione</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="descricao" value="Descrição" />
                    <textarea wire:model="descricao" id="descricao" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4"></textarea>
                    <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="dt_inicio" value="Início" />
                        <x-text-input wire:model="dt_inicio" id="dt_inicio" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('dt_inicio')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="dt_prevista" value="Prevista" />
                        <x-text-input wire:model="dt_prevista" id="dt_prevista" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('dt_prevista')" class="mt-2" />
                    </div>
                </div>
                <div class="flex gap-3">
                    <x-primary-button>Salvar</x-primary-button>
                    <a href="{{ route('projetos.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
