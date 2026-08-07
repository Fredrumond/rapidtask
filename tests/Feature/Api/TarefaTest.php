<?php

use Laravel\Sanctum\Sanctum;

function tarefasListUrl(int $timeId): string
{
    return '/api/tarefas?time_id='.$timeId;
}

function tarefaShowUrl(int $timeId, int $tarefaId): string
{
    return '/api/tarefas/'.$tarefaId.'?time_id='.$timeId;
}

function payloadTarefa(int $timeId, int $projetoId, array $overrides = []): array
{
    return array_merge([
        'time_id' => $timeId,
        'titulo' => 'Tarefa via API',
        'descricao' => 'Descrição de teste',
        'projeto_id' => $projetoId,
        'tipo_id' => 1,
        'situacao_id' => 1,
        'prioridade_id' => 2,
        'dt_inicio' => '2026-08-01',
        'dt_prevista' => '2026-08-15',
        'tempo_estimado' => 60,
    ], $overrides);
}

test('get tarefas returns unauthorized without bearer token', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->getJson(tarefasListUrl($cenario['timeA']->id))
        ->assertUnauthorized();
});

test('get tarefas returns bad request without time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/tarefas')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('get tarefas returns bad request with invalid time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/tarefas?time_id=abc')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é inválido.');
});

test('get tarefas returns forbidden when time belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(tarefasListUrl($cenario['timeB']->id))
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('get tarefas lists only tasks from the informed team with nested relations', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->getJson(tarefasListUrl($cenario['timeA']->id))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'titulo',
                    'descricao',
                    'dt_inicio',
                    'dt_prevista',
                    'dt_fim',
                    'tempo_estimado',
                    'status',
                    'tipo' => ['id', 'nome'],
                    'situacao' => ['id', 'nome'],
                    'prioridade' => ['id', 'nome'],
                    'projeto' => ['id', 'nome'],
                    'usuario' => ['id', 'name'],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($cenario['tarefaA']->id);
    expect($ids)->not->toContain($cenario['tarefaB']->id);
});

test('get tarefa show returns task detail with nested relations', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(tarefaShowUrl($cenario['timeA']->id, $cenario['tarefaA']->id))
        ->assertOk()
        ->assertJsonPath('data.id', $cenario['tarefaA']->id)
        ->assertJsonPath('data.titulo', $cenario['tarefaA']->titulo)
        ->assertJsonStructure([
            'data' => [
                'tipo' => ['id', 'nome'],
                'situacao' => ['id', 'nome'],
                'prioridade' => ['id', 'nome'],
                'projeto' => ['id', 'nome'],
                'usuario' => ['id', 'name'],
            ],
        ]);
});

test('get tarefa show returns not found for task from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(tarefaShowUrl($cenario['timeA']->id, $cenario['tarefaB']->id))
        ->assertNotFound();
});

test('post tarefas creates task attributed to conta owner', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadTarefa($cenario['timeA']->id, $cenario['projetoA']->id);

    $response = $this->postJson('/api/tarefas', $payload)
        ->assertCreated()
        ->assertJsonPath('data.titulo', 'Tarefa via API')
        ->assertJsonPath('data.status', 0)
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonPath('data.projeto.id', $cenario['projetoA']->id)
        ->assertJsonStructure([
            'data' => [
                'tipo' => ['id', 'nome'],
                'situacao' => ['id', 'nome'],
                'prioridade' => ['id', 'nome'],
            ],
        ]);

    $this->assertDatabaseHas('tarefas', [
        'id' => $response->json('data.id'),
        'titulo' => 'Tarefa via API',
        'usuario_id' => $cenario['userA']->id,
        'projeto_id' => $cenario['projetoA']->id,
        'status' => 0,
    ]);
});

test('post tarefas returns bad request without time_id in body', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadTarefa($cenario['timeA']->id, $cenario['projetoA']->id);
    unset($payload['time_id']);

    $this->postJson('/api/tarefas', $payload)
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('post tarefas rejects projeto from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson('/api/tarefas', payloadTarefa($cenario['timeA']->id, $cenario['projetoB']->id))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['projeto_id']);
});

test('post tarefas returns forbidden when time_id belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson('/api/tarefas', payloadTarefa($cenario['timeB']->id, $cenario['projetoB']->id))
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('put tarefas updates task completely', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadTarefa($cenario['timeA']->id, $cenario['projetoA']->id, [
        'titulo' => 'Título atualizado',
        'descricao' => 'Nova descrição',
        'situacao_id' => 2,
        'prioridade_id' => 3,
        'status' => 0,
    ]);

    $this->putJson('/api/tarefas/'.$cenario['tarefaA']->id, $payload)
        ->assertOk()
        ->assertJsonPath('data.titulo', 'Título atualizado')
        ->assertJsonPath('data.descricao', 'Nova descrição')
        ->assertJsonPath('data.situacao.id', 2)
        ->assertJsonPath('data.prioridade.id', 3);

    $this->assertDatabaseHas('tarefas', [
        'id' => $cenario['tarefaA']->id,
        'titulo' => 'Título atualizado',
        'situacao_id' => 2,
        'prioridade_id' => 3,
    ]);
});

test('put tarefas returns not found for task from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson(
        '/api/tarefas/'.$cenario['tarefaB']->id,
        payloadTarefa($cenario['timeA']->id, $cenario['projetoA']->id),
    )->assertNotFound();
});

test('delete tarefas soft deletes task', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson('/api/tarefas/'.$cenario['tarefaA']->id, [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Tarefa excluída com sucesso.');

    $this->assertSoftDeleted('tarefas', [
        'id' => $cenario['tarefaA']->id,
    ]);

    $this->getJson(tarefaShowUrl($cenario['timeA']->id, $cenario['tarefaA']->id))
        ->assertNotFound();
});

test('delete tarefas returns not found for task from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson('/api/tarefas/'.$cenario['tarefaB']->id, [
        'time_id' => $cenario['timeA']->id,
    ])->assertNotFound();

    $this->assertDatabaseHas('tarefas', [
        'id' => $cenario['tarefaB']->id,
        'deleted_at' => null,
    ]);
});

test('token of conta A cannot list or mutate resources of conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(tarefasListUrl($cenario['timeB']->id))
        ->assertForbidden();

    $this->getJson(tarefaShowUrl($cenario['timeB']->id, $cenario['tarefaB']->id))
        ->assertForbidden();

    $this->postJson('/api/tarefas', payloadTarefa($cenario['timeB']->id, $cenario['projetoB']->id))
        ->assertForbidden();

    $this->putJson(
        '/api/tarefas/'.$cenario['tarefaB']->id,
        payloadTarefa($cenario['timeB']->id, $cenario['projetoB']->id),
    )->assertForbidden();

    $this->deleteJson('/api/tarefas/'.$cenario['tarefaB']->id, [
        'time_id' => $cenario['timeB']->id,
    ])->assertForbidden();
});
