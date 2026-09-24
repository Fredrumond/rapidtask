<?php

use App\Models\Duvida;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.landing')] class extends Component
{
    public int $pontoAtivo = 0;

    public int $depoimentoAtivo = 0;

    public string $nome = '';

    public string $email = '';

    public string $telefone = '';

    public string $duvida = '';

    public bool $enviada = false;

    /**
     * @return array<int, array{titulo: string, texto: string}>
     */
    public function pontos(): array
    {
        return [
            [
                'titulo' => 'Autenticação',
                'texto' => 'Entre com a sua conta para acessar o RapidTask. O cadastro cria a conta e leva você ao painel.',
            ],
            [
                'titulo' => 'Tarefas, projetos, clientes e comentários',
                'texto' => 'Crie, consulte, atualize e exclua tarefas, projetos, clientes e comentários na interface web e na API.',
            ],
            [
                'titulo' => 'Tokens',
                'texto' => 'Gere e revogue tokens de API da conta para integrar o RapidTask ao que o time já usa.',
            ],
            [
                'titulo' => 'Dashboard',
                'texto' => 'O painel mostra clientes, projetos e tarefas que precisam de atenção antes da reunião começar.',
            ],
        ];
    }

    /**
     * @return array<int, array{texto: string, autor: string}>
     */
    public function depoimentos(): array
    {
        return [
            [
                'texto' => 'Passei a enxergar o andamento dos projetos sem cobrar atualização o tempo todo.',
                'autor' => 'Marina Costa',
            ],
            [
                'texto' => 'Tarefas, comentários e clientes no mesmo lugar tiraram a bagunça da nossa rotina.',
                'autor' => 'Paulo Henrique',
            ],
            [
                'texto' => 'O painel me mostra o que precisa de atenção antes da reunião começar.',
                'autor' => 'Aline Ferreira',
            ],
            [
                'texto' => 'Conseguimos acompanhar o trabalho pela tela e pela API, sem planilha paralela.',
                'autor' => 'Ricardo Nunes',
            ],
        ];
    }

    public function selecionarPonto(int $indice): void
    {
        if ($indice < 0 || $indice >= count($this->pontos())) {
            return;
        }

        $this->pontoAtivo = $indice;
    }

    public function depoimentoAnterior(): void
    {
        $total = count($this->depoimentos());
        $this->depoimentoAtivo = ($this->depoimentoAtivo - 1 + $total) % $total;
    }

    public function proximoDepoimento(): void
    {
        $total = count($this->depoimentos());
        $this->depoimentoAtivo = ($this->depoimentoAtivo + 1) % $total;
    }

    public function selecionarDepoimento(int $indice): void
    {
        if ($indice < 0 || $indice >= count($this->depoimentos())) {
            return;
        }

        $this->depoimentoAtivo = $indice;
    }

    public function enviar(): void
    {
        $this->enviada = false;

        $chave = 'duvida-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            $this->addError('duvida', 'Muitas tentativas. Tente novamente em instantes.');

            return;
        }

        $this->nome = trim($this->nome);
        $this->email = trim($this->email);
        $this->telefone = trim($this->telefone);
        $this->duvida = trim($this->duvida);

        $dados = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telefone' => ['required', 'string', 'max:30'],
            'duvida' => ['required', 'string', 'max:2000'],
        ], [
            'nome.required' => 'Informe o nome.',
            'nome.max' => 'O nome deve ter no máximo 255 caracteres.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'telefone.required' => 'Informe o telefone.',
            'telefone.max' => 'O telefone deve ter no máximo 30 caracteres.',
            'duvida.required' => 'Escreva a dúvida.',
            'duvida.max' => 'A dúvida deve ter no máximo 2000 caracteres.',
        ]);

        Duvida::query()->create($dados);

        RateLimiter::hit($chave, 60);

        $this->reset('nome', 'email', 'telefone', 'duvida');
        $this->enviada = true;
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $pontos = $this->pontos();
        $depoimentos = $this->depoimentos();

        return [
            'pontos' => $pontos,
            'depoimentos' => $depoimentos,
            'pontoAtual' => $pontos[$this->pontoAtivo],
            'depoimentoAtual' => $depoimentos[$this->depoimentoAtivo],
        ];
    }
}; ?>

