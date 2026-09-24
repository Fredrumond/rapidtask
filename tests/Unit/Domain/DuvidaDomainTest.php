<?php

use App\Domain\DuvidaDomain;
use App\Exceptions\DuvidaDomainException;

function duvidaDomainNova(array $overrides = []): DuvidaDomain
{
    return DuvidaDomain::criar(
        nome: $overrides['nome'] ?? 'Marina Costa',
        email: $overrides['email'] ?? 'marina@example.com',
        telefone: $overrides['telefone'] ?? '11999999999',
        duvida: $overrides['duvida'] ?? 'Como acompanho um projeto?',
    );
}

test('criar normaliza espaços e deixa o e-mail em minúsculas', function (): void {
    $domain = duvidaDomainNova([
        'nome' => '  Marina Costa  ',
        'email' => '  Marina@Example.com  ',
        'telefone' => '  11999999999  ',
        'duvida' => '  Como acompanho um projeto?  ',
    ]);

    expect($domain->getId())->toBeNull()
        ->and($domain->getNome())->toBe('Marina Costa')
        ->and($domain->getEmail())->toBe('marina@example.com')
        ->and($domain->getTelefone())->toBe('11999999999')
        ->and($domain->getDuvida())->toBe('Como acompanho um projeto?');
});

test('criar rejeita nome vazio', function (): void {
    expect(fn () => duvidaDomainNova(['nome' => '   ']))
        ->toThrow(DuvidaDomainException::class, 'O nome é obrigatório.');
});

test('criar rejeita e-mail vazio', function (): void {
    expect(fn () => duvidaDomainNova(['email' => '   ']))
        ->toThrow(DuvidaDomainException::class, 'O e-mail é obrigatório.');
});

test('criar rejeita e-mail inválido', function (): void {
    expect(fn () => duvidaDomainNova(['email' => 'nao-e-email']))
        ->toThrow(DuvidaDomainException::class, 'Informe um e-mail válido.');
});

test('criar rejeita telefone vazio', function (): void {
    expect(fn () => duvidaDomainNova(['telefone' => '   ']))
        ->toThrow(DuvidaDomainException::class, 'O telefone é obrigatório.');
});

test('criar rejeita dúvida vazia', function (): void {
    expect(fn () => duvidaDomainNova(['duvida' => '   ']))
        ->toThrow(DuvidaDomainException::class, 'A dúvida é obrigatória.');
});
