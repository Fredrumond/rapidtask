<?php

use App\DTO\Cliente\ClienteResponseDTO;
use App\Exceptions\ClienteException;
use App\Models\Cliente;
use App\Models\Time;
use App\Models\User;
use App\Repositories\ClienteEloquentRepository;
use App\Services\ClienteApiService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;

function clienteApiModel(array $overrides = []): Cliente
{
    $time = new Time;
    $time->id = $overrides['time_id'] ?? 2;
    $time->nome = 'Time Alpha';

    $usuario = new User;
    $usuario->id = $overrides['usuario_id'] ?? 7;
    $usuario->name = 'Ana';

    $cliente = new Cliente;
    $cliente->id = $overrides['id'] ?? 11;
    $cliente->nome = $overrides['nome'] ?? 'Acme Ltda';
    $cliente->email = $overrides['email'] ?? 'contato@acme.test';
    $cliente->telefone = $overrides['telefone'] ?? '11999999999';
    $cliente->usuario_id = $usuario->id;
    $cliente->time_id = $time->id;
    $cliente->created_at = now();
    $cliente->updated_at = now();
    $cliente->setRelation('time', $time);
    $cliente->setRelation('usuario', $usuario);

    return $cliente;
}

function clienteApiService(MockInterface $repository): ClienteApiService
{
    return new ClienteApiService($repository);
}

beforeEach(function (): void {
    Log::spy();
});

afterEach(function (): void {
    Mockery::close();
});

test('list devolve array de ClienteResponseDTO somente do time informado', function (): void {
    $cliente = clienteApiModel();
    $repository = Mockery::mock(ClienteEloquentRepository::class);
    $repository->shouldReceive('findByTime')
        ->once()
        ->with(2)
        ->andReturn(new Collection([$cliente]));

    $result = clienteApiService($repository)->list(2);

    expect($result)->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(ClienteResponseDTO::class)
        ->and($result[0]->id)->toBe(11)
        ->and($result[0]->nome)->toBe('Acme Ltda')
        ->and($result[0]->time?->id)->toBe(2)
        ->and($result[0]->usuario?->id)->toBe(7)
        ->and($result[0]->jsonSerialize())->toHaveKeys([
            'id',
            'nome',
            'email',
            'telefone',
            'time',
            'usuario',
            'created_at',
            'updated_at',
        ]);
});

test('find devolve ClienteResponseDTO quando o cliente pertence ao time', function (): void {
    $cliente = clienteApiModel();
    $repository = Mockery::mock(ClienteEloquentRepository::class);
    $repository->shouldReceive('findInTime')
        ->once()
        ->with(11, 2)
        ->andReturn($cliente);

    $result = clienteApiService($repository)->find(11, 2);

    expect($result)->toBeInstanceOf(ClienteResponseDTO::class)
        ->and($result->id)->toBe(11)
        ->and($result->email)->toBe('contato@acme.test');
});

test('find lança notFound quando o cliente não existe no time', function (): void {
    $repository = Mockery::mock(ClienteEloquentRepository::class);
    $repository->shouldReceive('findInTime')
        ->once()
        ->with(99, 2)
        ->andReturn(null);

    expect(fn () => clienteApiService($repository)->find(99, 2))
        ->toThrow(ClienteException::class, 'Cliente não encontrado.');
});

test('create persiste via domain e devolve ClienteResponseDTO', function (): void {
    $created = clienteApiModel(['id' => 21, 'nome' => 'Nova Empresa']);
    $repository = Mockery::mock(ClienteEloquentRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $payload): bool => $payload['nome'] === 'Nova Empresa'
            && $payload['usuario_id'] === 7
            && $payload['time_id'] === 2
            && $payload['email'] === 'nova@acme.test'))
        ->andReturn($created);

    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(fn (callable $callback) => $callback());

    $result = clienteApiService($repository)->create([
        'nome' => '  Nova Empresa  ',
        'email' => 'nova@acme.test',
        'telefone' => '11888888888',
        'usuario_id' => 7,
        'time_id' => 2,
    ]);

    expect($result)->toBeInstanceOf(ClienteResponseDTO::class)
        ->and($result->id)->toBe(21)
        ->and($result->nome)->toBe('Nova Empresa');
});

test('update aplica dados no domain e devolve ClienteResponseDTO', function (): void {
    $existente = clienteApiModel();
    $atualizado = clienteApiModel(['nome' => 'Acme Atualizada', 'email' => 'novo@acme.test']);
    $repository = Mockery::mock(ClienteEloquentRepository::class);
    $repository->shouldReceive('findInTime')
        ->once()
        ->with(11, 2)
        ->andReturn($existente);
    $repository->shouldReceive('update')
        ->once()
        ->andReturn($atualizado);

    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(fn (callable $callback) => $callback());

    $result = clienteApiService($repository)->update(11, [
        'nome' => 'Acme Atualizada',
        'email' => 'novo@acme.test',
    ], 2);

    expect($result)->toBeInstanceOf(ClienteResponseDTO::class)
        ->and($result->nome)->toBe('Acme Atualizada')
        ->and($result->email)->toBe('novo@acme.test');
});

test('delete remove o cliente do time e devolve ClienteResponseDTO', function (): void {
    $existente = clienteApiModel();
    $repository = Mockery::mock(ClienteEloquentRepository::class);
    $repository->shouldReceive('findInTime')
        ->once()
        ->with(11, 2)
        ->andReturn($existente);
    $repository->shouldReceive('delete')
        ->once()
        ->with($existente);

    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(fn (callable $callback) => $callback());

    $result = clienteApiService($repository)->delete(11, 2);

    expect($result)->toBeInstanceOf(ClienteResponseDTO::class)
        ->and($result->id)->toBe(11);
});
