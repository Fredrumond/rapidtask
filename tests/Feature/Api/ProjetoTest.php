<?php

use App\Models\Cliente;
use Laravel\Sanctum\Sanctum;

function projetosListUrl(int $timeId): string
{
    return '/api/projetos?time_id='.$timeId;
}

function projetoShowUrl(int $timeId, int $projetoId): string
{
    return '/api/projetos/'.$projetoId.'?time_id='.$timeId;
}

function payloadProjeto(int $timeId, int $clienteId, array $overrides = []): array
{
    return array_merge([
        'time_id' => $timeId,
        'nome' => 'Projeto via API',
        'sigla' => 'PAPI',
        'cliente_id' => $clienteId,
        'descricao' => 'Descrição de teste',
        'dt_inicio' => '2026-08-01',
        'dt_prevista' => '2026-08-15',
    ], $overrides);
}

test('get projetos returns unauthorized without bearer token', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->getJson(projetosListUrl($cenario['timeA']->id))
        ->assertUnauthorized();
});

test('get projetos returns bad request without time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/projetos')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('get projetos returns bad request with invalid time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/projetos?time_id=abc')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é inválido.');
});

test('get projetos returns forbidden when time belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(projetosListUrl($cenario['timeB']->id))
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('get projetos lists only projects from the informed team with nested relations', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->getJson(projetosListUrl($cenario['timeA']->id))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'nome',
                    'sigla',
                    'descricao',
                    'dt_inicio',
                    'dt_prevista',
                    'dt_fim',
                    'cliente' => ['id', 'nome'],
                    'time' => ['id', 'nome'],
                    'usuario' => ['id', 'name'],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($cenario['projetoA']->id);
    expect($ids)->not->toContain($cenario['projetoB']->id);
});

test('get projeto show returns project detail with nested relations', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(projetoShowUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertOk()
        ->assertJsonPath('data.id', $cenario['projetoA']->id)
        ->assertJsonPath('data.nome', $cenario['projetoA']->nome)
        ->assertJsonPath('data.sigla', $cenario['projetoA']->sigla)
        ->assertJsonPath('data.cliente.id', $cenario['clienteA']->id)
        ->assertJsonPath('data.time.id', $cenario['timeA']->id)
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonStructure([
            'data' => [
                'cliente' => ['id', 'nome'],
                'time' => ['id', 'nome'],
                'usuario' => ['id', 'name'],
            ],
        ]);
});

test('get projeto show returns not found for project from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(projetoShowUrl($cenario['timeA']->id, $cenario['projetoB']->id))
        ->assertNotFound();
});

test('post projetos creates project attributed to conta owner', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadProjeto($cenario['timeA']->id, $cenario['clienteA']->id);

    $response = $this->postJson('/api/projetos', $payload)
        ->assertCreated()
        ->assertJsonPath('data.nome', 'Projeto via API')
        ->assertJsonPath('data.sigla', 'PAPI')
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonPath('data.cliente.id', $cenario['clienteA']->id)
        ->assertJsonPath('data.time.id', $cenario['timeA']->id)
        ->assertJsonStructure([
            'data' => [
                'cliente' => ['id', 'nome'],
                'time' => ['id', 'nome'],
                'usuario' => ['id', 'name'],
            ],
        ]);

    $this->assertDatabaseHas('projetos', [
        'id' => $response->json('data.id'),
        'nome' => 'Projeto via API',
        'sigla' => 'PAPI',
        'usuario_id' => $cenario['userA']->id,
        'time_id' => $cenario['timeA']->id,
        'cliente_id' => $cenario['clienteA']->id,
    ]);
});

test('post projetos returns bad request without time_id in body', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadProjeto($cenario['timeA']->id, $cenario['clienteA']->id);
    unset($payload['time_id']);

    $this->postJson('/api/projetos', $payload)
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('post projetos rejects cliente from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson('/api/projetos', payloadProjeto($cenario['timeA']->id, $cenario['clienteB']->id))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['cliente_id']);
});

test('post projetos returns forbidden when time_id belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson('/api/projetos', payloadProjeto($cenario['timeB']->id, $cenario['clienteB']->id))
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('post projetos returns validation errors without nome sigla and cliente_id', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson('/api/projetos', [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nome', 'sigla', 'cliente_id']);
});

test('put projetos updates project completely', function (): void {
    $cenario = criarCenarioDoisTimes();

    $clienteNovo = Cliente::factory()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $cenario['userA']->id,
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadProjeto($cenario['timeA']->id, $clienteNovo->id, [
        'nome' => 'Nome atualizado',
        'sigla' => 'NA',
        'descricao' => 'Nova descrição',
        'dt_inicio' => '2026-09-01',
        'dt_prevista' => '2026-09-15',
        'dt_fim' => '2026-09-30',
    ]);

    $this->putJson('/api/projetos/'.$cenario['projetoA']->id, $payload)
        ->assertOk()
        ->assertJsonPath('data.nome', 'Nome atualizado')
        ->assertJsonPath('data.sigla', 'NA')
        ->assertJsonPath('data.descricao', 'Nova descrição')
        ->assertJsonPath('data.cliente.id', $clienteNovo->id)
        ->assertJsonPath('data.dt_inicio', '2026-09-01')
        ->assertJsonPath('data.dt_prevista', '2026-09-15')
        ->assertJsonPath('data.dt_fim', '2026-09-30')
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonPath('data.time.id', $cenario['timeA']->id);

    $this->assertDatabaseHas('projetos', [
        'id' => $cenario['projetoA']->id,
        'nome' => 'Nome atualizado',
        'sigla' => 'NA',
        'descricao' => 'Nova descrição',
        'cliente_id' => $clienteNovo->id,
        'usuario_id' => $cenario['userA']->id,
        'time_id' => $cenario['timeA']->id,
    ]);
});

test('put projetos returns not found for project from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson(
        '/api/projetos/'.$cenario['projetoB']->id,
        payloadProjeto($cenario['timeA']->id, $cenario['clienteA']->id),
    )->assertNotFound();
});

test('delete projetos soft deletes project', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson('/api/projetos/'.$cenario['projetoA']->id, [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Projeto excluído com sucesso.');

    $this->assertSoftDeleted('projetos', [
        'id' => $cenario['projetoA']->id,
    ]);

    $this->getJson(projetoShowUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertNotFound();
});

test('delete projetos returns not found for project from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson('/api/projetos/'.$cenario['projetoB']->id, [
        'time_id' => $cenario['timeA']->id,
    ])->assertNotFound();

    $this->assertDatabaseHas('projetos', [
        'id' => $cenario['projetoB']->id,
        'deleted_at' => null,
    ]);
});

test('token of conta A cannot list or mutate projetos of conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(projetosListUrl($cenario['timeB']->id))
        ->assertForbidden();

    $this->getJson(projetoShowUrl($cenario['timeB']->id, $cenario['projetoB']->id))
        ->assertForbidden();

    $this->postJson('/api/projetos', payloadProjeto($cenario['timeB']->id, $cenario['clienteB']->id))
        ->assertForbidden();

    $this->putJson(
        '/api/projetos/'.$cenario['projetoB']->id,
        payloadProjeto($cenario['timeB']->id, $cenario['clienteB']->id),
    )->assertForbidden();

    $this->deleteJson('/api/projetos/'.$cenario['projetoB']->id, [
        'time_id' => $cenario['timeB']->id,
    ])->assertForbidden();
});