<div class="bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-semibold text-slate-900" wire:navigate>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-sm text-white">R</span>
                RapidTask
            </a>

            <nav class="flex flex-wrap items-center gap-1 text-sm" aria-label="Seções">
                <a href="#apresentacao" class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Apresentação</a>
                <a href="#pontos" class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Pontos</a>
                <a href="#depoimentos" class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Depoimentos</a>
                <a href="#duvidas" class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Dúvidas</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" wire:navigate>
                        Painel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" wire:navigate>
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500" wire:navigate>
                        Experimentar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <section id="apresentacao" class="scroll-mt-24 bg-indigo-700 text-white">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 sm:py-28">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-200">RapidTask</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight sm:text-5xl">
                    O trabalho do time, em um só lugar.
                </h1>
                <p class="mt-6 max-w-2xl text-lg text-indigo-100">
                    Tarefas, projetos, clientes e comentários na interface e na API. O painel mostra o que precisa de atenção antes da reunião.
                </p>
                <div class="mt-8">
                    @guest
                        <a id="call-to-action" href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-white px-5 py-3 text-sm font-semibold text-indigo-700 hover:bg-indigo-50" wire:navigate>
                            Experimentar
                        </a>
                        <p class="mt-3 text-sm text-indigo-200">Crie a sua conta e entre no painel.</p>
                    @else
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md bg-white px-5 py-3 text-sm font-semibold text-indigo-700 hover:bg-indigo-50" wire:navigate>
                            Ir para o painel
                        </a>
                    @endguest
                </div>
            </div>
        </section>

        <section id="pontos" class="scroll-mt-24 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                <h2 class="text-3xl font-semibold tracking-tight">O que o RapidTask oferece</h2>
                <p class="mt-3 max-w-2xl text-slate-600">Mude de um ponto para o outro para ver o que a ferramenta já faz.</p>

                <div class="mt-10 grid gap-6 lg:grid-cols-12">
                    <div class="flex flex-col gap-2 lg:col-span-5" role="tablist" aria-label="Pontos da ferramenta">
                        @foreach ($pontos as $indice => $ponto)
                            <button
                                type="button"
                                role="tab"
                                wire:click="selecionarPonto({{ $indice }})"
                                aria-selected="{{ $indice === $pontoAtivo ? 'true' : 'false' }}"
                                class="rounded-lg border px-4 py-3 text-left transition {{ $indice === $pontoAtivo ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 bg-white text-slate-800 hover:border-indigo-300' }}"
                            >
                                <span class="block text-xs font-semibold uppercase tracking-wide {{ $indice === $pontoAtivo ? 'text-indigo-200' : 'text-slate-400' }}">0{{ $indice + 1 }}</span>
                                <span class="mt-1 block text-sm font-semibold">{{ $ponto['titulo'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-8 lg:col-span-7" role="tabpanel">
                        <h3 class="text-2xl font-semibold">{{ $pontoAtual['titulo'] }}</h3>
                        <p class="mt-4 text-lg leading-relaxed text-slate-700">{{ $pontoAtual['texto'] }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="depoimentos" class="scroll-mt-24">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                <h2 class="text-3xl font-semibold tracking-tight">Quem já usa</h2>
                <p class="mt-3 text-slate-600">Percorra os depoimentos.</p>

                <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm sm:p-12">
                    <blockquote>
                        <p class="text-2xl font-medium leading-relaxed text-slate-900">«{{ $depoimentoAtual['texto'] }}»</p>
                        <footer class="mt-6 text-base font-semibold text-indigo-700">{{ $depoimentoAtual['autor'] }}</footer>
                    </blockquote>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <button type="button" wire:click="depoimentoAnterior" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Anterior
                        </button>
                        <button type="button" wire:click="proximoDepoimento" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Próximo
                        </button>
                        <span class="text-sm text-slate-500">{{ $depoimentoAtivo + 1 }} de {{ count($depoimentos) }}</span>

                        <div class="ml-auto flex gap-2" role="group" aria-label="Escolher depoimento">
                            @foreach ($depoimentos as $indice => $depoimento)
                                <button
                                    type="button"
                                    wire:click="selecionarDepoimento({{ $indice }})"
                                    aria-label="Depoimento {{ $indice + 1 }}"
                                    class="h-2.5 w-2.5 rounded-full {{ $indice === $depoimentoAtivo ? 'bg-indigo-600' : 'bg-slate-300' }}"
                                ></button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer id="duvidas" class="scroll-mt-24 bg-slate-900 text-slate-300">
        <div class="mx-auto max-w-3xl px-4 py-14 sm:px-6">
            <h2 class="text-2xl font-semibold text-white">Dúvidas</h2>
            <p class="mt-2 text-sm">Envie nome, e-mail, telefone e a sua dúvida.</p>

            @if ($enviada)
                <p class="mt-6 rounded-md bg-emerald-500/15 px-4 py-3 text-sm text-emerald-200" role="status">
                    Recebemos a sua dúvida.
                </p>
            @endif

            <form wire:submit="enviar" class="mt-6 space-y-4">
                <div>
                    <x-input-label for="nome" value="Nome" class="!text-slate-200" />
                    <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" type="text" name="nome" required autocomplete="name" />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="E-mail" class="!text-slate-200" />
                    <x-text-input wire:model="email" id="email" class="mt-1 block w-full" type="email" name="email" required autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="telefone" value="Telefone" class="!text-slate-200" />
                    <x-text-input wire:model="telefone" id="telefone" class="mt-1 block w-full" type="tel" name="telefone" required autocomplete="tel" />
                    <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="duvida" value="Dúvida" class="!text-slate-200" />
                    <textarea wire:model="duvida" id="duvida" name="duvida" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error :messages="$errors->get('duvida')" class="mt-2" />
                </div>

                <x-primary-button>
                    Enviar
                </x-primary-button>
            </form>

            <p class="mt-10 text-xs text-slate-500">RapidTask</p>
        </div>
    </footer>
</div>
