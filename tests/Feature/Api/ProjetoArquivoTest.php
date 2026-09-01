<?php

use App\Models\ProjetoArquivo;
use App\Models\User;
use Database\Factories\TimeMembroFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

function arquivosListUrl(int $timeId, int $projetoId): string
{
    return '/api/projetos/'.$projetoId.'/arquivos?time_id='.$timeId;
}

function arquivoUrl(int $projetoId, int $arquivoId): string
{
    return '/api/projetos/'.$projetoId.'/arquivos/'.$arquivoId;
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function payloadArquivo(int $timeId, array $overrides = []): array
{
    return array_merge([
        'time_id' => $timeId,
        'nome' => 'Contrato',
        'descricao' => 'Contrato assinado',
        'arquivo' => UploadedFile::fake()->create('contrato.pdf', 200, 'application/pdf'),
    ], $overrides);
}

test('get arquivos returns unauthorized without bearer token', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->getJson(arquivosListUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertUnauthorized();
});

test('get arquivos returns bad request without time_id query param', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson('/api/projetos/'.$cenario['projetoA']->id.'/arquivos')
        ->assertStatus(400)
        ->assertJsonPath('message', 'O time_id é obrigatório.');
});

test('get arquivos lists only files of the project newest first', function (): void {
    $cenario = criarCenarioDoisTimes();

    $maisAntigo = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Antigo',
        'created_at' => now()->subHour(),
    ]);

    $maisNovo = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Novo',
        'created_at' => now(),
    ]);

    ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
        'nome' => 'Outro time',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->getJson(arquivosListUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'projeto_id',
                    'nome',
                    'descricao',
                    'usuario' => ['id', 'name'],
                    'created_at',
                    'updated_at',
                ],
            ],
        ])
        ->assertJsonMissingPath('data.0.src');

    $ids = collect($response->json('data'))->pluck('id')->all();

    expect($ids)->toBe([$maisNovo->id, $maisAntigo->id])
        ->and($ids)->not->toContain(
            ProjetoArquivo::query()->withoutGlobalScopes()->where('nome', 'Outro time')->value('id')
        );
});

test('get arquivos returns not found for project from another team', function (): void {
    $cenario = criarCenarioDoisTimes();

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(arquivosListUrl($cenario['timeA']->id, $cenario['projetoB']->id))
        ->assertNotFound();
});

test('post arquivos uploads file attributed to conta owner', function (): void {
    $cenario = criarCenarioDoisTimes();

    Storage::fake('local');

    Sanctum::actingAs($cenario['contaA']);

    $response = $this->post(
        '/api/projetos/'.$cenario['projetoA']->id.'/arquivos',
        payloadArquivo($cenario['timeA']->id),
        ['Accept' => 'application/json'],
    )
        ->assertCreated()
        ->assertJsonPath('data.nome', 'Contrato')
        ->assertJsonPath('data.descricao', 'Contrato assinado')
        ->assertJsonPath('data.projeto_id', $cenario['projetoA']->id)
        ->assertJsonPath('data.usuario.id', $cenario['userA']->id)
        ->assertJsonMissingPath('data.src');

    $arquivo = ProjetoArquivo::query()->withoutGlobalScopes()->find($response->json('data.id'));

    expect($arquivo)->not->toBeNull()
        ->and($arquivo->usuario_id)->toBe($cenario['userA']->id)
        ->and($arquivo->projeto_id)->toBe($cenario['projetoA']->id);

    $this->assertDatabaseHas('projetos_arquivos', [
        'id' => $arquivo->id,
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Contrato',
        'descricao' => 'Contrato assinado',
    ]);

    Storage::disk('local')->assertExists($arquivo->src);
});

test('post arquivos returns forbidden when time_id belongs to another conta', function (): void {
    $cenario = criarCenarioDoisTimes();

    Storage::fake('local');

    Sanctum::actingAs($cenario['contaA']);

    $this->post(
        '/api/projetos/'.$cenario['projetoB']->id.'/arquivos',
        payloadArquivo($cenario['timeB']->id),
        ['Accept' => 'application/json'],
    )
        ->assertForbidden()
        ->assertJsonPath('message', 'O time informado não pertence à conta autenticada.');
});

test('post arquivos returns validation error for invalid type or size', function (): void {
    $cenario = criarCenarioDoisTimes();

    Storage::fake('local');

    Sanctum::actingAs($cenario['contaA']);

    $this->post(
        '/api/projetos/'.$cenario['projetoA']->id.'/arquivos',
        payloadArquivo($cenario['timeA']->id, [
            'arquivo' => UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload'),
        ]),
        ['Accept' => 'application/json'],
    )->assertStatus(422);

    $this->post(
        '/api/projetos/'.$cenario['projetoA']->id.'/arquivos',
        payloadArquivo($cenario['timeA']->id, [
            'arquivo' => UploadedFile::fake()->create('grande.pdf', 11 * 1024, 'application/pdf'),
        ]),
        ['Accept' => 'application/json'],
    )->assertStatus(422);
});

test('delete arquivos soft deletes own file', function (): void {
    $cenario = criarCenarioDoisTimes();

    $arquivo = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Para excluir',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson(arquivoUrl($cenario['projetoA']->id, $arquivo->id), [
        'time_id' => $cenario['timeA']->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Arquivo excluído com sucesso.');

    $this->assertSoftDeleted('projetos_arquivos', [
        'id' => $arquivo->id,
    ]);

    $this->getJson(arquivosListUrl($cenario['timeA']->id, $cenario['projetoA']->id))
        ->assertOk()
        ->assertJsonMissing(['nome' => 'Para excluir']);
});

test('delete arquivos returns forbidden when file belongs to another user', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $arquivo = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $outroUser->id,
        'nome' => 'De outro membro',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->deleteJson(arquivoUrl($cenario['projetoA']->id, $arquivo->id), [
        'time_id' => $cenario['timeA']->id,
    ])->assertForbidden();

    $this->assertDatabaseHas('projetos_arquivos', [
        'id' => $arquivo->id,
        'deleted_at' => null,
    ]);
});

test('token of conta A cannot list or mutate files of conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    Storage::fake('local');

    $arquivoB = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
        'nome' => 'Secreto B',
    ]);

    Sanctum::actingAs($cenario['contaA']);

    $this->getJson(arquivosListUrl($cenario['timeB']->id, $cenario['projetoB']->id))
        ->assertForbidden();

    $this->post(
        '/api/projetos/'.$cenario['projetoB']->id.'/arquivos',
        payloadArquivo($cenario['timeB']->id),
        ['Accept' => 'application/json'],
    )->assertForbidden();

    $this->deleteJson(arquivoUrl($cenario['projetoB']->id, $arquivoB->id), [
        'time_id' => $cenario['timeB']->id,
    ])->assertForbidden();

    $this->assertDatabaseHas('projetos_arquivos', [
        'id' => $arquivoB->id,
        'deleted_at' => null,
    ]);
});
