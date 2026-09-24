<?php

use App\Models\Duvida;
use App\Models\User;
use Livewire\Volt\Volt;

test('a pagina inicial apresenta o rapidtask e leva ao cadastro', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('RapidTask')
        ->assertSee('Projetos, tarefas e clientes no mesmo lugar.')
        ->assertSee('Criar conta')
        ->assertSee(route('register'), false)
        ->assertSee('Autenticação')
        ->assertSee('CRUD de tarefas, projetos, clientes e comentários')
        ->assertSee('Gerenciamento de tokens')
        ->assertSee('Dashboard')
        ->assertSee('Passei a enxergar o andamento dos projetos sem cobrar atualização o tempo todo.')
        ->assertSee('Marina Costa')
        ->assertSee('Paulo Henrique')
        ->assertSee('Aline Ferreira')
        ->assertSee('Ricardo Nunes')
        ->assertSee('Anterior')
        ->assertSee('Próximo')
        ->assertSee('id="duvidas"', false);
});

test('usuario autenticado ve a pagina inicial com atalho para o painel', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertOk()
        ->assertSee('Painel')
        ->assertSee(route('dashboard'), false)
        ->assertDontSee('Criar conta');
});

test('visitante registra uma duvida', function () {
    Volt::test('pages.inicio')
        ->set('nome', 'Ana Lima')
        ->set('email', 'Ana@Example.com')
        ->set('telefone', '11988887777')
        ->set('duvida', 'Como funciona o painel?')
        ->call('enviar')
        ->assertHasNoErrors()
        ->assertSet('enviada', true)
        ->assertSet('nome', '')
        ->assertSet('duvida', '');

    $duvida = Duvida::query()->where('email', 'ana@example.com')->first();

    expect($duvida)->not->toBeNull()
        ->and($duvida->nome)->toBe('Ana Lima')
        ->and($duvida->telefone)->toBe('11988887777')
        ->and($duvida->duvida)->toBe('Como funciona o painel?');
});

test('formulario de duvida exige nome email telefone e texto', function () {
    Volt::test('pages.inicio')
        ->set('nome', '')
        ->set('email', 'nao-e-email')
        ->set('telefone', '')
        ->set('duvida', '')
        ->call('enviar')
        ->assertHasErrors(['nome', 'email', 'telefone', 'duvida']);

    expect(Duvida::query()->count())->toBe(0);
});
