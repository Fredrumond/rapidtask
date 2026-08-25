<?php

use App\Domain\TimeDomain;
use App\Exceptions\TimeDomainException;

function timeDomainNovo(array $overrides = []): TimeDomain
{
    return TimeDomain::criar(
        nome: $overrides['nome'] ?? 'Time Alpha',
        contaId: $overrides['contaId'] ?? 1,
        criadorId: $overrides['criadorId'] ?? 10,
    );
}

test('criar cria com id null, nome trimado e criador como admin', function (): void {
    $domain = timeDomainNovo(['nome' => '  Time Alpha  ']);

    expect($domain->getId())->toBeNull()
        ->and($domain->getNome())->toBe('Time Alpha')
        ->and($domain->getContaId())->toBe(1)
        ->and($domain->getCriadorId())->toBe(10)
        ->and($domain->isAtorAdmin())->toBeTrue()
        ->and($domain->foiExcluido())->toBeFalse();
});

test('criar lança nomeObrigatorio para nome vazio', function (): void {
    expect(fn () => timeDomainNovo(['nome' => '']))
        ->toThrow(TimeDomainException::class, 'O nome do time é obrigatório.');
});

test('criar lança nomeObrigatorio para nome em branco', function (): void {
    expect(fn () => timeDomainNovo(['nome' => '   ']))
        ->toThrow(TimeDomainException::class, 'O nome do time é obrigatório.');
});

test('criar lança contaIdInvalido para id zero', function (): void {
    expect(fn () => timeDomainNovo(['contaId' => 0]))
        ->toThrow(TimeDomainException::class, 'A conta do time é inválida.');
});

test('criar lança criadorIdInvalido para id zero', function (): void {
    expect(fn () => timeDomainNovo(['criadorId' => 0]))
        ->toThrow(TimeDomainException::class, 'O criador do time é inválido.');
});

test('excluir marca o time quando o ator é admin', function (): void {
    $domain = timeDomainNovo();
    $domain->excluir();

    expect($domain->foiExcluido())->toBeTrue();
});

test('excluir lança apenasAdminPodeExcluir quando o ator não é admin', function (): void {
    $domain = TimeDomain::reconstituir(
        id: 3,
        nome: 'Time Alpha',
        contaId: 1,
        criadorId: 10,
        atorEhAdmin: false,
    );

    expect(fn () => $domain->excluir())
        ->toThrow(TimeDomainException::class, 'Apenas um administrador do time pode excluí-lo.');
});

test('toPersistenceArray retorna chaves esperadas', function (): void {
    $domain = timeDomainNovo([
        'nome' => 'Beta',
        'contaId' => 4,
        'criadorId' => 8,
    ]);

    expect($domain->toPersistenceArray())->toBe([
        'nome' => 'Beta',
        'usuario_id' => 8,
        'conta_id' => 4,
    ]);
});

test('reconstituir preserva id, conta e flag de admin', function (): void {
    $domain = TimeDomain::reconstituir(
        id: 9,
        nome: 'Já persistido',
        contaId: 2,
        criadorId: 5,
        atorEhAdmin: true,
    );

    expect($domain->getId())->toBe(9)
        ->and($domain->getNome())->toBe('Já persistido')
        ->and($domain->getContaId())->toBe(2)
        ->and($domain->getCriadorId())->toBe(5)
        ->and($domain->isAtorAdmin())->toBeTrue()
        ->and($domain->foiExcluido())->toBeFalse();
});
