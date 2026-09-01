<?php

use App\Models\ProjetoAnotacao;
use App\Models\User;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Livewire\Volt\Volt;

function sessaoTimeContaAnotacao(int $timeId, int $contaId): array
{
    return [
        CurrentTeam::SESSION_KEY => $timeId,
        CurrentTeam::CONTA_SESSION_KEY => $contaId,
    ];
}

test('user A nao ve anotacao B via scope', function (): void {
    $cenario = criarCenarioDoisTimes();

    $anotacaoA = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
    ]);

    $anotacaoB = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaAnotacao($cenario['timeA']->id, $cenario['contaA']->id));

    expect(ProjetoAnotacao::find($anotacaoA->id))->not->toBeNull()
        ->and(ProjetoAnotacao::find($anotacaoB->id))->toBeNull()
        ->and(ProjetoAnotacao::pluck('id')->all())->toBe([$anotacaoA->id]);
});

test('somente autor pode update e delete anotacao via policy', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $anotacaoPropria = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
    ]);

    $anotacaoAlheia = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaAnotacao($cenario['timeA']->id, $cenario['contaA']->id));

    expect(Gate::forUser($cenario['userA'])->allows('update', $anotacaoPropria))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->allows('delete', $anotacaoPropria))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->denies('update', $anotacaoAlheia))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->denies('delete', $anotacaoAlheia))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->allows('view', $anotacaoAlheia))->toBeTrue();
});

test('user A recebe 403 ou 404 ao acessar projeto B na show', function (): void {
    $cenario = criarCenarioDoisTimes();

    $response = $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaAnotacao($cenario['timeA']->id, $cenario['contaA']->id))
        ->get(route('projetos.show', $cenario['projetoB']));

    expect($response->status())->toBeIn([403, 404]);
});

test('conta A nao altera anotacao da conta B', function (): void {
    $cenario = criarCenarioDoisTimes();

    $anotacaoB = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoB']->id,
        'usuario_id' => $cenario['userB']->id,
        'anotacao' => 'Anotação da conta B',
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaAnotacao($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoB']])
        ->assertForbidden();

    expect(fn () => Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->call('iniciarEdicao', $anotacaoB->id)
    )->toThrow(ModelNotFoundException::class);

    expect(fn () => Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->call('excluirAnotacao', $anotacaoB->id)
    )->toThrow(ModelNotFoundException::class);

    $this->assertDatabaseHas('projetos_anotacoes', [
        'id' => $anotacaoB->id,
        'anotacao' => 'Anotação da conta B',
        'deleted_at' => null,
    ]);
});

test('pagina show permite criar editar e excluir anotacao propria', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaAnotacao($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->set('novaAnotacao', 'Primeira anotação')
        ->call('criarAnotacao')
        ->assertHasNoErrors();

    $anotacao = ProjetoAnotacao::query()
        ->where('projeto_id', $cenario['projetoA']->id)
        ->where('anotacao', 'Primeira anotação')
        ->first();

    expect($anotacao)->not->toBeNull()
        ->and($anotacao->usuario_id)->toBe($cenario['userA']->id);

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->call('iniciarEdicao', $anotacao->id)
        ->set('editandoTexto', 'Anotação editada')
        ->call('salvarEdicao')
        ->assertHasNoErrors();

    expect($anotacao->fresh()->anotacao)->toBe('Anotação editada');

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->call('excluirAnotacao', $anotacao->id)
        ->assertHasNoErrors();

    $this->assertSoftDeleted('projetos_anotacoes', [
        'id' => $anotacao->id,
    ]);

    expect(ProjetoAnotacao::query()->whereKey($anotacao->id)->exists())->toBeFalse()
        ->and(ProjetoAnotacao::withTrashed()->whereKey($anotacao->id)->exists())->toBeTrue();
});

test('pagina show impede editar anotacao de outro membro', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $anotacaoAlheia = ProjetoAnotacao::factory()->create([
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $outroUser->id,
        'anotacao' => 'Alheia',
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaAnotacao($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->call('iniciarEdicao', $anotacaoAlheia->id)
        ->assertForbidden();

    Volt::test('pages.projetos.show', ['projeto' => $cenario['projetoA']])
        ->call('excluirAnotacao', $anotacaoAlheia->id)
        ->assertForbidden();

    $this->assertDatabaseHas('projetos_anotacoes', [
        'id' => $anotacaoAlheia->id,
        'anotacao' => 'Alheia',
        'deleted_at' => null,
    ]);
});
