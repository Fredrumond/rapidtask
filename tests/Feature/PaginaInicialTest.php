<?php

use App\Models\Duvida;
use App\Models\User;
use Livewire\Volt\Volt;

test('pagina inicial apresenta o produto e leva ao cadastro', function (): void {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Acompanhe projetos, tarefas e clientes no mesmo lugar.');
    $response->assertSee('href="'.route('register').'"', false);
    $response->assertSee('Autenticação');
    $response->assertSee('Tarefas, projetos, clientes e comentários');
    $response->assertSee('Gerenciamento de tokens');
    $response->assertSee('Dashboard');
    $response->assertSee('Passei a enxergar o andamento dos projetos sem cobrar atualização o tempo todo.');
    $response->assertSee('Marina Costa');
    $response->assertSee('Tarefas, comentários e clientes no mesmo lugar tiraram a bagunça da nossa rotina.');
    $response->assertSee('Paulo Henrique');
    $response->assertSee('O painel me mostra o que precisa de atenção antes da reunião começar.');
    $response->assertSee('Aline Ferreira');
    $response->assertSee('Conseguimos acompanhar o trabalho pela tela e pela API, sem planilha paralela.');
    $response->assertSee('Ricardo Nunes');
    $response->assertSee('id="apresentacao"', false);
    $response->assertSee('id="pontos"', false);
    $response->assertSee('id="depoimentos"', false);
    $response->assertSee('id="duvidas"', false);
});

test('formulario de duvida grava o registro', function (): void {
    Volt::test('pages.inicio')
        ->set('nome', 'João Souza')
        ->set('email', 'joao@example.com')
        ->set('telefone', '21977776666')
        ->set('mensagem', 'O cadastro já libera o painel?')
        ->call('enviar')
        ->assertHasNoErrors()
        ->assertSet('enviada', true);

    $this->assertDatabaseHas('duvidas', [
        'nome' => 'João Souza',
        'email' => 'joao@example.com',
        'telefone' => '21977776666',
        'mensagem' => 'O cadastro já libera o painel?',
    ]);
});

test('formulario de duvida rejeita e-mail invalido', function (): void {
    Volt::test('pages.inicio')
        ->set('nome', 'João Souza')
        ->set('email', 'nao-e-email')
        ->set('telefone', '21977776666')
        ->set('mensagem', 'Quero saber mais.')
        ->call('enviar')
        ->assertHasErrors(['email']);

    expect(Duvida::query()->count())->toBe(0);
});

test('visitante autenticado continua vendo a pagina e o atalho do painel', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee(route('dashboard'), false);
});
