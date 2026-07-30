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
    public Tarefa $tarefa;
    public string $titulo = '';
    public string $descricao = '';
    public ?int $projeto_id = null;
    public ?int $tipo_id = null;
    public ?int $situacao_id = null;
    public ?int $prioridade_id = null;
    public ?string $dt_inicio = null;
    public ?string $dt_prevista = null;
    public ?string $dt_fim = null;
    public ?int $tempo_estimado = null;

    public function mount(Tarefa $tarefa): void
    {
        $this->authorize('update', $tarefa);
        $this->tarefa = $tarefa;
        $this->titulo = $tarefa->titulo;
        $this->descricao = (string) $tarefa->descricao;
        $this->projeto_id = $tarefa->projeto_id;
        $this->tipo_id = $tarefa->tipo_id;
        $this->situacao_id = $tarefa->situacao_id;
        $this->prioridade_id = $tarefa->prioridade_id;
        $this->dt_inicio = optional($tarefa->dt_inicio)?->format('Y-m-d');
        $this->dt_prevista = optional($tarefa->dt_prevista)?->format('Y-m-d');
        $this->dt_fim = optional($tarefa->dt_fim)?->format('Y-m-d');
        $this->tempo_estimado = $tarefa->tempo_estimado;
    }

    public function save(): void
    {
        $this->authorize('update', $this->tarefa);

        $data = $this->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'projeto_id' => ['required', 'exists:projetos,id'],
            'tipo_id' => ['required', 'exists:tipos,id'],
            'situacao_id' => ['required', 'exists:situacoes,id'],
            'prioridade_id' => ['required', 'exists:prioridades,id'],
            'dt_inicio' => ['nullable', 'date'],
            'dt_prevista' => ['nullable', 'date'],
            'dt_fim' => ['nullable', 'date'],
            'tempo_estimado' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->tarefa->update($data);
        session()->flash('status', 'Tarefa atualizada.');
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
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar tarefa</h2></x-slot>
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
                        @foreach ($projetos as $projeto)
                            <option value="{{ $projeto->id }}">{{ $projeto->sigla }} — {{ $projeto->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="tipo_id" value="Tipo" />
                        <select wire:model="tipo_id" id="tipo_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($tipos as $tipo)<option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="situacao_id" value="Situação" />
                        <select wire:model="situacao_id" id="situacao_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($situacoes as $situacao)<option value="{{ $situacao->id }}">{{ $situacao->nome }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="prioridade_id" value="Prioridade" />
                        <select wire:model="prioridade_id" id="prioridade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($prioridades as $prioridade)<option value="{{ $prioridade->id }}">{{ $prioridade->nome }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <x-input-label for="descricao" value="Descrição" />
                    <textarea wire:model="descricao" id="descricao" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <x-primary-button>Salvar</x-primary-button>
                    <a href="{{ route('tarefas.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
