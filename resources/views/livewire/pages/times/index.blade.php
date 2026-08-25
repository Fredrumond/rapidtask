<?php

use App\Exceptions\ContaDomainException;
use App\Exceptions\ContaException;
use App\Exceptions\TimeDomainException;
use App\Exceptions\TimeException;
use App\Models\Time;
use App\Services\TimeService;
use App\Support\CurrentTeam;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $nome = '';

    public function create(TimeService $service): void
    {
        $this->authorize('create', Time::class);

        $data = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        try {
            $service->criar((int) auth()->id(), $data['nome']);
        } catch (TimeDomainException|TimeException|ContaDomainException|ContaException $exception) {
            $this->addError('nome', $exception->getMessage());

            return;
        }

        session()->flash('status', 'Time criado.');
        $this->redirect(route('times.show', CurrentTeam::id()), navigate: true);
    }

    public function with(): array
    {
        $this->authorize('viewAny', Time::class);

        return [
            'times' => auth()->user()->times()->orderBy('nome')->get(),
        ];
    }
}; ?>

<div>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Times</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form wire:submit="create" class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <x-input-label for="nome" value="Novo time" />
                    <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" placeholder="Nome do time" required />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>
                <x-primary-button>Criar</x-primary-button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                @forelse ($times as $time)
                    <a href="{{ route('times.show', $time) }}" wire:navigate class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                        <span class="font-medium text-gray-900">{{ $time->nome }}</span>
                        <span class="text-sm text-indigo-600">Abrir</span>
                    </a>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-gray-500">Você ainda não participa de nenhum time.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
