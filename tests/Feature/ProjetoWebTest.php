<?php

use App\Models\Projeto;
use App\Support\CurrentTeam;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Volt\Volt;

function sessaoTimeContaProjeto(int $timeId, int $contaId): array
{
    return [
        CurrentTeam::SESSION_KEY => $timeId,
        CurrentTeam::CONTA_SESSION_KEY => $contaId,
    ];
}

test('pagina create salva projeto via service', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.create')
        ->set('nome', 'Projeto Web')
        ->set('sigla', 'PWEB')
        ->set('descricao', 'Criado pela web')
        ->set('cliente_id', $cenario['clienteA']->id)
        ->set('dt_inicio', '2026-08-01')
        ->set('dt_prevista', '2026-08-15')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('projetos.index'));

    $projeto = Projeto::query()->where('sigla', 'PWEB')->first();

    expect($projeto)->not->toBeNull()
        ->and($projeto->nome)->toBe('Projeto Web')
        ->and($projeto->descricao)->toBe('Criado pela web')
        ->and($projeto->cliente_id)->toBe($cenario['clienteA']->id)
        ->and($projeto->usuario_id)->toBe($cenario['userA']->id)
        ->and($projeto->time_id)->toBe($cenario['timeA']->id)
        ->and($projeto->dt_inicio?->format('Y-m-d'))->toBe('2026-08-01')
        ->and($projeto->dt_prevista?->format('Y-m-d'))->toBe('2026-08-15');
});

test('pagina create sem nome mostra erro de validacao', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.create')
        ->set('nome', '')
        ->set('sigla', 'PWEB')
        ->set('cliente_id', $cenario['clienteA']->id)
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('pagina create sem cliente mostra erro de validacao', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.create')
        ->set('nome', 'Projeto Web')
        ->set('sigla', 'PWEB')
        ->set('cliente_id', null)
        ->call('save')
        ->assertHasErrors(['cliente_id']);
});

test('pagina create com nome em branco dispara erro de dominio', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.create')
        ->set('nome', '   ')
        ->set('sigla', 'PWEB')
        ->set('cliente_id', $cenario['clienteA']->id)
        ->call('save')
        ->assertHasErrors(['nome']);

    expect(Projeto::query()->where('sigla', 'PWEB')->exists())->toBeFalse();
});

test('visitante nao autorizado recebe 403 ao criar projeto', function (): void {
    Volt::test('pages.projetos.create')
        ->assertForbidden();
});

test('pagina edit atualiza projeto via service', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.edit', ['projeto' => $cenario['projetoA']])
        ->set('nome', 'Projeto Atualizado')
        ->set('sigla', 'PACT')
        ->set('descricao', 'Atualizado pela web')
        ->set('cliente_id', $cenario['clienteA']->id)
        ->set('dt_fim', '2026-08-20')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('projetos.index'));

    $projeto = $cenario['projetoA']->fresh();

    expect($projeto->nome)->toBe('Projeto Atualizado')
        ->and($projeto->sigla)->toBe('PACT')
        ->and($projeto->descricao)->toBe('Atualizado pela web')
        ->and($projeto->dt_fim?->format('Y-m-d'))->toBe('2026-08-20')
        ->and($projeto->time_id)->toBe($cenario['timeA']->id);
});

test('pagina edit sem nome mostra erro de validacao', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.edit', ['projeto' => $cenario['projetoA']])
        ->set('nome', '')
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('user A recebe 403 ao editar projeto do time B', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.edit', ['projeto' => $cenario['projetoB']])
        ->assertForbidden();
});

test('pagina index exclui projeto via service', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.index')
        ->call('delete', $cenario['projetoA']->id)
        ->assertHasNoErrors();

    $this->assertSoftDeleted('projetos', [
        'id' => $cenario['projetoA']->id,
    ]);
});

test('user A nao exclui projeto do time B', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaProjeto($cenario['timeA']->id, $cenario['contaA']->id));

    expect(fn () => Volt::test('pages.projetos.index')->call('delete', $cenario['projetoB']->id))
        ->toThrow(ModelNotFoundException::class);

    $this->assertDatabaseHas('projetos', [
        'id' => $cenario['projetoB']->id,
        'deleted_at' => null,
    ]);
});

test('visitante nao autorizado recebe 403 ao listar projetos', function (): void {
    Volt::test('pages.projetos.index')
        ->assertForbidden();
});
