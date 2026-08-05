<?php

use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\Time;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->cenario = criarCenarioDoisTimes();
});

function sessaoTimeConta(int $timeId, int $contaId): array
{
    return [
        CurrentTeam::SESSION_KEY => $timeId,
        CurrentTeam::CONTA_SESSION_KEY => $contaId,
    ];
}

test('criarCenarioDoisTimes expoe contas distintas', function () {
    extract($this->cenario);

    expect($contaA->id)->not->toBe($contaB->id)
        ->and($timeA->conta_id)->toBe($contaA->id)
        ->and($timeB->conta_id)->toBe($contaB->id);
});

test('user A lista apenas clientes do time A', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    $clientes = Cliente::all();

    expect($clientes)->toHaveCount(1)
        ->and($clientes->first()->id)->toBe($clienteA->id);
});

test('user A nao ve cliente B via Cliente::find', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Cliente::find($clienteB->id))->toBeNull();
});

test('user A nao pode ver cliente B via policy', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Gate::forUser($userA)->denies('view', $clienteB))->toBeTrue();
});

test('user A pode ver cliente A via policy com conta na sessao', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Gate::forUser($userA)->allows('view', $clienteA))->toBeTrue();
});

test('user A nao pode ver cliente A via policy sem current_conta_id', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession([CurrentTeam::SESSION_KEY => $timeA->id]);

    expect(Gate::forUser($userA)->denies('view', $clienteA))->toBeTrue();
});

test('user A nao pode ver projeto B via scope', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Projeto::find($projetoB->id))->toBeNull();
});

test('user A nao pode ver projeto B via policy', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Gate::forUser($userA)->denies('view', $projetoB))->toBeTrue();
});

test('user A nao pode ver tarefa B via scope', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Tarefa::find($tarefaB->id))->toBeNull();
});

test('user A nao pode ver tarefa B via policy', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Gate::forUser($userA)->denies('view', $tarefaB))->toBeTrue();
});

test('user A nao consegue deletar time B', function () {
    extract($this->cenario);

    $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id));

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

test('membership em time de outra conta nao vaza via scope nem policy', function () {
    extract($this->cenario);

    TimeMembroFactory::new()->create([
        'time_id' => $timeB->id,
        'usuario_id' => $userA->id,
    ]);

    $this->actingAs($userA)
        ->withSession([CurrentTeam::CONTA_SESSION_KEY => $contaA->id]);

    expect(Time::pluck('id')->all())->toBe([$timeA->id])
        ->and(Time::find($timeB->id))->toBeNull();

    $this->withSession(sessaoTimeConta($timeA->id, $contaA->id));

    expect(Gate::forUser($userA)->denies('view', $clienteB))->toBeTrue()
        ->and(Gate::forUser($userA)->denies('view', $timeB))->toBeTrue();
});

test('user A recebe 403 ou 404 ao acessar cliente B via rota quando existir', function () {
    extract($this->cenario);

    if (! Route::has('clientes.edit')) {
        expect(true)->toBeTrue();

        return;
    }

    $response = $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id))
        ->get(route('clientes.edit', $clienteB));

    expect($response->status())->toBeIn([403, 404]);
});

test('user A recebe 403 ou 404 ao acessar projeto B via rota', function () {
    extract($this->cenario);

    $response = $this->actingAs($userA)
        ->withSession(sessaoTimeConta($timeA->id, $contaA->id))
        ->get(route('projetos.show', $projetoB));

    expect($response->status())->toBeIn([403, 404]);
});
