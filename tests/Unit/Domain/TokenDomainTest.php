<?php

use App\Domain\TokenDomain;
use App\Enums\TokenStatus;
use App\Exceptions\TokenDomainException;

function tokenDomainNovo(): TokenDomain
{
    return TokenDomain::criar();
}

test('criar cria token ativo sem texto plano', function (): void {
    $domain = tokenDomainNovo();

    expect($domain->isActive())->toBeTrue()
        ->and($domain->getStatusEnum())->toBe(TokenStatus::Ativo)
        ->and($domain->getPlainTextToken())->toBeNull()
        ->and($domain->getCreatedAt())->toBeNull()
        ->and($domain->getTokenName())->toBe('api');
});

test('inactive reconstitui token inativo', function (): void {
    $domain = TokenDomain::inactive();

    expect($domain->isActive())->toBeFalse()
        ->and($domain->getStatusEnum())->toBe(TokenStatus::Inativo)
        ->and($domain->getPlainTextToken())->toBeNull();
});

test('active reconstitui token ativo com createdAt', function (): void {
    $domain = TokenDomain::active('2026-08-19T12:00:00+00:00');

    expect($domain->isActive())->toBeTrue()
        ->and($domain->getCreatedAt())->toBe('2026-08-19T12:00:00+00:00')
        ->and($domain->getPlainTextToken())->toBeNull();
});

test('reconstituir preserva status, createdAt e texto plano', function (): void {
    $domain = TokenDomain::reconstituir(
        status: TokenStatus::Ativo,
        createdAt: '2026-01-01T10:00:00+00:00',
        plainTextToken: '1|secret',
    );

    expect($domain->isActive())->toBeTrue()
        ->and($domain->getCreatedAt())->toBe('2026-01-01T10:00:00+00:00')
        ->and($domain->getPlainTextToken())->toBe('1|secret');
});

test('reconstituir inativo com texto plano lança textoPlanoEmInativo', function (): void {
    expect(fn () => TokenDomain::reconstituir(
        status: TokenStatus::Inativo,
        plainTextToken: '1|secret',
    ))->toThrow(TokenDomainException::class, 'Não é possível anexar o texto a um token inativo.');
});

test('reconstituir ativo com texto plano vazio lança textoPlanoObrigatorio', function (): void {
    expect(fn () => TokenDomain::reconstituir(
        status: TokenStatus::Ativo,
        plainTextToken: '   ',
    ))->toThrow(TokenDomainException::class, 'O texto do token é obrigatório.');
});

test('anexarTextoPlano grava o texto trimado em token ativo', function (): void {
    $domain = tokenDomainNovo();
    $domain->anexarTextoPlano('  1|secret  ');

    expect($domain->isActive())->toBeTrue()
        ->and($domain->getPlainTextToken())->toBe('1|secret');
});

test('anexarTextoPlano lança textoPlanoObrigatorio para texto vazio', function (): void {
    $domain = tokenDomainNovo();

    expect(fn () => $domain->anexarTextoPlano('   '))
        ->toThrow(TokenDomainException::class, 'O texto do token é obrigatório.');
});

test('anexarTextoPlano lança textoPlanoEmInativo em token inativo', function (): void {
    $domain = TokenDomain::inactive();

    expect(fn () => $domain->anexarTextoPlano('1|secret'))
        ->toThrow(TokenDomainException::class, 'Não é possível anexar o texto a um token inativo.');
});

test('revogar inativa e limpa o texto plano', function (): void {
    $domain = TokenDomain::reconstituir(
        status: TokenStatus::Ativo,
        createdAt: '2026-01-01T10:00:00+00:00',
        plainTextToken: '1|secret',
    );

    $domain->revogar();

    expect($domain->isActive())->toBeFalse()
        ->and($domain->getStatusEnum())->toBe(TokenStatus::Inativo)
        ->and($domain->getPlainTextToken())->toBeNull()
        ->and($domain->getCreatedAt())->toBe('2026-01-01T10:00:00+00:00');
});

test('revogar lança jaRevogado se já estiver inativo', function (): void {
    $domain = TokenDomain::inactive();

    expect(fn () => $domain->revogar())
        ->toThrow(TokenDomainException::class, 'O token de API já está revogado.');
});

test('toPersistenceArray retorna o nome canônico', function (): void {
    expect(tokenDomainNovo()->toPersistenceArray())->toBe([
        'name' => 'api',
    ]);
});
