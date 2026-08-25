<?php

use App\Domain\ConviteDomain;
use App\Exceptions\ConviteDomainException;

function conviteDomainNovo(array $overrides = []): ConviteDomain
{
    return ConviteDomain::emitir(
        nome: $overrides['nome'] ?? 'Maria Silva',
        email: $overrides['email'] ?? 'maria@example.com',
        token: $overrides['token'] ?? 'token-de-teste',
        timeId: $overrides['timeId'] ?? 1,
        emailJaPertenceAOutraConta: $overrides['emailJaPertenceAOutraConta'] ?? false,
    );
}

function conviteDomainPendente(array $overrides = []): ConviteDomain
{
    return ConviteDomain::reconstituir(
        id: $overrides['id'] ?? 10,
        nome: $overrides['nome'] ?? 'Maria Silva',
        email: $overrides['email'] ?? 'maria@example.com',
        timeId: $overrides['timeId'] ?? 1,
        token: $overrides['token'] ?? 'token-persistido',
        status: $overrides['status'] ?? ConviteDomain::STATUS_PENDENTE,
        contaId: $overrides['contaId'] ?? 5,
    );
}

test('emitir cria convite pendente com id null e dados trimados', function (): void {
    $domain = conviteDomainNovo([
        'nome' => '  Maria Silva  ',
        'email' => '  maria@example.com  ',
    ]);

    expect($domain->getId())->toBeNull()
        ->and($domain->getNome())->toBe('Maria Silva')
        ->and($domain->getEmail())->toBe('maria@example.com')
        ->and($domain->getTimeId())->toBe(1)
        ->and($domain->getStatus())->toBe(ConviteDomain::STATUS_PENDENTE)
        ->and($domain->isPendente())->toBeTrue();
});

test('emitir lança emailJaPertenceAOutraConta quando o e-mail já pertence a outra conta', function (): void {
    expect(fn () => conviteDomainNovo(['emailJaPertenceAOutraConta' => true]))
        ->toThrow(ConviteDomainException::class, 'Este e-mail já pertence a outra conta na plataforma.');
});

test('emitir lança nomeObrigatorio para nome vazio', function (): void {
    expect(fn () => conviteDomainNovo(['nome' => '']))
        ->toThrow(ConviteDomainException::class, 'O nome do convidado é obrigatório.');
});

test('emitir lança emailObrigatorio para e-mail em branco', function (): void {
    expect(fn () => conviteDomainNovo(['email' => '   ']))
        ->toThrow(ConviteDomainException::class, 'O e-mail do convidado é obrigatório.');
});

test('emitir lança tokenObrigatorio para token vazio', function (): void {
    expect(fn () => conviteDomainNovo(['token' => '']))
        ->toThrow(ConviteDomainException::class, 'O token do convite é obrigatório.');
});

test('emitir lança timeIdInvalido para id zero', function (): void {
    expect(fn () => conviteDomainNovo(['timeId' => 0]))
        ->toThrow(ConviteDomainException::class, 'O time do convite é inválido.');
});

test('aceitar marca o convite como aceito quando pendente, e-mail coincide e não pertence a outra conta', function (): void {
    $domain = conviteDomainPendente();
    $domain->aceitar('maria@example.com', 5);

    expect($domain->getStatus())->toBe(ConviteDomain::STATUS_ACEITO)
        ->and($domain->foiAceito())->toBeTrue();
});

test('aceitar aceita e-mail coincidente ignorando maiúsculas', function (): void {
    $domain = conviteDomainPendente();
    $domain->aceitar('Maria@Example.com', null);

    expect($domain->foiAceito())->toBeTrue();
});

test('aceitar lança conviteNaoPendente quando o convite já foi aceito', function (): void {
    $domain = conviteDomainPendente(['status' => ConviteDomain::STATUS_ACEITO]);

    expect(fn () => $domain->aceitar('maria@example.com', 5))
        ->toThrow(ConviteDomainException::class, 'Convite já utilizado.');
});

test('aceitar lança emailNaoCoincidente quando o e-mail diverge', function (): void {
    $domain = conviteDomainPendente();

    expect(fn () => $domain->aceitar('outro@example.com', 5))
        ->toThrow(ConviteDomainException::class, 'Este convite é para outro e-mail.');
});

test('aceitar lança usuarioJaPertenceAOutraConta quando a conta diverge', function (): void {
    $domain = conviteDomainPendente(['contaId' => 5]);

    expect(fn () => $domain->aceitar('maria@example.com', 99))
        ->toThrow(ConviteDomainException::class, 'Você já pertence a outra conta na plataforma.');
});

test('recusar marca o convite como recusado quando pendente e e-mail coincide', function (): void {
    $domain = conviteDomainPendente();
    $domain->recusar('maria@example.com');

    expect($domain->getStatus())->toBe(ConviteDomain::STATUS_RECUSADO)
        ->and($domain->foiRecusado())->toBeTrue();
});

test('recusar lança conviteNaoPendente quando o convite já foi recusado', function (): void {
    $domain = conviteDomainPendente(['status' => ConviteDomain::STATUS_RECUSADO]);

    expect(fn () => $domain->recusar('maria@example.com'))
        ->toThrow(ConviteDomainException::class, 'Convite já utilizado.');
});

test('recusar lança emailNaoCoincidente quando o e-mail diverge', function (): void {
    $domain = conviteDomainPendente();

    expect(fn () => $domain->recusar('outro@example.com'))
        ->toThrow(ConviteDomainException::class, 'Este convite é para outro e-mail.');
});

test('toPersistenceArray retorna chaves esperadas', function (): void {
    $domain = conviteDomainNovo([
        'nome' => 'Ana',
        'email' => 'ana@example.com',
        'token' => 'abc123',
        'timeId' => 7,
    ]);

    expect($domain->toPersistenceArray())->toBe([
        'nome' => 'Ana',
        'email' => 'ana@example.com',
        'time_id' => 7,
        'token' => 'abc123',
        'status' => ConviteDomain::STATUS_PENDENTE,
    ]);
});

test('reconstituir preserva id, status e time', function (): void {
    $domain = ConviteDomain::reconstituir(
        id: 3,
        nome: 'João',
        email: 'joao@example.com',
        timeId: 8,
        token: 'persistido',
        status: ConviteDomain::STATUS_PENDENTE,
        contaId: 2,
    );

    expect($domain->getId())->toBe(3)
        ->and($domain->getNome())->toBe('João')
        ->and($domain->getEmail())->toBe('joao@example.com')
        ->and($domain->getTimeId())->toBe(8)
        ->and($domain->getContaId())->toBe(2)
        ->and($domain->getStatus())->toBe(ConviteDomain::STATUS_PENDENTE)
        ->and($domain->isPendente())->toBeTrue();
});
