<?php

use App\Domain\TarefaComentarioDomain;
use App\Exceptions\TarefaComentarioDomainException;

function comentarioDomainNovo(array $overrides = []): TarefaComentarioDomain
{
    return TarefaComentarioDomain::criar(
        tarefaId: $overrides['tarefaId'] ?? 1,
        usuarioId: $overrides['usuarioId'] ?? 1,
        comentario: $overrides['comentario'] ?? 'Comentário de teste',
    );
}

test('criar cria com id null e texto trimado', function (): void {
    $domain = comentarioDomainNovo(['comentario' => '  Olá mundo  ']);

    expect($domain->getId())->toBeNull()
        ->and($domain->getTarefaId())->toBe(1)
        ->and($domain->getUsuarioId())->toBe(1)
        ->and($domain->getComentario())->toBe('Olá mundo');
});

test('criar lança comentarioObrigatorio para texto vazio', function (): void {
    expect(fn () => comentarioDomainNovo(['comentario' => '   ']))
        ->toThrow(TarefaComentarioDomainException::class, 'O comentário é obrigatório.');
});

test('criar lança comentarioObrigatorio para string vazia', function (): void {
    expect(fn () => comentarioDomainNovo(['comentario' => '']))
        ->toThrow(TarefaComentarioDomainException::class, 'O comentário é obrigatório.');
});

test('editarTexto atualiza o texto trimado', function (): void {
    $domain = comentarioDomainNovo();
    $domain->editarTexto('  Texto editado  ');

    expect($domain->getComentario())->toBe('Texto editado');
});

test('editarTexto lança comentarioObrigatorio para texto vazio', function (): void {
    $domain = comentarioDomainNovo();

    expect(fn () => $domain->editarTexto('   '))
        ->toThrow(TarefaComentarioDomainException::class, 'O comentário é obrigatório.');
});

test('toPersistenceArray retorna chaves esperadas', function (): void {
    $domain = comentarioDomainNovo([
        'tarefaId' => 10,
        'usuarioId' => 20,
        'comentario' => 'Persistir',
    ]);

    expect($domain->toPersistenceArray())->toBe([
        'tarefa_id' => 10,
        'usuario_id' => 20,
        'comentario' => 'Persistir',
    ]);
});

test('reconstituir preserva id e lookups', function (): void {
    $domain = TarefaComentarioDomain::reconstituir(
        id: 5,
        tarefaId: 3,
        usuarioId: 7,
        comentario: 'Já persistido',
        usuarioLookup: ['id' => 7, 'name' => 'Ana'],
        createdAt: '2026-01-01T10:00:00+00:00',
        updatedAt: '2026-01-02T10:00:00+00:00',
    );

    expect($domain->getId())->toBe(5)
        ->and($domain->getTarefaId())->toBe(3)
        ->and($domain->getUsuarioId())->toBe(7)
        ->and($domain->getComentario())->toBe('Já persistido')
        ->and($domain->getUsuario())->toBe(['id' => 7, 'name' => 'Ana'])
        ->and($domain->getCreatedAt())->toBe('2026-01-01T10:00:00+00:00')
        ->and($domain->getUpdatedAt())->toBe('2026-01-02T10:00:00+00:00');
});
