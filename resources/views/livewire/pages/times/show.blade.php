<?php

use App\Exceptions\TimeDomainException;
use App\Exceptions\TimeException;
use App\Mail\ConviteTimeMail;
use App\Models\Time;
use App\Models\TimeMembroConvite;
use App\Models\User;
use App\Services\TimeService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Time $time;
    public string $nome = '';
    public string $email = '';

    public function mount(Time $time): void
    {
        $this->authorize('view', $time);
        $this->time = $time->load(['membros.usuario', 'membros.nivel']);
    }

    public function delete(TimeService $service): void
    {
        $this->authorize('delete', $this->time);

        try {
            $service->excluir((int) $this->time->id, (int) auth()->id());
        } catch (TimeDomainException|TimeException $exception) {
            $this->addError('nome', $exception->getMessage());

            return;
        }

        session()->flash('status', 'Time excluído.');
        $this->redirect(route('times.index'), navigate: true);
    }

    public function convidar(): void
    {
        $this->authorize('update', $this->time);

        $data = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $contaId = $this->time->conta_id;
        $convidado = User::query()->where('email', $data['email'])->first();

        if ($convidado !== null && $contaId !== null && $convidado->belongsToOtherConta((int) $contaId)) {
            throw ValidationException::withMessages([
                'email' => 'Este e-mail já pertence a outra conta na plataforma.',
            ]);
        }

        $convite = TimeMembroConvite::query()->create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'time_id' => $this->time->id,
            'token' => Str::random(64),
            'status' => 0,
        ]);

        $aceitarUrl = URL::temporarySignedRoute(
            'convites.aceitar',
            now()->addDays(7),
            ['convite' => $convite->id]
        );

        Mail::to($convite->email)->queue(new ConviteTimeMail($convite, $aceitarUrl));

        $this->reset(['nome', 'email']);
        session()->flash('status', 'Convite enviado.');
    }

    public function with(): array
    {
        return [
            'convites' => TimeMembroConvite::query()
                ->where('time_id', $this->time->id)
                ->where('status', 0)
                ->latest()
                ->get(),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $time->nome }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            @can('delete', $time)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-medium text-gray-900">Excluir time</h3>
                        <p class="text-sm text-gray-500 mt-1">Remove o time da conta. Esta ação pode ser revertida apenas no banco.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="Excluir este time? Membros e vínculos deixam de aparecer na interface."
                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-500"
                    >
                        Excluir time
                    </button>
                </div>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b"><h3 class="font-medium">Membros</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nível</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($time->membros as $membro)
                                <tr>
                                    <td class="px-4 py-3 text-sm">{{ $membro->usuario?->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $membro->usuario?->email }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $membro->nivel?->nome }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @can('update', $time)
                <form wire:submit="convidar" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="font-medium">Convidar membro</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nome" value="Nome" />
                            <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="email" value="E-mail" />
                            <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>
                    <x-primary-button>Enviar convite</x-primary-button>

                    @if ($convites->isNotEmpty())
                        <div class="pt-4 border-t">
                            <p class="text-sm text-gray-500 mb-2">Convites pendentes</p>
                            <ul class="text-sm space-y-1">
                                @foreach ($convites as $convite)
                                    <li>{{ $convite->nome }} &lt;{{ $convite->email }}&gt;</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            @endcan
        </div>
    </div>
</div>
