<?php

use App\Exceptions\TarefaComentarioDomainException;
use App\Models\Tarefa;
use App\Models\TarefaComentario;
use App\Services\TarefaComentarioService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Tarefa $tarefa;

    public string $novoComentario = '';

    public ?int $editandoId = null;

    public string $editandoTexto = '';

    public function mount(Tarefa $tarefa): void
    {
        $this->authorize('view', $tarefa);
        $this->tarefa = $tarefa->load(['projeto', 'situacao', 'prioridade', 'tipo', 'usuario']);
    }

    public function criarComentario(TarefaComentarioService $service): void
    {
        $this->authorize('create', TarefaComentario::class);

        $data = $this->validate([
            'novoComentario' => ['required', 'string'],
        ]);

        try {
            $service->create((int) auth()->id(), (int) $this->tarefa->id, [
                'comentario' => $data['novoComentario'],
            ]);
        } catch (TarefaComentarioDomainException $exception) {
            $this->addError('novoComentario', $exception->getMessage());

            return;
        }

        $this->reset('novoComentario');
        session()->flash('status', 'Comentário adicionado.');
    }

    public function iniciarEdicao(int $comentarioId): void
    {
        $comentario = TarefaComentario::query()
            ->where('tarefa_id', $this->tarefa->id)
            ->findOrFail($comentarioId);

        $this->authorize('update', $comentario);

        $this->editandoId = $comentario->id;
        $this->editandoTexto = $comentario->comentario;
    }

    public function cancelarEdicao(): void
    {
        $this->reset('editandoId', 'editandoTexto');
    }

    public function salvarEdicao(TarefaComentarioService $service): void
    {
        $comentario = TarefaComentario::query()
            ->where('tarefa_id', $this->tarefa->id)
            ->findOrFail($this->editandoId);

        $this->authorize('update', $comentario);

        $data = $this->validate([
            'editandoTexto' => ['required', 'string'],
        ]);

        try {
            $service->update((int) $this->tarefa->id, (int) $comentario->id, [
                'comentario' => $data['editandoTexto'],
            ]);
        } catch (TarefaComentarioDomainException $exception) {
            $this->addError('editandoTexto', $exception->getMessage());

            return;
        }

        $this->reset('editandoId', 'editandoTexto');
        session()->flash('status', 'Comentário atualizado.');
    }

    public function excluirComentario(int $comentarioId, TarefaComentarioService $service): void
    {
        $comentario = TarefaComentario::query()
            ->where('tarefa_id', $this->tarefa->id)
            ->findOrFail($comentarioId);

        $this->authorize('delete', $comentario);

        $service->delete((int) $this->tarefa->id, $comentarioId);

        if ($this->editandoId === $comentarioId) {
            $this->reset('editandoId', 'editandoTexto');
        }

        session()->flash('status', 'Comentário excluído.');
    }

    public function with(): array
    {
        return [
            'comentarios' => TarefaComentario::query()
                ->with('usuario')
                ->where('tarefa_id', $this->tarefa->id)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get(),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $tarefa->titulo }}</h2>
                <p class="text-sm text-gray-500">{{ $tarefa->projeto?->sigla }} — {{ $tarefa->projeto?->nome }}</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('tarefas.edit', $tarefa) }}" wire:navigate class="text-sm text-gray-600 hover:underline">Editar</a>
                <a href="{{ route('tarefas.index') }}" wire:navigate class="text-sm text-gray-600 hover:underline">Voltar</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex flex-wrap gap-2 text-sm">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-blue-50 text-blue-700">{{ $tarefa->situacao?->nome }}</span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700">{{ $tarefa->prioridade?->nome }}</span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">{{ $tarefa->tipo?->nome }}</span>
                </div>
                @if ($tarefa->descricao)
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $tarefa->descricao }}</p>
                @endif
                <p class="text-xs text-gray-500">Responsável: {{ $tarefa->usuario?->name }}</p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">Comentários</h3>

                <form wire:submit="criarComentario" class="space-y-3">
                    <div>
                        <x-input-label for="novoComentario" value="Novo comentário" />
                        <textarea
                            wire:model="novoComentario"
                            id="novoComentario"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        ></textarea>
                        @error('novoComentario')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-primary-button>Adicionar comentário</x-primary-button>
                </form>

                <div class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($comentarios as $comentario)
                        <div class="py-4 space-y-2" wire:key="comentario-{{ $comentario->id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $comentario->usuario?->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $comentario->created_at?->format('d/m/Y H:i') }}</p>
                                </div>
                                @can('update', $comentario)
                                    <div class="flex items-center gap-2 text-sm shrink-0">
                                        @if ($editandoId !== $comentario->id)
                                            <button type="button" wire:click="iniciarEdicao({{ $comentario->id }})" class="text-indigo-600 hover:underline">Editar</button>
                                            <button type="button" wire:click="excluirComentario({{ $comentario->id }})" wire:confirm="Excluir este comentário?" class="text-red-600 hover:underline">Excluir</button>
                                        @endif
                                    </div>
                                @endcan
                            </div>

                            @if ($editandoId === $comentario->id)
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
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comentario->comentario }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-sm text-gray-500">Nenhum comentário ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
