<?php

use App\Domain\ContaDomain;
use App\Exceptions\ContaDomainException;

function contaDomainNova(array $overrides = []): ContaDomain
{
    return ContaDomain::criar(
        nome: $overrides['nome'] ?? 'Conta de Alice',
        usuarioId: $overrides['usuarioId'] ?? 1,
    );
}

test('criar cria com id null e nome trimado', function (): void {
    $domain = contaDomainNova(['nome' => '  Conta de Alice  ']);

    expect($domain->getId())->toBeNull()
        ->and($domain->getNome())->toBe('Conta de Alice')
        ->and($domain->getUsuarioId())->toBe(1);
});

test('criar lança nomeObrigatorio para nome vazio', function (): void {
    expect(fn () => contaDomainNova(['nome' => '']))
        ->toThrow(ContaDomainException::class, 'O nome da conta é obrigatório.');
});

test('criar lança nomeObrigatorio para nome em branco', function (): void {
    expect(fn () => contaDomainNova(['nome' => '   ']))
        ->toThrow(ContaDomainException::class, 'O nome da conta é obrigatório.');
});

test('criar lança usuarioIdInvalido para id zero', function (): void {
    expect(fn () => contaDomainNova(['usuarioId' => 0]))
        ->toThrow(ContaDomainException::class, 'O proprietário da conta é inválido.');
});

test('criar lança usuarioIdInvalido para id negativo', function (): void {
    expect(fn () => contaDomainNova(['usuarioId' => -1]))
        ->toThrow(ContaDomainException::class, 'O proprietário da conta é inválido.');
});

test('renomear atualiza o nome trimado quando o ator é o owner', function (): void {
    $domain = contaDomainNova();
    $domain->renomear('  Conta Nova  ', 1);

    expect($domain->getNome())->toBe('Conta Nova');
});

test('renomear lança nomeObrigatorio para nome vazio', function (): void {
    $domain = contaDomainNova();

    expect(fn () => $domain->renomear('   ', 1))
        ->toThrow(ContaDomainException::class, 'O nome da conta é obrigatório.');
});

test('renomear lança apenasOwnerPodeRenomear quando o ator não é o owner', function (): void {
    $domain = contaDomainNova(['usuarioId' => 10]);

    expect(fn () => $domain->renomear('Outro nome', 99))
        ->toThrow(ContaDomainException::class, 'Apenas o proprietário pode alterar o nome da conta.');
});

test('toPersistenceArray retorna chaves esperadas', function (): void {
    $domain = contaDomainNova([
        'nome' => 'Acme',
        'usuarioId' => 7,
    ]);

    expect($domain->toPersistenceArray())->toBe([
        'nome' => 'Acme',
        'usuario_id' => 7,
    ]);
});

test('reconstituir preserva id e owner', function (): void {
    $domain = ContaDomain::reconstituir(
        id: 5,
        nome: 'Já persistida',
        usuarioId: 3,
    );

    expect($domain->getId())->toBe(5)
        ->and($domain->getNome())->toBe('Já persistida')
        ->and($domain->getUsuarioId())->toBe(3);
});
