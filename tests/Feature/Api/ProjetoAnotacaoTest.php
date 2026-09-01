<?php

use App\Models\ProjetoAnotacao;
use App\Models\User;
use Database\Factories\TimeMembroFactory;
use Laravel\Sanctum\Sanctum;

function anotacoesListUrl(int $timeId, int $projetoId): string
{
    return '/api/projetos/'.$projetoId.'/anotacoes?time_id='.$timeId;
}

function anotacaoUrl(int $projetoId, int $anotacaoId): string
{
    return '/api/projetos/'.$projetoId.'/anotacoes/'.$anotacaoId;
}

function payloadAnotacao(int $timeId, array $overrides = []): array
{
    return array_merge([
        'time_id' => $timeId,
        'anotacao' => 'Anotação via API',
    ], $overrides);
}

test('get anotacoes returns unauthorized without bearer token', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->getJson(anotacoesListUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertUnauthorized();
});

test('get anotacoes returns bad request without time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/projetos/'.$cenario['projetoA']->id.'/anotacoes')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('get anotacoes lists only notes of the project newest first', function (): void {
    $cenario = criarCenarioDoisTimes();

    $maisAntiga = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'anotacao' => 'Antiga',
        'created_at' => now()->subHour(),
    ]);

    $maisNova = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'anotacao' => 'Nova',
        'created_at' => now(),
    ]);

    ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
        'anotacao' => 'Outro time',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->getJson(anotacoesListUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'projeto_id',
                    'anotacao',
                    'usuario' => ['id', 'name'],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $ids = collect($response->json('data'))->pluck('id')->all();

    expect($ids)->toBe([$maisNova->id, $maisAntiga->id])
        ->and($ids)->not->toContain(
            ProjetoAnotacao::query()->withoutGlobalScopes()->where('anotacao', 'Outro time')->value('id')
        );
});

test('get anotacoes returns not found for project from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(anotacoesListUrl($cenario['timeA']->id, $cenario['projetoB']->id))
        ->assertNotFound();
});

test('post anotacoes creates note attributed to conta owner', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->postJson(
        '/api/projetos/'.$cenario['projetoA']->id.'/anotacoes',
        payloadAnotacao($cenario['timeA']->id),
    )
        ->assertCreated()
        ->assertJsonPath('data.anotacao', 'Anotação via API')
        ->assertJsonPath('data.projeto_id', $cenario['projetoA']->id)
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id);

    $this->assertDatabaseHas('projetos_anotacoes', [
        'id' => $response->json('data.id'),
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'anotacao' => 'Anotação via API',
    ]);
});

test('post anotacoes returns forbidden when time_id belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson(
        '/api/projetos/'.$cenario['projetoB']->id.'/anotacoes',
        payloadAnotacao($cenario['timeB']->id),
    )
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('put anotacoes updates own note', function (): void {
    $cenario = criarCenarioDoisTimes();

    $anotacao = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'anotacao' => 'Original',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson(
        anotacaoUrl($cenario['projetoA']->id, $anotacao->id),
        payloadAnotacao($cenario['timeA']->id, ['anotacao' => 'Atualizado']),
    )
        ->assertOk()
        ->assertJsonPath('data.anotacao', 'Atualizado');

    $this->assertDatabaseHas('projetos_anotacoes', [
        'id' => $anotacao->id,
        'anotacao' => 'Atualizado',
    ]);
});

test('put anotacoes returns forbidden when note belongs to another user', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $anotacao = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $outroUser->id,
        'anotacao' => 'De outro membro',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson(
        anotacaoUrl($cenario['projetoA']->id, $anotacao->id),
        payloadAnotacao($cenario['timeA']->id, ['anotacao' => 'Tentativa']),
    )->assertForbidden();

    $this->assertDatabaseHas('projetos_anotacoes', [
        'id' => $anotacao->id,
        'anotacao' => 'De outro membro',
    ]);
});

test('delete anotacoes soft deletes own note and hides it from get', function (): void {
    $cenario = criarCenarioDoisTimes();

    $anotacao = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'anotacao' => 'Para excluir',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson(anotacaoUrl($cenario['projetoA']->id, $anotacao->id), [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Anotação excluída com sucesso.');

    $this->assertSoftDeleted('projetos_anotacoes', [
        'id' => $anotacao->id,
    ]);

    $response = $this->getJson(anotacoesListUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertOk();

    expect(collect($response->json('data'))->pluck('id')->all())->not->toContain($anotacao->id);
});

test('delete anotacoes returns forbidden when note belongs to another user', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $anotacao = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson(anotacaoUrl($cenario['projetoA']->id, $anotacao->id), [
        'time_id' => $cenario['timeA']->id,
    ])->assertForbidden();

    $this->assertDatabaseHas('projetos_anotacoes', [
        'id' => $anotacao->id,
        'deleted_at' => null,
    ]);
});

test('token of conta A cannot list or mutate notes of conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    $anotacaoB = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(anotacoesListUrl($cenario['timeB']->id, $cenario['projetoB']->id))
        ->assertForbidden();

    $this->postJson(
        '/api/projetos/'.$cenario['projetoB']->id.'/anotacoes',
        payloadAnotacao($cenario['timeB']->id),
    )->assertForbidden();

    $this->putJson(
        anotacaoUrl($cenario['projetoB']->id, $anotacaoB->id),
        payloadAnotacao($cenario['timeB']->id, ['anotacao' => 'Hack']),
    )->assertForbidden();

    $this->deleteJson(anotacaoUrl($cenario['projetoB']->id, $anotacaoB->id), [
        'time_id' => $cenario['timeB']->id,
    ])->assertForbidden();
});
