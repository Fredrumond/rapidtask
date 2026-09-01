<?php

use App\Models\ProjetoArquivo;
use App\Models\User;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

function sessaoTimeContaArquivo(int $timeId, int $contaId): array
{
    return [
        CurrentTeam::SESSION_KEY => $timeId,
        CurrentTeam::CONTA_SESSION_KEY => $contaId,
    ];
}

test('conta A nao lista arquivo da conta B na show', function (): void {
    $cenario = criarCenarioDoisTimes();

    $arquivoA = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Arquivo da conta A',
    ]);

    $arquivoB = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
        'nome' => 'Arquivo da conta B',
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaArquivo($cenario['timeA']->id, $cenario['contaA']->id));

    expect(ProjetoArquivo::find($arquivoA->id))->not->toBeNull()
        ->and(ProjetoArquivo::find($arquivoB->id))->toBeNull()
        ->and(ProjetoArquivo::pluck('id')->all())->toBe([$arquivoA->id]);

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->assertSee('Arquivo da conta A')
        ->assertDontSee('Arquivo da conta B');

    $response = $this->get(route('projetos.show', $cenario['projetoB']));

    expect($response->status())->toBeIn([403, 404]);
});

test('conta A nao baixa e nao exclui arquivo da conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    Storage::fake('local');

    $arquivoB = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
        'nome' => 'Secreto B',
        'src' => 'projetos/b/secreto.pdf',
    ]);

    Storage::disk('local')->put($arquivoB->src, 'conteudo-b');

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaArquivo($cenario['timeA']->id, $cenario['contaA']->id));

    expect(Gate::forUser($cenario['userA'])->denies('view', $arquivoB))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->denies('delete', $arquivoB))->toBeTrue();

    $download = $this->get(route('arquivos.download', $arquivoB));

    expect($download->status())->toBeIn([403, 404]);

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoB']])
        ->assertForbidden();

    expect(fn () => Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->call('excluirArquivo', $arquivoB->id))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);

    $this->assertDatabaseHas('projetos_arquivos', [
        'id' => $arquivoB->id,
        'deleted_at' => null,
    ]);
});

test('membro do time que nao e dono nao exclui arquivo', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $arquivo = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Do dono',
    ]);

    $this->actingAs($outroUser)
        ->withSession(sessaoTimeContaArquivo($cenario['timeA']->id, $cenario['contaA']->id));

    expect(Gate::forUser($outroUser)->allows('view', $arquivo))->toBeTrue()
        ->and(Gate::forUser($outroUser)->denies('delete', $arquivo))->toBeTrue();

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->assertSee('Do dono')
        ->assertDontSee('Excluir')
        ->call('excluirArquivo', $arquivo->id)
        ->assertForbidden();

    $this->assertDatabaseHas('projetos_arquivos', [
        'id' => $arquivo->id,
        'deleted_at' => null,
    ]);
});

test('dono exclui arquivo com soft delete e some da lista', function (): void {
    $cenario = criarCenarioDoisTimes();

    $arquivo = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Para excluir',
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaArquivo($cenario['timeA']->id, $cenario['contaA']->id));

    expect(Gate::forUser($cenario['userA'])->allows('delete', $arquivo))->toBeTrue();

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->assertSee('Para excluir')
        ->assertSee('Excluir')
        ->call('excluirArquivo', $arquivo->id)
        ->assertHasNoErrors();

    $this->assertSoftDeleted('projetos_arquivos', [
        'id' => $arquivo->id,
    ]);

    expect(ProjetoArquivo::find($arquivo->id))->toBeNull()
        ->and(ProjetoArquivo::withTrashed()->find($arquivo->id))->not->toBeNull();

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->assertDontSee('Para excluir');
});

test('upload valido aparece na show e tipo ou tamanho invalido falha', function (): void {
    $cenario = criarCenarioDoisTimes();

    Storage::fake('local');

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaArquivo($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->set('arquivoNome', 'Contrato')
        ->set('arquivoDescricao', 'Contrato assinado')
        ->set('arquivo', UploadedFile::fake()->create('contrato.pdf', 200, 'application/pdf'))
        ->call('enviarArquivo')
        ->assertHasNoErrors();

    $arquivo = ProjetoArquivo::query()
        ->where('projeto_id', $cenario['projetoA']->id)
        ->where('nome', 'Contrato')
        ->first();

    expect($arquivo)->not->toBeNull()
        ->and($arquivo->descricao)->toBe('Contrato assinado')
        ->and($arquivo->usuario_id)->toBe($cenario['userA']->id)
        ->and($arquivo->projeto_id)->toBe($cenario['projetoA']->id);

    Storage::disk('local')->assertExists($arquivo->src);

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->assertSee('Contrato')
        ->assertSee('Contrato assinado');

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->set('arquivoNome', 'Invalido')
        ->set('arquivoDescricao', 'Tipo errado')
        ->set('arquivo', UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload'))
        ->call('enviarArquivo')
        ->assertHasErrors(['arquivo']);

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->set('arquivoNome', 'Grande')
        ->set('arquivoDescricao', 'Acima do limite')
        ->set('arquivo', UploadedFile::fake()->create('grande.pdf', 11 * 1024, 'application/pdf'))
        ->call('enviarArquivo')
        ->assertHasErrors(['arquivo']);
});

test('download do proprio time funciona e arquivo soft-deleted nao baixa', function (): void {
    $cenario = criarCenarioDoisTimes();

    Storage::fake('local');

    $arquivo = ProjetoArquivo::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'nome' => 'Relatorio.pdf',
        'src' => 'projetos/a/relatorio.pdf',
    ]);

    Storage::disk('local')->put($arquivo->src, 'conteudo-relatorio');

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaArquivo($cenario['timeA']->id, $cenario['contaA']->id));

    $this->get(route('arquivos.download', $arquivo))
        ->assertOk();

    $arquivo->delete();

    $this->get(route('arquivos.download', $arquivo->id))
        ->assertNotFound();
});
