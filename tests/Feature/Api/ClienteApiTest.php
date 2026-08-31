<?php

use Laravel\Sanctum\Sanctum;

function clientesListUrl(int $timeId): string
{
    return '/api/clientes?time_id='.$timeId;
}

function clienteShowUrl(int $timeId, int $clienteId): string
{
    return '/api/clientes/'.$clienteId.'?time_id='.$timeId;
}

function payloadCliente(int $timeId, array $overrides = []): array
{
    return array_merge([
        'time_id' => $timeId,
        'nome' => 'Cliente via API',
        'email' => 'cliente.api@example.test',
        'telefone' => '11988887777',
    ], $overrides);
}

test('clientes api returns unauthorized without bearer token', function (string $method, string $uri): void {
    $this->json($method, $uri, payloadCliente(1))->assertUnauthorized();
})->with([
    ['GET', '/api/clientes?time_id=1'],
    ['POST', '/api/clientes'],
    ['GET', '/api/clientes/1?time_id=1'],
    ['PUT', '/api/clientes/1'],
    ['DELETE', '/api/clientes/1'],
]);

test('get clientes returns bad request without time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/clientes')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('get clientes returns forbidden when time belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(clientesListUrl($cenario['timeB']->id))
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('get clientes lists only clients from the informed team with nested relations', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->getJson(clientesListUrl($cenario['timeA']->id))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'nome',
                    'email',
                    'telefone',
                    'time' => ['id', 'nome'],
                    'usuario' => ['id', 'name'],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($cenario['clienteA']->id);
    expect($ids)->not->toContain($cenario['clienteB']->id);
});

test('get cliente show returns client detail with nested relations', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(clienteShowUrl($cenario['timeA']->id, $cenario['clienteA']->id))
        ->assertOk()
        ->assertJsonPath('data.id', $cenario['clienteA']->id)
        ->assertJsonPath('data.nome', $cenario['clienteA']->nome)
        ->assertJsonPath('data.email', $cenario['clienteA']->email)
        ->assertJsonPath('data.time.id', $cenario['timeA']->id)
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonStructure([
            'data' => [
                'time' => ['id', 'nome'],
                'usuario' => ['id', 'name'],
            ],
        ]);
});

test('get cliente show returns not found for client from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(clienteShowUrl($cenario['timeA']->id, $cenario['clienteB']->id))
        ->assertNotFound();
});

test('get cliente show returns not found for missing client', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(clienteShowUrl($cenario['timeA']->id, 999999))
        ->assertNotFound();
});

test('post clientes creates client attributed to conta owner', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadCliente($cenario['timeA']->id);

    $response = $this->postJson('/api/clientes', $payload)
        ->assertCreated()
        ->assertJsonPath('data.nome', 'Cliente via API')
        ->assertJsonPath('data.email', 'cliente.api@example.test')
        ->assertJsonPath('data.telefone', '11988887777')
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonPath('data.time.id', $cenario['timeA']->id)
        ->assertJsonStructure([
            'data' => [
                'time' => ['id', 'nome'],
                'usuario' => ['id', 'name'],
            ],
        ]);

    $this->assertDatabaseHas('clientes', [
        'id' => $response->json('data.id'),
        'nome' => 'Cliente via API',
        'email' => 'cliente.api@example.test',
        'telefone' => '11988887777',
        'usuario_id' => $cenario['userA']->id,
        'time_id' => $cenario['timeA']->id,
    ]);
});

test('post clientes returns validation errors without nome', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->postJson('/api/clientes', [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nome']);
});

test('put clientes updates client completely', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $payload = payloadCliente($cenario['timeA']->id, [
        'nome' => 'Cliente atualizado',
        'email' => 'novo@example.test',
        'telefone' => '11911112222',
    ]);

    $this->putJson('/api/clientes/'.$cenario['clienteA']->id, $payload)
        ->assertOk()
        ->assertJsonPath('data.nome', 'Cliente atualizado')
        ->assertJsonPath('data.email', 'novo@example.test')
        ->assertJsonPath('data.telefone', '11911112222')
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonPath('data.time.id', $cenario['timeA']->id);

    $this->assertDatabaseHas('clientes', [
        'id' => $cenario['clienteA']->id,
        'nome' => 'Cliente atualizado',
        'email' => 'novo@example.test',
        'telefone' => '11911112222',
        'usuario_id' => $cenario['userA']->id,
        'time_id' => $cenario['timeA']->id,
    ]);
});

test('put clientes returns validation errors without nome', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson('/api/clientes/'.$cenario['clienteA']->id, [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nome']);
});

test('put clientes returns not found for client from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson(
        '/api/clientes/'.$cenario['clienteB']->id,
        payloadCliente($cenario['timeA']->id),
    )->assertNotFound();
});

test('put clientes returns not found for missing client', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->putJson('/api/clientes/999999', payloadCliente($cenario['timeA']->id))
        ->assertNotFound();
});

test('delete clientes soft deletes client', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson('/api/clientes/'.$cenario['clienteA']->id, [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Cliente excluído com sucesso.');

    $this->assertSoftDeleted('clientes', [
        'id' => $cenario['clienteA']->id,
    ]);

    $this->getJson(clienteShowUrl($cenario['timeA']->id, $cenario['clienteA']->id))
        ->assertNotFound();
});

test('delete clientes returns not found for client from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson('/api/clientes/'.$cenario['clienteB']->id, [
        'time_id' => $cenario['timeA']->id,
    ])->assertNotFound();

    $this->assertDatabaseHas('clientes', [
        'id' => $cenario['clienteB']->id,
        'deleted_at' => null,
    ]);
});

test('delete clientes returns not found for missing client', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson('/api/clientes/999999', [
        'time_id' => $cenario['timeA']->id,
    ])->assertNotFound();
});

test('token of conta A cannot list or mutate clientes of conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(clientesListUrl($cenario['timeB']->id))
        ->assertForbidden();

    $this->getJson(clienteShowUrl($cenario['timeB']->id, $cenario['clienteB']->id))
        ->assertForbidden();

    $this->postJson('/api/clientes', payloadCliente($cenario['timeB']->id))
        ->assertForbidden();

    $this->putJson(
        '/api/clientes/'.$cenario['clienteB']->id,
        payloadCliente($cenario['timeB']->id),
    )->assertForbidden();

    $this->deleteJson('/api/clientes/'.$cenario['clienteB']->id, [
        'time_id' => $cenario['timeB']->id,
    ])->assertForbidden();
});
