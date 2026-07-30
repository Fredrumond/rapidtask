<?php

use App\Models\Cliente;
use App\Models\Concerns\TeamScope;
use App\Models\Tarefa;
use App\Models\Time;
use App\Models\User;

beforeEach(function () {
    $this->cenario = criarCenarioDoisTimes();
});

test('applyTimeIdFilter restringe ao current_time_id quando definido', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(['current_time_id' => $timeA->id]);

    expect(Cliente::count())->toBe(1)
        ->and(Cliente::query()->toSql())->toContain('time_id');
});

test('applyTimeIdFilter usa memberships quando current_time_id ausente', function () {
    extract($this->cenario);

    $this->actingAs($userA);

    expect(Cliente::count())->toBe(1)
        ->and(Cliente::first()->id)->toBe($clienteA->id);
});

test('applyTimeIdFilter retorna vazio para usuario sem memberships', function () {
    $outsider = User::factory()->create();
    $this->actingAs($outsider);

    expect(Cliente::count())->toBe(0);
});

test('applyMemberFilter limita times ao usuario autenticado', function () {
    extract($this->cenario);

    $this->actingAs($userA);

    expect(Time::count())->toBe(1)
        ->and(Time::first()->id)->toBe($timeA->id);
});

test('applyProjetoTimeFilter isola tarefas por time do projeto', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(['current_time_id' => $timeA->id]);

    expect(Tarefa::count())->toBe(1)
        ->and(Tarefa::first()->id)->toBe($tarefaA->id);
});

test('applyTimeIdFilter nao aplica filtro para convidado', function () {
    extract($this->cenario);

    $builder = Cliente::query();
    $model = new Cliente;
    $guestBuilder = $model->newQueryWithoutScopes();

    TeamScope::applyTimeIdFilter($guestBuilder, 'clientes.time_id');

    expect($guestBuilder->count())->toBe(2);
});
