<?php

use App\Exceptions\DuvidaDomainException;
use App\Exceptions\DuvidaException;
use App\Services\DuvidaService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component
{
    public string $nome = '';

    public string $email = '';

    public string $telefone = '';

    public string $mensagem = '';

    public bool $enviada = false;

    public function enviar(DuvidaService $service): void
    {
        $data = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefone' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string', 'max:5000'],
        ], [
            'nome.required' => 'Informe o nome.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'telefone.required' => 'Informe o telefone.',
            'mensagem.required' => 'Escreva a dúvida.',
        ]);

        try {
            $service->registrar($data);
        } catch (DuvidaDomainException|DuvidaException $exception) {
            $this->addError('mensagem', $exception->getMessage());

            return;
        }

        $this->reset('nome', 'email', 'telefone', 'mensagem');
        $this->enviada = true;
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

    /**
     * @return list<array{titulo: string, texto: string}>
     */
    public function pontos(): array
    {
        return [
            [
                'titulo' => 'Autenticação',
                'texto' => 'Crie uma conta e entre no painel com o seu usuário.',
            ],
            [
                'titulo' => 'Tarefas, projetos, clientes e comentários',
                'texto' => 'Cadastre e acompanhe tarefas, projetos, clientes e comentários na interface web e na API.',
            ],
            [
                'titulo' => 'Gerenciamento de tokens',
                'texto' => 'Gere e administre os tokens de acesso à API da sua conta.',
            ],
            [
                'titulo' => 'Dashboard',
                'texto' => 'Veja no painel o que precisa de atenção.',
            ],
        ];
    }
}; ?>

<div>
    <header class="border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-lg font-semibold text-gray-900">RapidTask</a>
            <nav class="flex flex-wrap items-center gap-4 text-sm text-gray-600" aria-label="Seções">
                <a href="#apresentacao" class="hover:text-gray-900">Apresentação</a>
                <a href="#pontos" class="hover:text-gray-900">Pontos</a>
                <a href="#depoimentos" class="hover:text-gray-900">Depoimentos</a>
                <a href="#duvidas" class="hover:text-gray-900">Dúvidas</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="font-medium text-gray-900">Painel</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gray-900">Entrar</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-2 bg-gray-800 rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-gray-700">Criar conta</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section id="apresentacao" class="bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 sm:py-24">
                <p class="text-sm font-medium text-indigo-600">RapidTask</p>
                <h1 class="mt-3 text-3xl sm:text-5xl font-semibold text-gray-900 tracking-tight max-w-3xl">
                    Acompanhe projetos, tarefas e clientes no mesmo lugar.
                </h1>
                <p class="mt-6 text-lg text-gray-600 max-w-2xl">
                    O RapidTask reúne o andamento do trabalho na tela e na API, para você ver o que precisa de atenção sem planilha paralela.
                </p>
                @guest
                    <a href="{{ route('register') }}" class="mt-8 inline-flex items-center px-5 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700">
                        Criar conta
                    </a>
                @endguest
            </div>
        </section>

        <section id="pontos" class="max-w-6xl mx-auto px-4 sm:px-6 py-16" x-data="{ atual: 0 }">
            <h2 class="text-2xl font-semibold text-gray-900">O que o RapidTask oferece</h2>
            <div class="mt-8 flex flex-col lg:flex-row gap-8" role="tablist" aria-label="Pontos da ferramenta">
                <div class="flex lg:flex-col gap-2 overflow-x-auto">
                    @foreach ($this->pontos() as $indice => $ponto)
                        <button
                            type="button"
                            role="tab"
                            class="text-left px-4 py-3 rounded-md text-sm font-medium whitespace-nowrap"
                            :class="atual === {{ $indice }} ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700'"
                            :aria-selected="atual === {{ $indice }}"
                            @click="atual = {{ $indice }}"
                        >
                            {{ $ponto['titulo'] }}
                        </button>
                    @endforeach
                </div>
                <div class="flex-1">
                    @foreach ($this->pontos() as $indice => $ponto)
                        <article x-show="atual === {{ $indice }}" @if ($indice !== 0) x-cloak @endif class="rounded-lg border border-gray-200 p-6">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $ponto['titulo'] }}</h3>
                            <p class="mt-3 text-gray-600">{{ $ponto['texto'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="depoimentos" class="bg-gray-50" x-data="{ atual: 0, total: {{ count($this->depoimentos()) }} }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
                <h2 class="text-2xl font-semibold text-gray-900">Depoimentos</h2>
                <div class="mt-8">
                    @foreach ($this->depoimentos() as $indice => $depoimento)
                        <blockquote x-show="atual === {{ $indice }}" @if ($indice !== 0) x-cloak @endif class="max-w-3xl">
                            <p class="text-xl text-gray-900">«{{ $depoimento['texto'] }}»</p>
                            <footer class="mt-4 text-sm font-medium text-gray-600">{{ $depoimento['autor'] }}</footer>
                        </blockquote>
                    @endforeach
                </div>
                <div class="mt-8 flex items-center gap-3">
                    <button type="button" class="px-4 py-2 text-sm font-medium rounded-md bg-white border border-gray-300" @click="atual = (atual - 1 + total) % total">Anterior</button>
                    <button type="button" class="px-4 py-2 text-sm font-medium rounded-md bg-white border border-gray-300" @click="atual = (atual + 1) % total">Próximo</button>
                </div>
            </div>
        </section>
    </main>

    <footer id="duvidas" class="border-t border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl font-semibold text-gray-900">Dúvidas</h2>
            <p class="mt-2 text-gray-600">Envie seu nome, e-mail, telefone e a dúvida.</p>

            @if ($enviada)
                <p class="mt-6 text-sm font-medium text-green-700" role="status">Recebemos sua dúvida.</p>
            @endif

            <form wire:submit="enviar" class="mt-8 max-w-xl space-y-4">
                <div>
                    <x-input-label for="nome" value="Nome" />
                    <x-text-input wire:model="nome" id="nome" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="E-mail" />
                    <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="telefone" value="Telefone" />
                    <x-text-input wire:model="telefone" id="telefone" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="mensagem" value="Dúvida" />
                    <textarea wire:model="mensagem" id="mensagem" rows="4" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('mensagem')" class="mt-2" />
                </div>
                <x-primary-button>Enviar</x-primary-button>
            </form>
        </div>
    </footer>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
