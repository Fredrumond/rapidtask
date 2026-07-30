<?php

use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\Time;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->cenario = criarCenarioDoisTimes();
});

test('user A lista apenas clientes do time A', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(['current_time_id' => $timeA->id]);

    $clientes = Cliente::all();

    expect($clientes)->toHaveCount(1)
        ->and($clientes->first()->id)->toBe($clienteA->id);
});

test('user A nao ve cliente B via Cliente::find', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(['current_time_id' => $timeA->id]);

    expect(Cliente::find($clienteB->id))->toBeNull();
});

test('user A nao pode ver cliente B via policy', function () {
    extract($this->cenario);

    expect(Gate::forUser($userA)->denies('view', $clienteB))->toBeTrue();
});

test('user A nao pode ver projeto B via scope', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(['current_time_id' => $timeA->id]);

    expect(Projeto::find($projetoB->id))->toBeNull();
});

test('user A nao pode ver projeto B via policy', function () {
    extract($this->cenario);

    expect(Gate::forUser($userA)->denies('view', $projetoB))->toBeTrue();
});

test('user A nao pode ver tarefa B via scope', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(['current_time_id' => $timeA->id]);

    expect(Tarefa::find($tarefaB->id))->toBeNull();
});

test('user A nao pode ver tarefa B via policy', function () {
    extract($this->cenario);

    expect(Gate::forUser($userA)->denies('view', $tarefaB))->toBeTrue();
});

test('user A nao consegue deletar time B', function () {
    extract($this->cenario);

    expect(Gate::forUser($userA)->denies('delete', $timeB))->toBeTrue();
});

test('sem current_time_id queries nao vazam time B para user A', function () {
    extract($this->cenario);

    $this->actingAs($userA);

    $clienteIds = Cliente::pluck('id')->all();
    expect($clienteIds)->toContain($clienteA->id)
        ->and($clienteIds)->not->toContain($clienteB->id);

    $projetoIds = Projeto::pluck('id')->all();
    expect($projetoIds)->toContain($projetoA->id)
        ->and($projetoIds)->not->toContain($projetoB->id);

    $tarefaIds = Tarefa::pluck('id')->all();
    expect($tarefaIds)->toContain($tarefaA->id)
        ->and($tarefaIds)->not->toContain($tarefaB->id);

    $timeIds = Time::pluck('id')->all();
    expect($timeIds)->toContain($timeA->id)
        ->and($timeIds)->not->toContain($timeB->id);
});

test('user A recebe 403 ou 404 ao acessar cliente B via rota quando existir', function () {
    extract($this->cenario);

    if (! Route::has('clientes.show')) {
        expect(true)->toBeTrue();

        return;
    }

    $response = $this->actingAs($userA)
        ->withSession(['current_time_id' => $timeA->id])
        ->get(route('clientes.show', $clienteB));

    expect($response->status())->toBeIn([403, 404]);
});
