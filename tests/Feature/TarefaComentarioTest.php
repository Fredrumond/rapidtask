<?php

use App\Models\TarefaComentario;
use App\Models\User;
use App\Support\CurrentTeam;
use Database\Factories\TimeMembroFactory;
use Illuminate\Support\Facades\Gate;
use Livewire\Volt\Volt;

function sessaoTimeContaComentario(int $timeId, int $contaId): array
{
    return [
        CurrentTeam::SESSION_KEY => $timeId,
        CurrentTeam::CONTA_SESSION_KEY => $contaId,
    ];
}

test('user A nao ve comentario B via scope', function (): void {
    $cenario = criarCenarioDoisTimes();

    $comentarioA = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $cenario['userA']->id,
    ]);

    $comentarioB = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaB']->id,
        'usuario_id' => $cenario['userB']->id,
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaComentario($cenario['timeA']->id, $cenario['contaA']->id));

    expect(TarefaComentario::find($comentarioA->id))->not->toBeNull()
        ->and(TarefaComentario::find($comentarioB->id))->toBeNull()
        ->and(TarefaComentario::pluck('id')->all())->toBe([$comentarioA->id]);
});

test('somente autor pode update e delete comentario via policy', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $comentarioProprio = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $cenario['userA']->id,
    ]);

    $comentarioAlheio = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaComentario($cenario['timeA']->id, $cenario['contaA']->id));

    expect(Gate::forUser($cenario['userA'])->allows('update', $comentarioProprio))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->allows('delete', $comentarioProprio))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->denies('update', $comentarioAlheio))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->denies('delete', $comentarioAlheio))->toBeTrue()
        ->and(Gate::forUser($cenario['userA'])->allows('view', $comentarioAlheio))->toBeTrue();
});

test('user A recebe 403 ou 404 ao acessar tarefa B na show', function (): void {
    $cenario = criarCenarioDoisTimes();

    $response = $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaComentario($cenario['timeA']->id, $cenario['contaA']->id))
        ->get(route('tarefas.show', $cenario['tarefaB']));

    expect($response->status())->toBeIn([403, 404]);
});

test('pagina show permite criar editar e excluir comentario proprio', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaComentario($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.tarefas.show', ['tarefa' => $cenario['tarefaA']])
        ->set('novoComentario', 'Primeiro comentário')
        ->call('criarComentario')
        ->assertHasNoErrors();

    $comentario = TarefaComentario::query()
        ->where('tarefa_id', $cenario['tarefaA']->id)
        ->where('comentario', 'Primeiro comentário')
        ->first();

    expect($comentario)->not->toBeNull()
        ->and($comentario->usuario_id)->toBe($cenario['userA']->id);

    Volt::test('pages.tarefas.show', ['tarefa' => $cenario['tarefaA']])
        ->call('iniciarEdicao', $comentario->id)
        ->set('editandoTexto', 'Comentário editado')
        ->call('salvarEdicao')
        ->assertHasNoErrors();

    expect($comentario->fresh()->comentario)->toBe('Comentário editado');

    Volt::test('pages.tarefas.show', ['tarefa' => $cenario['tarefaA']])
        ->call('excluirComentario', $comentario->id)
        ->assertHasNoErrors();

    $this->assertSoftDeleted('tarefa_comentario', [
        'id' => $comentario->id,
    ]);
});

test('pagina show impede editar comentario de outro membro', function (): void {
    $cenario = criarCenarioDoisTimes();

    $outroUser = User::factory()->create();
    TimeMembroFactory::new()->create([
        'time_id' => $cenario['timeA']->id,
        'usuario_id' => $outroUser->id,
    ]);

    $comentarioAlheio = TarefaComentario::factory()->create([
        'tarefa_id' => $cenario['tarefaA']->id,
        'usuario_id' => $outroUser->id,
        'comentario' => 'Alheio',
    ]);

    $this->actingAs($cenario['userA'])
        ->withSession(sessaoTimeContaComentario($cenario['timeA']->id, $cenario['contaA']->id));

    Volt::test('pages.tarefas.show', ['tarefa' => $cenario['tarefaA']])
        ->call('iniciarEdicao', $comentarioAlheio->id)
        ->assertForbidden();

    Volt::test('pages.tarefas.show', ['tarefa' => $cenario['tarefaA']])
        ->call('excluirComentario', $comentarioAlheio->id)
        ->assertForbidden();

    $this->assertDatabaseHas('tarefa_comentario', [
        'id' => $comentarioAlheio->id,
        'comentario' => 'Alheio',
        'deleted_at' => null,
    ]);
});
