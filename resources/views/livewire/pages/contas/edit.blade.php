<?php

use App\Exceptions\ContaDomainException;
use App\Exceptions\ContaException;
use App\Models\Conta;
use App\Services\ContaService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Conta $conta;

    public string $nome = '';

    public function mount(Conta $conta): void
    {
        abort_unless(auth()->id() === $conta->usuario_id, 403);

        $this->conta = $conta;
        $this->nome = $conta->nome;
    }

    public function save(ContaService $service): void
    {
        abort_unless(auth()->id() === $this->conta->usuario_id, 403);

        $data = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        try {
            $service->renomear((int) $this->conta->id, $data['nome'], (int) auth()->id());
        } catch (ContaDomainException|ContaException $exception) {
            $this->addError('nome', $exception->getMessage());

            return;
        }

        session()->flash('status', 'Conta atualizada.');
        $this->redirect(route('contas.edit', $this->conta), navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuração da conta</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form wire:submit="save" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <x-input-label for="nome" value="Nome da conta" />
                    <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" required autofocus />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Salvar</x-primary-button>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <livewire:contas.manage-api-token-form :conta="$conta" />
            </div>
        </div>
    </div>
</div>
