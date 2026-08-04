<?php

use App\Support\CurrentTeam;
use Laravel\Sanctum\Sanctum;

function apiTarefaHeaders(int $timeId): array
{
    return [CurrentTeam::HEADER_NAME => (string) $timeId];
}

function payloadTarefa(int $projetoId, array $overrides = []): array
{
    return array_merge([
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

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->getJson('/api/tarefas')
        ->assertUnauthorized();
});

test('get tarefas returns bad request without X-Time-Id header', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $this->getJson('/api/tarefas')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O header X-Time-Id é obrigatório.');
});

test('get tarefas returns bad request with invalid X-Time-Id header', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders([CurrentTeam::HEADER_NAME => 'abc'])
        ->getJson('/api/tarefas')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O header X-Time-Id é inválido.');
});

test('get tarefas returns forbidden when user does not belong to time', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders(apiTarefaHeaders($cenario['timeB']->id))
        ->getJson('/api/tarefas')
        ->assertForbidden()
        ->assertJsonPath('message', 'Você não pertence ao time informado.');
});

test('get tarefas lists only tasks from the informed team with nested relations', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $response = $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->getJson('/api/tarefas')
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

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->getJson('/api/tarefas/'.$cenario['tarefaA']->id)
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

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->getJson('/api/tarefas/'.$cenario['tarefaB']->id)
        ->assertNotFound();
});

test('post tarefas creates task for authenticated user', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $payload = payloadTarefa($cenario['projetoA']->id);

    $response = $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->postJson('/api/tarefas', $payload)
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

test('post tarefas rejects projeto from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->postJson('/api/tarefas', payloadTarefa($cenario['projetoB']->id))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['projeto_id']);
});

test('put tarefas updates task completely', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $payload = payloadTarefa($cenario['projetoA']->id, [
        'titulo' => 'Título atualizado',
        'descricao' => 'Nova descrição',
        'situacao_id' => 2,
        'prioridade_id' => 3,
        'status' => 0,
    ]);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->putJson('/api/tarefas/'.$cenario['tarefaA']->id, $payload)
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

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->putJson('/api/tarefas/'.$cenario['tarefaB']->id, payloadTarefa($cenario['projetoA']->id))
        ->assertNotFound();
});

test('delete tarefas soft deletes task', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->deleteJson('/api/tarefas/'.$cenario['tarefaA']->id)
        ->assertOk()
        ->assertJsonPath('message', 'Tarefa excluída com sucesso.');

    $this->assertSoftDeleted('tarefas', [
        'id' => $cenario['tarefaA']->id,
    ]);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->getJson('/api/tarefas/'.$cenario['tarefaA']->id)
        ->assertNotFound();
});

test('delete tarefas returns not found for task from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['userA']);

    $this->withHeaders(apiTarefaHeaders($cenario['timeA']->id))
        ->deleteJson('/api/tarefas/'.$cenario['tarefaB']->id)
        ->assertNotFound();

    $this->assertDatabaseHas('tarefas', [
        'id' => $cenario['tarefaB']->id,
        'deleted_at' => null,
    ]);
});
