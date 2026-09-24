<?php

use App\Exceptions\DuvidaDomainException;
use App\Exceptions\DuvidaException;
use App\Models\Duvida;
use App\Repositories\DuvidaEloquentRepository;
use App\Services\DuvidaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

test('criar com nome email telefone e duvida validos grava uma linha', function () {
    app(DuvidaService::class)->create([
        'nome' => 'Marina Costa',
        'email' => 'marina@exemplo.com',
        'telefone' => '11988887777',
        'duvida' => 'Como acompanho um projeto?',
    ]);

    $this->assertDatabaseHas('duvidas', [
        'nome' => 'Marina Costa',
        'email' => 'marina@exemplo.com',
        'telefone' => '11988887777',
        'duvida' => 'Como acompanho um projeto?',
    ]);

    expect(Duvida::query()->count())->toBe(1);
});

test('campo em branco nao grava a duvida', function (string $campo) {
    $data = [
        'nome' => 'Marina Costa',
        'email' => 'marina@exemplo.com',
        'telefone' => '11988887777',
        'duvida' => 'Como acompanho um projeto?',
    ];
    $data[$campo] = '   ';

    expect(fn () => app(DuvidaService::class)->create($data))
        ->toThrow(DuvidaDomainException::class);

    expect(Duvida::query()->count())->toBe(0);
})->with(['nome', 'email', 'telefone', 'duvida']);

test('email invalido nao grava a duvida', function () {
    expect(fn () => app(DuvidaService::class)->create([
        'nome' => 'Marina Costa',
        'email' => 'nao-e-email',
        'telefone' => '11988887777',
        'duvida' => 'Como acompanho um projeto?',
    ]))->toThrow(DuvidaDomainException::class);

    expect(Duvida::query()->count())->toBe(0);
});

test('tabela duvidas nao tem time_id nem usuario_id', function () {
    expect(Schema::hasTable('duvidas'))->toBeTrue()
        ->and(Schema::hasColumn('duvidas', 'time_id'))->toBeFalse()
        ->and(Schema::hasColumn('duvidas', 'usuario_id'))->toBeFalse()
        ->and(Schema::hasColumns('duvidas', [
            'id',
            'nome',
            'email',
            'telefone',
            'duvida',
            'created_at',
            'updated_at',
        ]))->toBeTrue();
});

test('log de sucesso e de falha nao contem o texto da duvida o email nem o telefone', function () {
    Log::spy();

    $email = 'visitante-secreto@exemplo.com';
    $telefone = '11977776666';
    $duvida = 'Texto unico da duvida para o log';

    app(DuvidaService::class)->create([
        'nome' => 'Paulo Henrique',
        'email' => $email,
        'telefone' => $telefone,
        'duvida' => $duvida,
    ]);

    Log::shouldHaveReceived('info')->withArgs(
        fn (string $message, array $context): bool => logNaoContemDadoPessoal($message, $context, $email, $telefone, $duvida)
            && $message === 'duvida_created'
    );

    app()->instance(DuvidaEloquentRepository::class, new class extends DuvidaEloquentRepository
    {
        public function create(array $data): Duvida
        {
            throw new RuntimeException('falha interna');
        }
    });

    expect(fn () => app(DuvidaService::class)->create([
        'nome' => 'Paulo Henrique',
        'email' => $email,
        'telefone' => $telefone,
        'duvida' => $duvida,
    ]))->toThrow(DuvidaException::class);

    Log::shouldHaveReceived('error')->withArgs(
        fn (string $message, array $context): bool => logNaoContemDadoPessoal($message, $context, $email, $telefone, $duvida)
            && $message === 'duvida_create_failed'
    );

    expect(Duvida::query()->count())->toBe(1);
});

function logNaoContemDadoPessoal(string $message, array $context, string $email, string $telefone, string $duvida): bool
{
    $encoded = $message.' '.json_encode($context);

    return ! str_contains($encoded, $email)
        && ! str_contains($encoded, $telefone)
        && ! str_contains($encoded, $duvida);
}
