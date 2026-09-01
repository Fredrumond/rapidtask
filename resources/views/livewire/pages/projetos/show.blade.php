<?php

use App\Exceptions\ProjetoArquivoDomainException;
use App\Models\Projeto;
use App\Models\ProjetoArquivo;
use App\Models\Situacao;
use App\Services\ProjetoArquivoService;
use Illuminate\Validation\Rules\File;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] class extends Component
{
    use WithFileUploads;

    public Projeto $projeto;

    public string $arquivoNome = '';

    public string $arquivoDescricao = '';

    public $arquivo = null;

    public function mount(Projeto $projeto): void
    {
        $this->authorize('view', $projeto);
        $this->projeto = $projeto->load(['cliente', 'tarefas.prioridade', 'tarefas.situacao']);
    }

    public function enviarArquivo(ProjetoArquivoService $service): void
    {
        $this->authorize('create', ProjetoArquivo::class);

        $data = $this->validate([
            'arquivoNome' => ['required', 'string', 'max:255'],
            'arquivoDescricao' => ['required', 'string'],
            'arquivo' => [
                'required',
                File::types(['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx', 'xls', 'xlsx', 'txt'])
                    ->max(10 * 1024),
            ],
        ]);

        try {
            $service->create((int) auth()->id(), (int) $this->projeto->id, [
                'nome' => $data['arquivoNome'],
                'descricao' => $data['arquivoDescricao'],
                'arquivo' => $data['arquivo'],
            ]);
        } catch (ProjetoArquivoDomainException $exception) {
            $this->addError('arquivoNome', $exception->getMessage());

            return;
        }

        $this->reset('arquivoNome', 'arquivoDescricao', 'arquivo');
        session()->flash('status', 'Arquivo enviado.');
    }

    public function excluirArquivo(int $arquivoId, ProjetoArquivoService $service): void
    {
        $arquivo = ProjetoArquivo::query()
            ->where('projeto_id', $this->projeto->id)
            ->findOrFail($arquivoId);

        $this->authorize('delete', $arquivo);

        $service->delete((int) $this->projeto->id, $arquivoId);

        session()->flash('status', 'Arquivo excluído.');
    }

    public function with(): array
    {
        $situacoes = Situacao::query()->orderBy('id')->get();
        $tarefasPorSituacao = $this->projeto->tarefas
            ->where('status', 0)
            ->groupBy('situacao_id');
        $arquivos = app(ProjetoArquivoService::class)->list((int) $this->projeto->id);

        return compact('situacoes', 'tarefasPorSituacao', 'arquivos');
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
                <h3 class="font-medium text-gray-900">Arquivos</h3>

                <form wire:submit="enviarArquivo" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="arquivoNome" value="Nome" />
                            <x-text-input wire:model="arquivoNome" id="arquivoNome" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('arquivoNome')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="arquivo" value="Arquivo" />
                            <input
                                type="file"
                                wire:model="arquivo"
                                id="arquivo"
                                class="mt-1 block w-full text-sm text-gray-700"
                            />
                            <x-input-error :messages="$errors->get('arquivo')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="arquivoDescricao" value="Descrição" />
                        <textarea
                            wire:model="arquivoDescricao"
                            id="arquivoDescricao"
                            rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        ></textarea>
                        <x-input-error :messages="$errors->get('arquivoDescricao')" class="mt-2" />
                    </div>
                    <x-primary-button>Enviar arquivo</x-primary-button>
                </form>

                <div class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($arquivos as $arquivoItem)
                        <div class="py-4 space-y-1" wire:key="arquivo-{{ $arquivoItem->id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $arquivoItem->nome }}</p>
                                    <p class="text-xs text-gray-500">{{ $arquivoItem->usuario?->name }} · {{ $arquivoItem->created_at?->format('d/m/Y H:i') }}</p>
                                    <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $arquivoItem->descricao }}</p>
                                </div>
                                <div class="flex items-center gap-2 text-sm shrink-0">
                                    <a href="{{ route('arquivos.download', $arquivoItem) }}" class="text-indigo-600 hover:underline">Baixar</a>
                                    @can('delete', $arquivoItem)
                                        <button type="button" wire:click="excluirArquivo({{ $arquivoItem->id }})" wire:confirm="Excluir este arquivo?" class="text-red-600 hover:underline">Excluir</button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-sm text-gray-500">Nenhum arquivo ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
