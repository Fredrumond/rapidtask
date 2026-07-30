<?php

use App\Models\Cliente;
use App\Models\Projeto;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Projeto $projeto;
    public string $nome = '';
    public string $sigla = '';
    public string $descricao = '';
    public ?int $cliente_id = null;
    public ?string $dt_inicio = null;
    public ?string $dt_prevista = null;
    public ?string $dt_fim = null;

    public function mount(Projeto $projeto): void
    {
        $this->authorize('update', $projeto);
        $this->projeto = $projeto;
        $this->nome = $projeto->nome;
        $this->sigla = $projeto->sigla;
        $this->descricao = (string) $projeto->descricao;
        $this->cliente_id = $projeto->cliente_id;
        $this->dt_inicio = optional($projeto->dt_inicio)?->format('Y-m-d');
        $this->dt_prevista = optional($projeto->dt_prevista)?->format('Y-m-d');
        $this->dt_fim = optional($projeto->dt_fim)?->format('Y-m-d');
    }

    public function save(): void
    {
        $this->authorize('update', $this->projeto);

        $data = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sigla' => ['required', 'string', 'max:20'],
            'descricao' => ['nullable', 'string'],
            'cliente_id' => ['required', 'exists:clientes,id'],
            'dt_inicio' => ['nullable', 'date'],
            'dt_prevista' => ['nullable', 'date'],
            'dt_fim' => ['nullable', 'date'],
        ]);

        $this->projeto->update($data);
        session()->flash('status', 'Projeto atualizado.');
        $this->redirect(route('projetos.index'), navigate: true);
    }

    public function with(): array
    {
        return ['clientes' => Cliente::query()->orderBy('nome')->get()];
    }
}; ?>

<div>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar projeto</h2></x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form wire:submit="save" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <x-input-label for="nome" value="Nome" />
                    <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="sigla" value="Sigla" />
                    <x-text-input wire:model="sigla" id="sigla" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="cliente_id" value="Cliente" />
                    <select wire:model="cliente_id" id="cliente_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="descricao" value="Descrição" />
                    <textarea wire:model="descricao" id="descricao" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="dt_inicio" value="Início" />
                        <x-text-input wire:model="dt_inicio" id="dt_inicio" type="date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="dt_prevista" value="Prevista" />
                        <x-text-input wire:model="dt_prevista" id="dt_prevista" type="date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="dt_fim" value="Fim" />
                        <x-text-input wire:model="dt_fim" id="dt_fim" type="date" class="mt-1 block w-full" />
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
