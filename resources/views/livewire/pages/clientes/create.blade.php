<?php

use App\Models\Cliente;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $nome = '';
    public string $email = '';
    public string $telefone = '';

    public function mount(): void
    {
        $this->authorize('create', Cliente::class);
    }

    public function save(): void
    {
        $this->authorize('create', Cliente::class);

        $data = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:255'],
        ]);

        Cliente::query()->create([
            ...$data,
            'usuario_id' => auth()->id(),
            'time_id' => current_time_id(),
        ]);

        session()->flash('status', 'Cliente criado.');
        $this->redirect(route('clientes.index'), navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo cliente</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form wire:submit="save" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <x-input-label for="nome" value="Nome" />
                    <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="E-mail" />
                    <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="telefone" value="Telefone" />
                    <x-text-input wire:model="telefone" id="telefone" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                </div>
                <div class="flex gap-3">
                    <x-primary-button>Salvar</x-primary-button>
                    <a href="{{ route('clientes.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
