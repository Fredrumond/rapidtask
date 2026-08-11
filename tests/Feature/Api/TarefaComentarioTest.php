<?php

use App\Models\TarefaComentario;
use App\Models\User;
use Database\Factories\TimeMembroFactory;
use Laravel\Sanctum\Sanctum;

function comentariosListUrl(int $timeId, int $tarefaId): string
{
    return '/api/tarefas/'.$tarefaId.'/comentarios?time_id='.$timeId;
}

function comentarioUrl(int $tarefaId, int $comentarioId): string
{
    return '/api/tarefas/'.$tarefaId.'/comentarios/'.$comentarioId;
}

function payloadComentario(int $timeId, array $overrides = []): array
{
    return array_merge([
        'time_id' => $timeId,
        'comentario' => 'Comentário via API',
    ], $overrides);
}

test('get comentarios returns unauthorized without bearer token', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->getJson(comentariosListUrl($cenario['timeA']->id, $cenario['tarefaA']->id))
        ->assertUnauthorized();
});

test('get comentarios returns bad request without time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/tarefas/'.$cenario['tarefaA']->id.'/comentarios')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('get comentarios lists only comments of the task newest first', function (): void {
    $cenario = criarCenarioDoisTimes();

    $maisAntigo = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $cenario['userA']->id,
        'comentario' => 'Antigo',
        'created_at' => now()->subHour(),
    ]);

    $maisNovo = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $cenario['userA']->id,
        'comentario' => 'Novo',
        'created_at' => now(),
    ]);

    TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaB']->id,
        'usuario_id' => $cenario['userB']->id,
        'comentario' => 'Outro time',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->getJson(comentariosListUrl($cenario['timeA']->id, $cenario['tarefaA']->id))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'tarefa_id',
                    'comentario',
                    'usuario' => ['id', 'name'],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $ids = collect($response->json('data'))->pluck('id')->all();

    expect($ids)->toBe([$maisNovo->id, $maisAntigo->id])
        ->and($ids)->not->toContain(
            TarefaComentario::query()->withoutGlobalScopes()->where('comentario', 'Outro time')->value('id')
        );
});

test('get comentarios returns not found for task from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(comentariosListUrl($cenario['timeA']->id, $cenario['tarefaB']->id))
        ->assertNotFound();
});

test('post comentarios creates comment attributed to conta owner', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->postJson(
        '/api/tarefas/'.$cenario['tarefaA']->id.'/comentarios',
        payloadComentario($cenario['timeA']->id),
    )
        ->assertCreated()
        ->assertJsonPath('data.comentario', 'Comentário via API')
        ->assertJsonPath('data.tarefa_id', $cenario['tarefaA']->id)
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id);

    $this->assertDatabaseHas('tarefa_comentario', [
        'id' => $response->json('data.id'),
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $cenario['userA']->id,
        'comentario' => 'Comentário via API',
    ]);
});

test('post comentarios returns forbidden when time_id belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson(
        '/api/tarefas/'.$cenario['tarefaB']->id.'/comentarios',
        payloadComentario($cenario['timeB']->id),
    )
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('put comentarios updates own comment', function (): void {
    $cenario = criarCenarioDoisTimes();

    $comentario = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $cenario['userA']->id,
        'comentario' => 'Original',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson(
        comentarioUrl($cenario['tarefaA']->id, $comentario->id),
        payloadComentario($cenario['timeA']->id, ['comentario' => 'Atualizado']),
    )
        ->assertOk()
        ->assertJsonPath('data.comentario', 'Atualizado');

    $this->assertDatabaseHas('tarefa_comentario', [
        'id' => $comentario->id,
        'comentario' => 'Atualizado',
    ]);
});

test('put comentarios returns forbidden when comment belongs to another user', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $comentario = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $outroUser->id,
        'comentario' => 'De outro membro',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson(
        comentarioUrl($cenario['tarefaA']->id, $comentario->id),
        payloadComentario($cenario['timeA']->id, ['comentario' => 'Tentativa']),
    )->assertForbidden();

    $this->assertDatabaseHas('tarefa_comentario', [
        'id' => $comentario->id,
        'comentario' => 'De outro membro',
    ]);
});

test('delete comentarios soft deletes own comment', function (): void {
    $cenario = criarCenarioDoisTimes();

    $comentario = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $cenario['userA']->id,
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson(comentarioUrl($cenario['tarefaA']->id, $comentario->id), [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Comentário excluído com sucesso.');

    $this->assertSoftDeleted('tarefa_comentario', [
        'id' => $comentario->id,
    ]);
});

test('delete comentarios returns forbidden when comment belongs to another user', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $comentario = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson(comentarioUrl($cenario['tarefaA']->id, $comentario->id), [
        'time_id' => $cenario['timeA']->id,
    ])->assertForbidden();

    $this->assertDatabaseHas('tarefa_comentario', [
        'id' => $comentario->id,
        'deleted_at' => null,
    ]);
});

test('token of conta A cannot list or mutate comments of conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    $comentarioB = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaB']->id,
        'usuario_id' => $cenario['userB']->id,
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(comentariosListUrl($cenario['timeB']->id, $cenario['tarefaB']->id))
        ->assertForbidden();

    $this->postJson(
        '/api/tarefas/'.$cenario['tarefaB']->id.'/comentarios',
        payloadComentario($cenario['timeB']->id),
    )->assertForbidden();

    $this->putJson(
        comentarioUrl($cenario['tarefaB']->id, $comentarioB->id),
        payloadComentario($cenario['timeB']->id, ['comentario' => 'Hack']),
    )->assertForbidden();

    $this->deleteJson(comentarioUrl($cenario['tarefaB']->id, $comentarioB->id), [
        'time_id' => $cenario['timeB']->id,
    ])->assertForbidden();
});
