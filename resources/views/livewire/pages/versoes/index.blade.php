<?php

use App\Support\Versoes;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    #[Url]
    public string $busca = '';

    #[Url]
    public string $estado = '';

    public function filtrarPor(string $estado): void
    {
        $this->estado = $this->estado === $estado ? '' : $estado;
    }

    public function limpar(): void
    {
        $this->busca = '';
        $this->estado = '';
    }

    public function with(): array
    {
        $estados = Versoes::estados();
        $estado = array_key_exists($this->estado, $estados) ? $this->estado : null;

        return [
            'estados' => $estados,
            'contagem' => Versoes::contagemPorEstado(),
            'atual' => Versoes::atual(),
            'totalReleases' => count(Versoes::todas()),
            'totalEntregas' => Versoes::totalEntregas(),
            'releases' => Versoes::filtrar($estado, $this->busca),
            'filtrando' => $estado !== null || trim($this->busca) !== '',
        ];
    }
}; ?>

@php
    $pills = [
        'stable' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
        'development' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-600/20',
        'test' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        'bug' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
    ];
    $dots = [
        'stable' => 'bg-emerald-500',
        'development' => 'bg-sky-500',
        'test' => 'bg-amber-500',
        'bug' => 'bg-rose-500',
    ];
    $textos = [
        'stable' => 'text-emerald-600',
        'development' => 'text-sky-600',
        'test' => 'text-amber-600',
        'bug' => 'text-rose-600',
    ];
@endphp

<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Versões</h2>
                <p class="text-sm text-gray-500">Evolução das features do RapidTask, do primeiro release ao atual.</p>
            </div>
            @if ($atual)
                <span class="inline-flex items-center gap-2 self-start sm:self-auto rounded-full bg-gray-900 px-3 py-1 text-xs font-medium text-white">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    {{ $atual['versao'] }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Versão atual</p>
                    <p class="mt-1 font-mono text-lg text-gray-900">{{ $atual['versao'] ?? '—' }}</p>
                    @if ($atual)
                        <p class="text-xs text-gray-500">{{ $atual['titulo'] }}</p>
                    @endif
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Releases</p>
                    <p class="mt-1 text-lg text-gray-900">{{ $totalReleases }}</p>
                    <p class="text-xs text-gray-500">desde 2019</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Entregas registradas</p>
                    <p class="mt-1 text-lg text-gray-900">{{ $totalEntregas }}</p>
                    <p class="text-xs text-gray-500">features, correções e ajustes</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4 space-y-3">
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="busca"
                        placeholder="Buscar feature, módulo ou versão..."
                        class="w-full lg:w-80 rounded-md border-gray-300 shadow-sm text-sm"
                    />
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($estados as $chave => $label)
                            <button
                                type="button"
                                wire:click="filtrarPor('{{ $chave }}')"
                                @disabled(($contagem[$chave] ?? 0) === 0)
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium transition',
                                    $pills[$chave],
                                    'ring-2 ring-offset-1 ring-gray-900' => $estado === $chave,
                                    'opacity-40 cursor-not-allowed' => ($contagem[$chave] ?? 0) === 0,
                                ])
                            >
                                <span class="h-1.5 w-1.5 rounded-full {{ $dots[$chave] }}"></span>
                                {{ $label }}
                                <span class="opacity-60">{{ $contagem[$chave] ?? 0 }}</span>
                            </button>
                        @endforeach
                        @if ($filtrando)
                            <button type="button" wire:click="limpar" class="text-xs text-gray-500 hover:text-gray-700 hover:underline">
                                Limpar filtros
                            </button>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-gray-500">
                    O estado indica a maturidade de cada entrega, herdado da legenda do changelog original.
                </p>
            </div>

            @if ($releases === [])
                <div class="bg-white shadow-sm sm:rounded-lg p-10 text-center">
                    <p class="text-sm text-gray-600">Nenhuma entrega encontrada com esses filtros.</p>
                    <button type="button" wire:click="limpar" class="mt-2 text-sm text-indigo-600 hover:underline">
                        Ver todas as versões
                    </button>
                </div>
            @else
                <div class="relative ml-2 space-y-6 border-l-2 border-gray-200 pl-8">
                    @foreach ($releases as $indice => $release)
                        <article class="relative">
                            <span @class([
                                'absolute -left-[41px] top-6 h-4 w-4 rounded-full ring-4 ring-gray-100',
                                $dots[$release['estado']],
                            ])></span>

                            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                                <header class="px-5 py-4 border-b border-gray-100">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-mono text-base font-semibold text-gray-900">{{ $release['versao'] }}</h3>
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $pills[$release['estado']] }}">
                                            {{ $estados[$release['estado']] }}
                                        </span>
                                        @if ($indice === 0 && ! $filtrando)
                                            <span class="inline-flex items-center rounded-full bg-gray-900 px-2 py-0.5 text-xs font-medium text-white">Atual</span>
                                        @endif
                                        <span class="ml-auto text-xs text-gray-500">
                                            {{ isset($release['data']) ? Carbon::parse($release['data'])->format('d/m/Y') : ($release['data_label'] ?? 'data não registrada') }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm font-medium text-gray-800">{{ $release['titulo'] }}</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $release['resumo'] }}</p>
                                    @if (! empty($release['nota']))
                                        <p class="mt-2 text-xs text-gray-500 italic">{{ $release['nota'] }}</p>
                                    @endif
                                </header>

                                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach ($release['modulos'] as $modulo)
                                        <section class="rounded-lg border border-gray-200 bg-gray-50/60 p-4">
                                            <div class="flex items-baseline justify-between gap-2">
                                                <h4 class="text-sm font-semibold text-gray-800">{{ $modulo['nome'] }}</h4>
                                                <span class="text-xs text-gray-400">{{ count($modulo['itens']) }}</span>
                                            </div>
                                            <ul class="mt-3 space-y-2">
                                                @foreach ($modulo['itens'] as $item)
                                                    <li class="flex gap-2">
                                                        <span @class([
                                                            'mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full',
                                                            $dots[$item['estado']],
                                                        ])></span>
                                                        <div class="min-w-0">
                                                            <p class="text-sm text-gray-700">
                                                                {{ $item['titulo'] }}
                                                                @if ($item['estado'] !== 'stable')
                                                                    <span class="text-xs font-medium {{ $textos[$item['estado']] }}">
                                                                        · {{ $estados[$item['estado']] }}
                                                                    </span>
                                                                @endif
                                                            </p>
                                                            @if (! empty($item['nota']))
                                                                <p class="text-xs text-gray-500">{{ $item['nota'] }}</p>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </section>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
