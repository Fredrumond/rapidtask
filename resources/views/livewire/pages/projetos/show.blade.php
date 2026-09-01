<?php

use App\Exceptions\ProjetoAnotacaoDomainException;
use App\Models\Projeto;
use App\Models\ProjetoAnotacao;
use App\Models\Situacao;
use App\Services\ProjetoAnotacaoService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Projeto $projeto;

    public string $novaAnotacao = '';

    public ?int $editandoId = null;

    public string $editandoTexto = '';

    public function mount(Projeto $projeto): void
    {
        $this->authorize('view', $projeto);
        $this->projeto = $projeto->load(['cliente', 'tarefas.prioridade', 'tarefas.situacao']);
    }

    public function criarAnotacao(ProjetoAnotacaoService $service): void
    {
        $this->authorize('create', ProjetoAnotacao::class);

        $data = $this->validate([
            'novaAnotacao' => ['required', 'string'],
        ]);

        try {
            $service->create((int) auth()->id(), (int) $this->projeto->id, [
                'anotacao' => $data['novaAnotacao'],
            ]);
        } catch (ProjetoAnotacaoDomainException $exception) {
            $this->addError('novaAnotacao', $exception->getMessage());

            return;
        }

        $this->reset('novaAnotacao');
        session()->flash('status', 'Anotação adicionada.');
    }

    public function iniciarEdicao(int $anotacaoId): void
    {
        $anotacao = ProjetoAnotacao::query()
            ->where('projeto_id', $this->projeto->id)
            ->findOrFail($anotacaoId);

        $this->authorize('update', $anotacao);

        $this->editandoId = $anotacao->id;
        $this->editandoTexto = $anotacao->anotacao;
    }

    public function cancelarEdicao(): void
    {
        $this->reset('editandoId', 'editandoTexto');
    }

    public function salvarEdicao(ProjetoAnotacaoService $service): void
    {
        $anotacao = ProjetoAnotacao::query()
            ->where('projeto_id', $this->projeto->id)
            ->findOrFail($this->editandoId);

        $this->authorize('update', $anotacao);

        $data = $this->validate([
            'editandoTexto' => ['required', 'string'],
        ]);

        try {
            $service->update((int) $this->projeto->id, (int) $anotacao->id, [
                'anotacao' => $data['editandoTexto'],
            ]);
        } catch (ProjetoAnotacaoDomainException $exception) {
            $this->addError('editandoTexto', $exception->getMessage());

            return;
        }

        $this->reset('editandoId', 'editandoTexto');
        session()->flash('status', 'Anotação atualizada.');
    }

    public function excluirAnotacao(int $anotacaoId, ProjetoAnotacaoService $service): void
    {
        $anotacao = ProjetoAnotacao::query()
            ->where('projeto_id', $this->projeto->id)
            ->findOrFail($anotacaoId);

        $this->authorize('delete', $anotacao);

        $service->delete((int) $this->projeto->id, $anotacaoId);

        if ($this->editandoId === $anotacaoId) {
            $this->reset('editandoId', 'editandoTexto');
        }

        session()->flash('status', 'Anotação excluída.');
    }

    public function with(): array
    {
        $situacoes = Situacao::query()->orderBy('id')->get();
        $tarefasPorSituacao = $this->projeto->tarefas
            ->where('status', 0)
            ->groupBy('situacao_id');
        $anotacoes = app(ProjetoAnotacaoService::class)->list((int) $this->projeto->id);

        return compact('situacoes', 'tarefasPorSituacao', 'anotacoes');
    }
}; ?>

<div>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $projeto->sigla }} — {{ $projeto->nome }}</h2>
            <p class="text-sm text-gray-500">Cliente: {{ $projeto->cliente?->nome }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-gray-700">{{ $projeto->descricao }}</p>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('projetos.edit', $projeto) }}" wire:navigate class="text-sm text-gray-600 hover:underline">Editar</a>
                    <a href="{{ route('tarefas.create', ['projeto_id' => $projeto->id]) }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500">
                        Nova tarefa
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($situacoes as $situacao)
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h3 class="font-medium text-sm text-gray-700 mb-3">{{ $situacao->nome }}</h3>
                        <div class="space-y-2">
                            @forelse (($tarefasPorSituacao[$situacao->id] ?? collect()) as $tarefa)
                                <a href="{{ route('tarefas.show', $tarefa) }}" wire:navigate
                                   data-id="{{ $tarefa->id }}"
                                   class="block bg-white rounded-md p-3 shadow-sm border border-gray-100 hover:border-indigo-300">
                                    <p class="text-sm font-medium text-gray-900">{{ $tarefa->titulo }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $tarefa->prioridade?->nome }}</p>
                                </a>
                            @empty
                                <p class="text-xs text-gray-400">Sem tarefas</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">Anotações</h3>

                <form wire:submit="criarAnotacao" class="space-y-3">
                    <div>
                        <x-input-label for="novaAnotacao" value="Nova anotação" />
                        <textarea
                            wire:model="novaAnotacao"
                            id="novaAnotacao"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        ></textarea>
                        @error('novaAnotacao')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-primary-button>Adicionar anotação</x-primary-button>
                </form>

                <div class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($anotacoes as $anotacao)
                        <div class="py-4 space-y-2" wire:key="anotacao-{{ $anotacao->id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $anotacao->usuario?->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $anotacao->created_at?->format('d/m/Y H:i') }}</p>
                                </div>
                                @can('update', $anotacao)
                                    <div class="flex items-center gap-2 text-sm shrink-0">
                                        @if ($editandoId !== $anotacao->id)
                                            <button type="button" wire:click="iniciarEdicao({{ $anotacao->id }})" class="text-indigo-600 hover:underline">Editar</button>
                                            <button type="button" wire:click="excluirAnotacao({{ $anotacao->id }})" wire:confirm="Excluir esta anotação?" class="text-red-600 hover:underline">Excluir</button>
                                        @endif
                                    </div>
                                @endcan
                            </div>

                            @if ($editandoId === $anotacao->id)
                                <form wire:submit="salvarEdicao" class="space-y-3">
                                    <textarea
                                        wire:model="editandoTexto"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        required
                                    ></textarea>
                                    @error('editandoTexto')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <div class="flex gap-3">
                                        <x-primary-button>Salvar</x-primary-button>
                                        <button type="button" wire:click="cancelarEdicao" class="text-sm text-gray-600 hover:underline">Cancelar</button>
                                    </div>
                                </form>
                            @else
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $anotacao->anotacao }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-sm text-gray-500">Nenhuma anotação ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
