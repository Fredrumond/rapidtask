<?php

use App\Exceptions\DuvidaDomainException;
use App\Exceptions\DuvidaException;
use App\Services\DuvidaService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component
{
    public int $blocoAtivo = 0;

    public int $depoimentoAtivo = 0;

    public string $nome = '';

    public string $email = '';

    public string $telefone = '';

    public string $duvida = '';

    public bool $enviado = false;

    public function selecionarBloco(int $indice): void
    {
        if ($indice < 0 || $indice >= count($this->blocos())) {
            return;
        }

        $this->blocoAtivo = $indice;
    }

    public function avancarDepoimento(): void
    {
        $total = count($this->depoimentos());

        $this->depoimentoAtivo = ($this->depoimentoAtivo + 1) % $total;
    }

    public function voltarDepoimento(): void
    {
        $total = count($this->depoimentos());

        $this->depoimentoAtivo = ($this->depoimentoAtivo - 1 + $total) % $total;
    }

    public function enviar(DuvidaService $duvidaService): void
    {
        $this->enviado = false;

        $this->nome = trim($this->nome);
        $this->email = trim($this->email);
        $this->telefone = trim($this->telefone);
        $this->duvida = trim($this->duvida);

        $validated = $this->validate([
            'nome' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'telefone' => ['required', 'string'],
            'duvida' => ['required', 'string'],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail é inválido.',
            'telefone.required' => 'O telefone é obrigatório.',
            'duvida.required' => 'A dúvida é obrigatória.',
        ]);

        try {
            $duvidaService->create([
                'nome' => $validated['nome'],
                'email' => $validated['email'],
                'telefone' => $validated['telefone'],
                'duvida' => $validated['duvida'],
            ]);
        } catch (DuvidaDomainException|DuvidaException) {
            $this->addError('formulario', 'Não foi possível registrar a dúvida.');

            return;
        }

        $this->reset('nome', 'email', 'telefone', 'duvida');
        $this->enviado = true;
    }

    /**
     * @return list<array{titulo: string, texto: string}>
     */
    public function blocos(): array
    {
        return [
            [
                'titulo' => 'Autenticação',
                'texto' => 'Acesse o RapidTask com a sua conta.',
            ],
            [
                'titulo' => 'CRUD de tarefas, projetos, clientes e comentários',
                'texto' => 'Gerencie tarefas, projetos, clientes e comentários na interface web e na API.',
            ],
            [
                'titulo' => 'Gerenciamento de tokens',
                'texto' => 'Crie e controle os tokens de acesso da conta.',
            ],
            [
                'titulo' => 'Dashboard',
                'texto' => 'Acompanhe no painel o que precisa de atenção.',
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
}; ?>

<div>
    <section class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <h2 class="text-sm font-medium uppercase tracking-wide text-gray-500">Apresentação</h2>
            <h1 class="mt-3 text-3xl sm:text-4xl font-semibold text-gray-900 leading-tight">
                Acompanhe tarefas, projetos e clientes no mesmo lugar
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-gray-600">
                O RapidTask reúne o trabalho do time na tela e na API, para você ver o andamento sem planilha paralela.
            </p>
            <a
                href="{{ route('register') }}"
                class="inline-flex items-center mt-8 px-5 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
            >
                Criar conta
            </a>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-semibold text-gray-900">Pontos da ferramenta</h2>
        <div class="mt-6 flex flex-wrap gap-2">
            @foreach ($this->blocos() as $indice => $bloco)
                <button
                    type="button"
                    wire:click="selecionarBloco({{ $indice }})"
                    class="px-4 py-2 rounded-md text-sm font-medium {{ $blocoAtivo === $indice ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-100' }}"
                >
                    {{ $bloco['titulo'] }}
                </button>
            @endforeach
        </div>
        <div class="mt-6 bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ $this->blocos()[$blocoAtivo]['titulo'] }}</h3>
            <p class="mt-2 text-gray-600">{{ $this->blocos()[$blocoAtivo]['texto'] }}</p>
        </div>
    </section>

    <section class="bg-white border-y border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h2 class="text-2xl font-semibold text-gray-900">Depoimentos</h2>
            <figure class="mt-6">
                <blockquote class="text-lg text-gray-800">
                    «{{ $this->depoimentos()[$depoimentoAtivo]['texto'] }}»
                </blockquote>
                <figcaption class="mt-4 text-sm font-medium text-gray-600">
                    {{ $this->depoimentos()[$depoimentoAtivo]['autor'] }}
                </figcaption>
            </figure>
            <div class="mt-6 flex gap-2">
                <button
                    type="button"
                    wire:click="voltarDepoimento"
                    class="px-4 py-2 bg-white rounded-md text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-100"
                >
                    Anterior
                </button>
                <button
                    type="button"
                    wire:click="avancarDepoimento"
                    class="px-4 py-2 bg-white rounded-md text-sm font-medium text-gray-700 ring-1 ring-gray-200 hover:bg-gray-100"
                >
                    Próximo
                </button>
            </div>
        </div>
    </section>

    <footer class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-semibold text-gray-900">Dúvidas</h2>
        <p class="mt-2 text-gray-600">Envie nome, e-mail, telefone e a sua dúvida.</p>

        @if ($enviado)
            <p class="mt-6 text-sm font-medium text-green-700">Dúvida enviada. Obrigado pelo contato.</p>
        @endif

        <x-input-error :messages="$errors->get('formulario')" class="mt-4" />

        <form wire:submit="enviar" class="mt-6 max-w-xl space-y-4">
            <div>
                <x-input-label for="nome" value="Nome" />
                <x-text-input wire:model="nome" id="nome" class="block mt-1 w-full" type="text" name="nome" autocomplete="name" />
                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="E-mail" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="telefone" value="Telefone" />
                <x-text-input wire:model="telefone" id="telefone" class="block mt-1 w-full" type="text" name="telefone" autocomplete="tel" />
                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="duvida" value="Dúvida" />
                <textarea
                    wire:model="duvida"
                    id="duvida"
                    name="duvida"
                    rows="4"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                ></textarea>
                <x-input-error :messages="$errors->get('duvida')" class="mt-2" />
            </div>

            <x-primary-button>Enviar dúvida</x-primary-button>
        </form>
    </footer>
</div>
