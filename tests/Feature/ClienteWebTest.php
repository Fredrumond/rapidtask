<?php

use App\Models\Cliente;
use App\Support\CurrentTeam;
use Livewire\Volt\Volt;

function sessaoTimeContaCliente(int $timeId, int $contaId): array
{
    return [
        CurrentTeam::SESSION_KEY => $timeId,
        CurrentTeam::CONTA_SESSION_KEY => $contaId,
    ];
}

test('pagina create salva cliente via service', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaCliente($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.clientes.create')
        ->set('nome', 'Cliente Web')
        ->set('email', 'web@acme.test')
        ->set('telefone', '11911111111')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('clientes.index'));

    $this->assertDatabaseHas('clientes', [
        'nome' => 'Cliente Web',
        'email' => 'web@acme.test',
        'telefone' => '11911111111',
        'usuario_id' => $cenario['userA']->id,
        'time_id' => $cenario['timeA']->id,
    ]);
});

test('pagina create sem nome mostra erro', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaCliente($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.clientes.create')
        ->set('nome', '')
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('pagina create com nome em branco dispara erro de dominio', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaCliente($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.clientes.create')
        ->set('nome', '   ')
        ->call('save')
        ->assertHasErrors(['nome']);

    expect(Cliente::query()->where('nome', '   ')->exists())->toBeFalse();
});

test('pagina edit atualiza cliente via service', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaCliente($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.clientes.edit', ['cliente' => $cenario['clienteA']])
        ->set('nome', 'Cliente Atualizado')
        ->set('email', 'novo@acme.test')
        ->set('telefone', '')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('clientes.index'));

    $this->assertDatabaseHas('clientes', [
        'id' => $cenario['clienteA']->id,
        'nome' => 'Cliente Atualizado',
        'email' => 'novo@acme.test',
        'telefone' => null,
        'time_id' => $cenario['timeA']->id,
    ]);
});

test('pagina index exclui cliente via service', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaCliente($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.clientes.index')
        ->call('delete', $cenario['clienteA']->id)
        ->assertHasNoErrors();

    $this->assertSoftDeleted('clientes', [
        'id' => $cenario['clienteA']->id,
    ]);
});
