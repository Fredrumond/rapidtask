<?php

use App\Domain\ProjetoDomain;
use App\Exceptions\ProjetoDomainException;

function projetoDomainNovo(array $overrides = []): ProjetoDomain
{
    return ProjetoDomain::criar(
        nome: $overrides['nome'] ?? 'Portal do Cliente',
        sigla: $overrides['sigla'] ?? 'PDC',
        clienteId: $overrides['clienteId'] ?? 1,
        usuarioId: $overrides['usuarioId'] ?? 1,
        timeId: $overrides['timeId'] ?? 2,
        descricao: $overrides['descricao'] ?? 'Descrição inicial',
        dtInicio: $overrides['dtInicio'] ?? null,
        dtPrevista: $overrides['dtPrevista'] ?? null,
        dtFim: $overrides['dtFim'] ?? null,
    );
}

test('criar rejeita nome vazio', function (): void {
    expect(fn () => projetoDomainNovo(['nome' => '']))
        ->toThrow(ProjetoDomainException::class, 'O nome do projeto é obrigatório.');
});

test('criar rejeita nome em branco', function (): void {
    expect(fn () => projetoDomainNovo(['nome' => '   ']))
        ->toThrow(ProjetoDomainException::class, 'O nome do projeto é obrigatório.');
});

test('criar rejeita sigla vazia', function (): void {
    expect(fn () => projetoDomainNovo(['sigla' => '']))
        ->toThrow(ProjetoDomainException::class, 'A sigla do projeto é obrigatória.');
});

test('criar rejeita sigla em branco', function (): void {
    expect(fn () => projetoDomainNovo(['sigla' => '   ']))
        ->toThrow(ProjetoDomainException::class, 'A sigla do projeto é obrigatória.');
});

test('criar persiste datas nulas', function (): void {
    $domain = projetoDomainNovo();

    expect($domain->getId())->toBeNull()
        ->and($domain->getDtInicio())->toBeNull()
        ->and($domain->getDtPrevista())->toBeNull()
        ->and($domain->getDtFim())->toBeNull();
});

test('aplicarAtualizacao altera nome sigla cliente e datas', function (): void {
    $domain = projetoDomainNovo();

    $domain->aplicarAtualizacao([
        'nome' => '  Nome atualizado  ',
        'sigla' => '  NA  ',
        'descricao' => 'Nova descrição',
        'cliente_id' => 9,
        'dt_inicio' => '2026-09-01',
        'dt_prevista' => '2026-09-15',
        'dt_fim' => '2026-09-30',
    ]);

    expect($domain->getNome())->toBe('Nome atualizado')
        ->and($domain->getSigla())->toBe('NA')
        ->and($domain->getDescricao())->toBe('Nova descrição')
        ->and($domain->getClienteId())->toBe(9)
        ->and($domain->getDtInicio())->toBe('2026-09-01')
        ->and($domain->getDtPrevista())->toBe('2026-09-15')
        ->and($domain->getDtFim())->toBe('2026-09-30')
        ->and($domain->getUsuarioId())->toBe(1)
        ->and($domain->getTimeId())->toBe(2);
});
