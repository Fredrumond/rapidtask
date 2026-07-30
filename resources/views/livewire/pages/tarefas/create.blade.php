<?php

use App\Models\Prioridade;
use App\Models\Projeto;
use App\Models\Situacao;
use App\Models\Tarefa;
use App\Models\Tipo;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $titulo = '';
    public string $descricao = '';
    public ?int $projeto_id = null;
    public ?int $tipo_id = null;
    public ?int $situacao_id = 1;
    public ?int $prioridade_id = 1;
    public ?string $dt_inicio = null;
    public ?string $dt_prevista = null;
    public ?int $tempo_estimado = null;

    public function mount(): void
    {
        $this->authorize('create', Tarefa::class);
    }

    public function save(): void
    {
        $this->authorize('create', Tarefa::class);

        $data = $this->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'projeto_id' => ['required', 'exists:projetos,id'],
            'tipo_id' => ['required', 'exists:tipos,id'],
            'situacao_id' => ['required', 'exists:situacoes,id'],
            'prioridade_id' => ['required', 'exists:prioridades,id'],
            'dt_inicio' => ['nullable', 'date'],
            'dt_prevista' => ['nullable', 'date'],
            'tempo_estimado' => ['nullable', 'integer', 'min:0'],
        ]);

        Tarefa::query()->create([
            ...$data,
            'usuario_id' => auth()->id(),
            'status' => 0,
        ]);

        session()->flash('status', 'Tarefa criada.');
        $this->redirect(route('tarefas.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'projetos' => Projeto::query()->orderBy('nome')->get(),
            'tipos' => Tipo::query()->orderBy('id')->get(),
            'situacoes' => Situacao::query()->orderBy('id')->get(),
            'prioridades' => Prioridade::query()->orderBy('id')->get(),
        ];
    }
}; ?>

<div>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova tarefa</h2></x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form wire:submit="save" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <x-input-label for="titulo" value="Título" />
                    <x-text-input wire:model="titulo" id="titulo" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="projeto_id" value="Projeto" />
                    <select wire:model="projeto_id" id="projeto_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Selecione</option>
                        @foreach ($projetos as $projeto)
                            <option value="{{ $projeto->id }}">{{ $projeto->sigla }} — {{ $projeto->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="tipo_id" value="Tipo" />
                        <select wire:model="tipo_id" id="tipo_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @foreach ($tipos as $tipo)<option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="situacao_id" value="Situação" />
                        <select wire:model="situacao_id" id="situacao_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @foreach ($situacoes as $situacao)<option value="{{ $situacao->id }}">{{ $situacao->nome }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="prioridade_id" value="Prioridade" />
                        <select wire:model="prioridade_id" id="prioridade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @foreach ($prioridades as $prioridade)<option value="{{ $prioridade->id }}">{{ $prioridade->nome }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <x-input-label for="descricao" value="Descrição" />
                    <textarea wire:model="descricao" id="descricao" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
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
                        <x-input-label for="tempo_estimado" value="Tempo estimado (h)" />
                        <x-text-input wire:model="tempo_estimado" id="tempo_estimado" type="number" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="flex gap-3">
                    <x-primary-button>Salvar</x-primary-button>
                    <a href="{{ route('tarefas.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
