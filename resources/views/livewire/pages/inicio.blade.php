<?php

use App\Exceptions\DuvidaDomainException;
use App\Exceptions\DuvidaException;
use App\Services\DuvidaService;
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
     * @return list<array{titulo: string, texto: string}>
     */
    public function pontos(): array
    {
        return [
            [
                'titulo' => 'Autenticação',
                'texto' => 'A entrada na aplicação é por conta. Quem cria a conta segue para o painel e passa a trabalhar com o time.',
            ],
            [
                'titulo' => 'Tarefas, projetos, clientes e comentários',
                'texto' => 'Tarefas, projetos, clientes e comentários podem ser criados, consultados, alterados e removidos na interface web e na API.',
            ],
            [
                'titulo' => 'Gerenciamento de tokens',
                'texto' => 'A conta tem um token de API. Gerar um novo revoga o anterior, e o texto do token aparece só no momento da geração.',
            ],
            [
                'titulo' => 'Dashboard',
                'texto' => 'O painel mostra clientes, projetos, tarefas abertas e tarefas arquivadas do time ativo, para ver o que precisa de atenção.',
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

    public function enviar(DuvidaService $service): void
    {
        $data = $this->validate([
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

        try {
            $service->registrar($data);
        } catch (DuvidaDomainException|DuvidaException $exception) {
            $this->addError('duvida', $exception->getMessage());

            return;
        }

        $this->reset('nome', 'email', 'telefone', 'duvida');
        $this->enviada = true;
    }
}; ?>

<div>
    <header class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-6 gap-y-3 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-gray-900">RapidTask</a>

            <nav aria-label="Seções da página" class="order-last flex w-full flex-wrap gap-x-5 gap-y-2 text-sm text-gray-600 sm:order-none sm:w-auto">
                <a href="#apresentacao" class="hover:text-gray-900">Apresentação</a>
                <a href="#pontos" class="hover:text-gray-900">O que oferece</a>
                <a href="#depoimentos" class="hover:text-gray-900">Depoimentos</a>
                <a href="#duvidas" class="hover:text-gray-900">Dúvidas</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Painel
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-gray-900">Entrar</a>
                    <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Criar conta
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <section id="apresentacao" class="border-b border-gray-100 bg-gray-50">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 sm:py-28">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">RapidTask</p>
                <h1 class="mt-3 max-w-3xl text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">
                    Acompanhe projetos, tarefas e clientes no mesmo lugar.
                </h1>
                <p class="mt-6 max-w-2xl text-lg text-gray-600">
                    O RapidTask reúne o andamento do trabalho na tela e na API, para a equipe ver o que precisa de atenção sem planilha paralela.
                </p>
                <div class="mt-10">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-3 text-base font-semibold text-white hover:bg-indigo-500">
                            Ir para o painel
                        </a>
                    @else
                        <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-3 text-base font-semibold text-white hover:bg-indigo-500">
                            Criar conta
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <section id="pontos" class="border-b border-gray-100" x-data="{ ativo: 0 }">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <h2 class="text-3xl font-semibold tracking-tight text-gray-900">O que o RapidTask oferece</h2>
                <p class="mt-3 max-w-2xl text-gray-600">Escolha um ponto para ver o que já está no produto.</p>

                <div class="mt-8 flex flex-wrap gap-2" role="tablist" aria-label="Pontos da ferramenta">
                    @foreach ($this->pontos() as $indice => $ponto)
                        <button
                            type="button"
                            role="tab"
                            id="ponto-tab-{{ $indice }}"
                            :aria-selected="ativo === {{ $indice }}"
                            :class="ativo === {{ $indice }} ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            class="rounded-full border border-gray-300 px-4 py-2 text-sm font-medium"
                            @click="ativo = {{ $indice }}"
                        >
                            {{ $ponto['titulo'] }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-8">
                    @foreach ($this->pontos() as $indice => $ponto)
                        <article
                            role="tabpanel"
                            id="ponto-painel-{{ $indice }}"
                            aria-labelledby="ponto-tab-{{ $indice }}"
                            x-show="ativo === {{ $indice }}"
                            @if ($indice !== 0) x-cloak @endif
                            class="rounded-2xl border border-gray-200 bg-gray-50 p-8"
                        >
                            <h3 class="text-xl font-semibold text-gray-900">{{ $ponto['titulo'] }}</h3>
                            <p class="mt-3 max-w-3xl text-gray-700">{{ $ponto['texto'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="depoimentos" class="border-b border-gray-100 bg-gray-50" x-data="{ indice: 0, total: {{ count($this->depoimentos()) }} }">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <h2 class="text-3xl font-semibold tracking-tight text-gray-900">Depoimentos</h2>
                <p class="mt-3 text-gray-600">Percorra os quatro relatos.</p>

                <div class="relative mt-8" aria-roledescription="carrossel" aria-label="Depoimentos">
                    @foreach ($this->depoimentos() as $ordem => $depoimento)
                        <figure
                            x-show="indice === {{ $ordem }}"
                            @if ($ordem !== 0) x-cloak @endif
                            class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        >
                            <blockquote class="text-xl text-gray-900">«{{ $depoimento['texto'] }}»</blockquote>
                            <figcaption class="mt-6 text-sm font-semibold text-gray-700">{{ $depoimento['autor'] }}</figcaption>
                        </figure>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        @click="indice = indice === 0 ? total - 1 : indice - 1"
                    >
                        Anterior
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        @click="indice = indice === total - 1 ? 0 : indice + 1"
                    >
                        Próximo
                    </button>

                    <div class="flex gap-2" role="group" aria-label="Escolher depoimento">
                        @foreach ($this->depoimentos() as $ordem => $depoimento)
                            <button
                                type="button"
                                class="h-2.5 w-2.5 rounded-full"
                                :class="indice === {{ $ordem }} ? 'bg-indigo-600' : 'bg-gray-300'"
                                aria-label="Depoimento de {{ $depoimento['autor'] }}"
                                @click="indice = {{ $ordem }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer id="duvidas" class="bg-gray-900 text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <h2 class="text-2xl font-semibold">Dúvidas</h2>
            <p class="mt-2 max-w-xl text-sm text-gray-300">
                Envie nome, e-mail, telefone e a dúvida. Esses dados ficam registrados para o retorno do contato.
            </p>

            @if ($enviada)
                <p class="mt-8 rounded-md bg-white/10 px-4 py-3 text-sm" role="status">Dúvida registrada. Vamos usar esses dados para retornar o contato.</p>
            @endif

            <form wire:submit="enviar" class="mt-8 grid max-w-xl gap-4">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-200">Nome</label>
                    <input wire:model="nome" id="nome" type="text" name="nome" autocomplete="name" required class="mt-1 block w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-200">E-mail</label>
                    <input wire:model="email" id="email" type="email" name="email" autocomplete="email" required class="mt-1 block w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-200">Telefone</label>
                    <input wire:model="telefone" id="telefone" type="tel" name="telefone" autocomplete="tel" required class="mt-1 block w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                </div>
                <div>
                    <label for="duvida" class="block text-sm font-medium text-gray-200">Dúvida</label>
                    <textarea wire:model="duvida" id="duvida" name="duvida" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error :messages="$errors->get('duvida')" class="mt-2" />
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-100">
                        Enviar dúvida
                    </button>
                </div>
            </form>

            <p class="mt-12 text-sm text-gray-400">RapidTask</p>
        </div>
    </footer>
</div>
