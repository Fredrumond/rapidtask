<?php

use App\Domain\ClienteDomain;
use App\Exceptions\ClienteDomainException;

function clienteDomainNovo(array $overrides = []): ClienteDomain
{
    return ClienteDomain::criar(
        nome: $overrides['nome'] ?? 'Acme Ltda',
        usuarioId: $overrides['usuarioId'] ?? 1,
        timeId: $overrides['timeId'] ?? 2,
        email: $overrides['email'] ?? 'contato@acme.test',
        telefone: $overrides['telefone'] ?? '11999999999',
    );
}

test('criar cria com id null e nome trimado', function (): void {
    $domain = clienteDomainNovo(['nome' => '  Acme Ltda  ']);

    expect($domain->getId())->toBeNull()
        ->and($domain->getNome())->toBe('Acme Ltda')
        ->and($domain->getUsuarioId())->toBe(1)
        ->and($domain->getTimeId())->toBe(2)
        ->and($domain->getEmail())->toBe('contato@acme.test')
        ->and($domain->getTelefone())->toBe('11999999999');
});

test('criar normaliza email e telefone vazios para null', function (): void {
    $domain = clienteDomainNovo([
        'email' => '   ',
        'telefone' => '',
    ]);

    expect($domain->getEmail())->toBeNull()
        ->and($domain->getTelefone())->toBeNull();
});

test('criar lança nomeObrigatorio para nome vazio', function (): void {
    expect(fn () => clienteDomainNovo(['nome' => '   ']))
        ->toThrow(ClienteDomainException::class, 'O nome do cliente é obrigatório.');
});

test('criar lança nomeObrigatorio para string vazia', function (): void {
    expect(fn () => clienteDomainNovo(['nome' => '']))
        ->toThrow(ClienteDomainException::class, 'O nome do cliente é obrigatório.');
});

test('renomear atualiza o nome trimado', function (): void {
    $domain = clienteDomainNovo();
    $domain->renomear('  Nova Empresa  ');

    expect($domain->getNome())->toBe('Nova Empresa');
});

test('renomear lança nomeObrigatorio para nome vazio', function (): void {
    $domain = clienteDomainNovo();

    expect(fn () => $domain->renomear('   '))
        ->toThrow(ClienteDomainException::class, 'O nome do cliente é obrigatório.');
});

test('atualizarContato grava email e telefone trimados', function (): void {
    $domain = clienteDomainNovo();
    $domain->atualizarContato('  novo@acme.test  ', '  11888888888  ');

    expect($domain->getEmail())->toBe('novo@acme.test')
        ->and($domain->getTelefone())->toBe('11888888888');
});

test('atualizarContato limpa contato quando vazio', function (): void {
    $domain = clienteDomainNovo();
    $domain->atualizarContato('  ', null);

    expect($domain->getEmail())->toBeNull()
        ->and($domain->getTelefone())->toBeNull();
});

test('aplicarAtualizacao delega para renomear e atualizarContato', function (): void {
    $domain = clienteDomainNovo();
    $domain->aplicarAtualizacao([
        'nome' => '  Atualizada  ',
        'email' => '  outro@acme.test  ',
        'telefone' => '',
    ]);

    expect($domain->getNome())->toBe('Atualizada')
        ->and($domain->getEmail())->toBe('outro@acme.test')
        ->and($domain->getTelefone())->toBeNull();
});

test('aplicarAtualizacao com nome vazio lança nomeObrigatorio', function (): void {
    $domain = clienteDomainNovo();

    expect(fn () => $domain->aplicarAtualizacao(['nome' => '']))
        ->toThrow(ClienteDomainException::class, 'O nome do cliente é obrigatório.');
});

test('toPersistenceArray retorna chaves esperadas', function (): void {
    $domain = clienteDomainNovo([
        'nome' => 'Persistir',
        'usuarioId' => 10,
        'timeId' => 20,
        'email' => 'a@b.test',
        'telefone' => '11000000000',
    ]);

    expect($domain->toPersistenceArray())->toBe([
        'nome' => 'Persistir',
        'email' => 'a@b.test',
        'telefone' => '11000000000',
        'usuario_id' => 10,
        'time_id' => 20,
    ]);
});

test('reconstituir preserva id e contato', function (): void {
    $domain = ClienteDomain::reconstituir(
        id: 5,
        nome: 'Já persistido',
        usuarioId: 7,
        timeId: 3,
        email: 'ja@acme.test',
        telefone: '11777777777',
    );

    expect($domain->getId())->toBe(5)
        ->and($domain->getNome())->toBe('Já persistido')
        ->and($domain->getUsuarioId())->toBe(7)
        ->and($domain->getTimeId())->toBe(3)
        ->and($domain->getEmail())->toBe('ja@acme.test')
        ->and($domain->getTelefone())->toBe('11777777777');
});
