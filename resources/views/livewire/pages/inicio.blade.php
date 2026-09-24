<?php

use App\Models\Duvida;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.publico')] class extends Component
{
    public string $nome = '';

    public string $email = '';

    public string $telefone = '';

    public string $duvida = '';

    public bool $enviada = false;

    /**
     * @return list<array{id: string, aba: string, titulo: string, texto: string}>
     */
    public function pontos(): array
    {
        return [
            [
                'id' => 'autenticacao',
                'aba' => 'Autenticação',
                'titulo' => 'Autenticação',
                'texto' => 'Cadastro e login da conta. Quem cria a conta entra no painel.',
            ],
            [
                'id' => 'crud',
                'aba' => 'CRUD na web e na API',
                'titulo' => 'CRUD de tarefas, projetos, clientes e comentários',
                'texto' => 'Na interface web e na API, o time registra e acompanha tarefas, projetos, clientes e comentários.',
            ],
            [
                'id' => 'tokens',
                'aba' => 'Gerenciamento de tokens',
                'titulo' => 'Gerenciamento de tokens',
                'texto' => 'O dono da conta gera e revoga o token usado para chamar a API.',
            ],
            [
                'id' => 'dashboard',
                'aba' => 'Dashboard',
                'titulo' => 'Dashboard',
                'texto' => 'O painel mostra clientes, projetos e tarefas abertas e arquivadas do time ativo.',
            ],
        ];
    }

    /**
     * @return list<array{texto: string, autor: string}>
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

    public function enviar(): void
    {
        $this->enviada = false;
        $this->email = strtolower($this->email);

        $dados = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telefone' => ['required', 'string', 'max:30'],
            'duvida' => ['required', 'string', 'max:5000'],
        ], [
            'nome.required' => 'Informe o nome.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'telefone.required' => 'Informe o telefone.',
            'duvida.required' => 'Escreva a dúvida.',
        ]);

        $chave = 'duvida-envio:'.request()->ip();

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            $this->addError('duvida', 'Muitas dúvidas enviadas em sequência. Tente novamente em instantes.');

            return;
        }

        Duvida::query()->create($dados);

        RateLimiter::hit($chave, 60);

        $this->reset('nome', 'email', 'telefone', 'duvida');
        $this->enviada = true;
    }
}; ?>

<div class="min-h-screen bg-gray-100">
    <header class="sticky top-0 z-10 border-b border-gray-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <a href="#apresentacao" class="flex items-center gap-2 font-semibold text-gray-800">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    <span>RapidTask</span>
                </a>

                <nav class="hidden items-center gap-6 text-sm text-gray-600 md:flex" aria-label="Seções da página">
                    <a href="#apresentacao" class="hover:text-gray-900">Apresentação</a>
                    <a href="#pontos" class="hover:text-gray-900">Pontos</a>
                    <a href="#depoimentos" class="hover:text-gray-900">Depoimentos</a>
                    <a href="#duvidas" class="hover:text-gray-900">Dúvidas</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                            Painel
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="rounded-md px-3 py-2 text-sm text-gray-600 hover:text-gray-900">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                            Criar conta
                        </a>
                    @endauth
                </div>
            </div>

            <nav class="flex gap-4 overflow-x-auto pb-3 text-sm text-gray-600 md:hidden" aria-label="Seções da página">
                <a href="#apresentacao" class="shrink-0 hover:text-gray-900">Apresentação</a>
                <a href="#pontos" class="shrink-0 hover:text-gray-900">Pontos</a>
                <a href="#depoimentos" class="shrink-0 hover:text-gray-900">Depoimentos</a>
                <a href="#duvidas" class="shrink-0 hover:text-gray-900">Dúvidas</a>
            </nav>
        </div>
    </header>

    <main>
        <section id="apresentacao" class="scroll-mt-24">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                <p class="text-sm font-medium uppercase tracking-widest text-gray-500">RapidTask</p>
                <h1 class="mt-3 max-w-3xl text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl">
                    Projetos, tarefas e clientes no mesmo lugar.
                </h1>
                <p class="mt-4 max-w-2xl text-lg text-gray-600">
                    Acompanhe o trabalho do time pela tela e pela API, e veja no painel o que precisa de atenção.
                </p>
                <div class="mt-8">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center rounded-md bg-gray-800 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-700">
                            Ir para o painel
                        </a>
                    @else
                        <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center rounded-md bg-gray-800 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-700">
                            Criar conta
                        </a>
                        <p class="mt-3 text-sm text-gray-500">Depois do cadastro, você entra no painel.</p>
                    @endauth
                </div>
            </div>
        </section>

        <section id="pontos" class="scroll-mt-24 border-t border-gray-200 bg-white" x-data="{ ativo: 'autenticacao' }">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-gray-900">O que a ferramenta oferece</h2>
                <p class="mt-2 max-w-2xl text-gray-600">Escolha um ponto para ver o que o RapidTask já faz.</p>

                <div role="tablist" aria-label="Pontos da ferramenta" class="mt-8 flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                    @foreach ($this->pontos() as $ponto)
                        <button
                            type="button"
                            role="tab"
                            id="ponto-tab-{{ $ponto['id'] }}"
                            aria-controls="ponto-painel-{{ $ponto['id'] }}"
                            :aria-selected="ativo === '{{ $ponto['id'] }}'"
                            @click="ativo = '{{ $ponto['id'] }}'"
                            :class="ativo === '{{ $ponto['id'] }}' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="rounded-md px-4 py-2 text-left text-sm font-medium"
                        >
                            {{ $ponto['aba'] }}
                        </button>
                    @endforeach
                </div>

                @foreach ($this->pontos() as $indice => $ponto)
                    <div
                        role="tabpanel"
                        id="ponto-painel-{{ $ponto['id'] }}"
                        aria-labelledby="ponto-tab-{{ $ponto['id'] }}"
                        x-show="ativo === '{{ $ponto['id'] }}'"
                        @if ($indice !== 0) x-cloak @endif
                        class="mt-6 max-w-3xl rounded-lg border border-gray-200 bg-gray-50 p-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-900">{{ $ponto['titulo'] }}</h3>
                        <p class="mt-2 text-gray-600">{{ $ponto['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="depoimentos" class="scroll-mt-24" x-data="{ atual: 0, total: {{ count($this->depoimentos()) }} }">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-gray-900">Depoimentos</h2>

                <div class="relative mt-8 max-w-3xl" aria-live="polite">
                    @foreach ($this->depoimentos() as $indice => $depoimento)
                        <blockquote
                            x-show="atual === {{ $indice }}"
                            @if ($indice !== 0) x-cloak @endif
                            class="rounded-lg bg-white p-6 shadow-sm"
                        >
                            <p class="text-lg text-gray-800">{{ $depoimento['texto'] }}</p>
                            <footer class="mt-4 text-sm font-medium text-gray-500">{{ $depoimento['autor'] }}</footer>
                        </blockquote>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        @click="atual = (atual - 1 + total) % total"
                    >
                        Anterior
                    </button>
                    <button
                        type="button"
                        class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        @click="atual = (atual + 1) % total"
                    >
                        Próximo
                    </button>

                    <div class="flex gap-2" role="group" aria-label="Escolher depoimento">
                        @foreach ($this->depoimentos() as $indice => $depoimento)
                            <button
                                type="button"
                                class="h-2.5 w-2.5 rounded-full bg-gray-300"
                                :class="atual === {{ $indice }} ? 'bg-gray-800' : 'bg-gray-300'"
                                :aria-current="atual === {{ $indice }} ? 'true' : 'false'"
                                aria-label="Depoimento de {{ $depoimento['autor'] }}"
                                @click="atual = {{ $indice }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer id="duvidas" class="scroll-mt-24 border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="max-w-xl">
                <h2 class="text-2xl font-semibold text-gray-900">Dúvidas</h2>
                <p class="mt-2 text-gray-600">Envie nome, e-mail, telefone e a sua dúvida. O registro fica no RapidTask.</p>

                @if ($enviada)
                    <p class="mt-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                        Dúvida registrada. Vamos usar esses dados para responder.
                    </p>
                @endif

                <form wire:submit="enviar" class="mt-6 space-y-4">
                    <div>
                        <x-input-label for="nome" value="Nome" />
                        <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" autocomplete="name" required />
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" autocomplete="email" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="telefone" value="Telefone" />
                        <x-text-input wire:model="telefone" id="telefone" type="tel" class="mt-1 block w-full" autocomplete="tel" required />
                        <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="duvida" value="Dúvida" />
                        <textarea
                            wire:model="duvida"
                            id="duvida"
                            rows="4"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>
                        <x-input-error :messages="$errors->get('duvida')" class="mt-2" />
                    </div>
                    <p class="text-sm text-gray-500">Nome, e-mail e telefone ficam gravados para responder a esta dúvida.</p>
                    <x-primary-button>Enviar dúvida</x-primary-button>
                </form>
            </div>
        </div>
    </footer>
</div>
