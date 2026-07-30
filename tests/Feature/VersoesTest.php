<?php

use App\Models\User;
use App\Support\Versoes;
use Livewire\Volt\Volt;

test('visitante nao acessa a pagina de versoes', function () {
    $this->get('/versoes')->assertRedirect('/login');
});

test('usuario autenticado ve o historico completo', function () {
    $this->actingAs(User::factory()->create())
        ->get('/versoes')
        ->assertOk()
        ->assertSeeVolt('pages.versoes.index')
        ->assertSee('1.0.0-alpha.0')
        ->assertSee('1.0.0-alpha.1')
        ->assertSee(Versoes::numeroAtual());
});

test('rota legada /versions redireciona para /versoes', function () {
    $this->actingAs(User::factory()->create())
        ->get('/versions')
        ->assertRedirect('/versoes');
});

test('busca reduz o historico as entregas correspondentes', function () {
    $this->actingAs(User::factory()->create());

    Volt::test('pages.versoes.index')
        ->set('busca', 'Convidar membro para o time')
        ->assertSee('1.0.0-alpha.1')
        ->assertDontSee('1.0.0-alpha.0');
});

test('filtro por estado alterna e limpa', function () {
    $this->actingAs(User::factory()->create());

    Volt::test('pages.versoes.index')
        ->call('filtrarPor', 'bug')
        ->assertSet('estado', 'bug')
        ->assertSee('Excluir tarefa arquivada')
        ->assertDontSee('Restaurar tarefa arquivada')
        ->call('filtrarPor', 'bug')
        ->assertSet('estado', '')
        ->assertSee('Restaurar tarefa arquivada');
});

test('helper mantem o rastreio das versoes antigas', function () {
    $versoes = collect(Versoes::todas())->pluck('versao');

    expect($versoes)->toContain('1.0.0-alpha.0', '1.0.0-alpha.1')
        ->and(Versoes::totalEntregas())->toBeGreaterThan(0)
        ->and(Versoes::contagemPorEstado())->toHaveKeys(['stable', 'development', 'test', 'bug']);
});
