<?php

use App\Domain\TarefaDomain;
use App\Enums\TarefaSituacao;
use App\Enums\TarefaStatus;
use App\Exceptions\TarefaDomainException;

function tarefaDomainNova(array $overrides = []): TarefaDomain
{
    return TarefaDomain::criar(
        titulo: $overrides['titulo'] ?? 'Tarefa de teste',
        projetoId: $overrides['projetoId'] ?? 1,
        usuarioId: $overrides['usuarioId'] ?? 1,
        tipoId: $overrides['tipoId'] ?? 1,
        prioridadeId: $overrides['prioridadeId'] ?? 1,
        situacao: $overrides['situacao'] ?? TarefaSituacao::Novo,
        descricao: $overrides['descricao'] ?? null,
        dtInicio: $overrides['dtInicio'] ?? null,
        dtPrevista: $overrides['dtPrevista'] ?? null,
        dtFim: $overrides['dtFim'] ?? null,
        tempoEstimado: $overrides['tempoEstimado'] ?? null,
    );
}

test('criar cria com status Ativa e situacao Novo por padrão', function (): void {
    $domain = tarefaDomainNova();

    expect($domain->getStatusEnum())->toBe(TarefaStatus::Ativa)
        ->and($domain->getSituacaoEnum())->toBe(TarefaSituacao::Novo)
        ->and($domain->isAtiva())->toBeTrue()
        ->and($domain->getId())->toBeNull();
});

test('criar lança tituloObrigatorio para título vazio', function (): void {
    expect(fn () => tarefaDomainNova(['titulo' => '   ']))
        ->toThrow(TarefaDomainException::class, 'O título da tarefa é obrigatório.');
});

test('arquivar muda status para Arquivada', function (): void {
    $domain = tarefaDomainNova();
    $domain->arquivar();

    expect($domain->isArquivada())->toBeTrue()
        ->and($domain->getStatus())->toBe(TarefaStatus::Arquivada->value);
});

test('arquivar lança jaArquivada se já estiver arquivada', function (): void {
    $domain = tarefaDomainNova();
    $domain->arquivar();

    expect(fn () => $domain->arquivar())
        ->toThrow(TarefaDomainException::class, 'A tarefa já está arquivada.');
});

test('recuperar muda status para Ativa', function (): void {
    $domain = tarefaDomainNova();
    $domain->arquivar();
    $domain->recuperar();

    expect($domain->isAtiva())->toBeTrue();
});

test('recuperar lança naoArquivada se não estiver arquivada', function (): void {
    $domain = tarefaDomainNova();

    expect(fn () => $domain->recuperar())
        ->toThrow(TarefaDomainException::class, 'A tarefa não está arquivada.');
});

test('renomear lança operacaoEmArquivada em tarefa arquivada', function (): void {
    $domain = tarefaDomainNova();
    $domain->arquivar();

    expect(fn () => $domain->renomear('Novo título'))
        ->toThrow(TarefaDomainException::class, 'Não é possível renomear uma tarefa arquivada.');
});

test('reagendar lança datasInvalidas se dt_inicio for depois da prevista', function (): void {
    $domain = tarefaDomainNova();

    expect(fn () => $domain->reagendar(
        new DateTimeImmutable('2026-08-20'),
        new DateTimeImmutable('2026-08-10'),
    ))->toThrow(TarefaDomainException::class, 'Datas inválidas: início depois da prevista');
});

test('finalizar muda situacao para Finalizado e preenche dt_fim com hoje se nulo', function (): void {
    $domain = tarefaDomainNova();
    $domain->finalizar();

    expect($domain->isFinalizada())->toBeTrue()
        ->and($domain->getSituacaoEnum())->toBe(TarefaSituacao::Finalizado)
        ->and($domain->getDtFim())->toBe((new DateTimeImmutable('today'))->format('Y-m-d'));
});

test('finalizar lança operacaoEmArquivada se arquivada', function (): void {
    $domain = tarefaDomainNova();
    $domain->arquivar();

    expect(fn () => $domain->finalizar())
        ->toThrow(TarefaDomainException::class, 'Não é possível mudar situação uma tarefa arquivada.');
});

test('mudarSituacao permite transição válida de Novo para Andamento', function (): void {
    $domain = tarefaDomainNova();
    $domain->mudarSituacao(TarefaSituacao::Andamento);

    expect($domain->getSituacaoEnum())->toBe(TarefaSituacao::Andamento);
});

test('mudarSituacao lança situacaoInvalida de Finalizado para qualquer outra', function (): void {
    $domain = tarefaDomainNova();
    $domain->finalizar();

    expect(fn () => $domain->mudarSituacao(TarefaSituacao::Andamento))
        ->toThrow(TarefaDomainException::class, 'Transição de situação não permitida.');
});

test('isAtrasada é true quando ativa, não finalizada e dt_prevista anterior a hoje', function (): void {
    $domain = tarefaDomainNova([
        'dtPrevista' => new DateTimeImmutable('2020-01-01'),
    ]);

    expect($domain->isAtrasada(new DateTimeImmutable('2026-08-11')))->toBeTrue();
});

test('isAtrasada é false quando arquivada mesmo com data vencida', function (): void {
    $domain = tarefaDomainNova([
        'dtPrevista' => new DateTimeImmutable('2020-01-01'),
    ]);
    $domain->arquivar();

    expect($domain->isAtrasada(new DateTimeImmutable('2026-08-11')))->toBeFalse();
});

test('toPersistenceArray retorna campos corretos para gravar no banco', function (): void {
    $domain = tarefaDomainNova([
        'titulo' => 'Persistir',
        'projetoId' => 10,
        'usuarioId' => 20,
        'tipoId' => 2,
        'prioridadeId' => 3,
        'descricao' => 'Desc',
        'dtInicio' => new DateTimeImmutable('2026-08-01'),
        'dtPrevista' => new DateTimeImmutable('2026-08-15'),
        'tempoEstimado' => 60,
    ]);

    expect($domain->toPersistenceArray())->toBe([
        'titulo' => 'Persistir',
        'descricao' => 'Desc',
        'projeto_id' => 10,
        'usuario_id' => 20,
        'tipo_id' => 2,
        'prioridade_id' => 3,
        'situacao_id' => TarefaSituacao::Novo->value,
        'status' => TarefaStatus::Ativa->value,
        'dt_inicio' => '2026-08-01',
        'dt_prevista' => '2026-08-15',
        'dt_fim' => null,
        'tempo_estimado' => 60,
    ]);
});
