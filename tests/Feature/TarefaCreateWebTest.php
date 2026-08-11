<?php

use App\Models\Tarefa;
use App\Support\CurrentTeam;
use Livewire\Volt\Volt;

test('pagina create salva tarefa via service', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession([
            CurrentTeam::SESSION_KEY => $cenario['timeA']->id,
            CurrentTeam::CONTA_SESSION_KEY => $cenario['contaA']->id,
        ]);

    Volt::test('pages.tarefas.create')
        ->set('titulo', 'Criada pela web')
        ->set('descricao', 'Desc')
        ->set('projeto_id', $cenario['projetoA']->id)
        ->set('tipo_id', 1)
        ->set('situacao_id', 1)
        ->set('prioridade_id', 1)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('tarefas.index'));

    $this->assertDatabaseHas('tarefas', [
        'titulo' => 'Criada pela web',
        'projeto_id' => $cenario['projetoA']->id,
        'usuario_id' => $cenario['userA']->id,
        'status' => 0,
    ]);
});

test('pagina create sem projeto_id mostra erro', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession([
            CurrentTeam::SESSION_KEY => $cenario['timeA']->id,
            CurrentTeam::CONTA_SESSION_KEY => $cenario['contaA']->id,
        ]);

    Volt::test('pages.tarefas.create')
        ->set('titulo', 'Sem projeto')
        ->set('projeto_id', null)
        ->call('save')
        ->assertHasErrors(['projeto_id']);
});

test('pagina create com campos vazios de data e tempo salva', function (): void {
    $cenario = criarCenarioDoisTimes();

    $this->actingAs($cenario['userA'])
        ->withSession([
            CurrentTeam::SESSION_KEY => $cenario['timeA']->id,
            CurrentTeam::CONTA_SESSION_KEY => $cenario['contaA']->id,
        ]);

    Volt::test('pages.tarefas.create')
        ->set('titulo', 'Com vazios')
        ->set('descricao', '')
        ->set('projeto_id', $cenario['projetoA']->id)
        ->set('tipo_id', 1)
        ->set('situacao_id', 1)
        ->set('prioridade_id', 1)
        ->set('dt_inicio', '')
        ->set('dt_prevista', '')
        ->set('tempo_estimado', '')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('tarefas.index'));

    expect(Tarefa::query()->where('titulo', 'Com vazios')->exists())->toBeTrue();
});
