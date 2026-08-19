<?php

namespace App\Services;

use App\Domain\ClienteDomain;
use App\Exceptions\ClienteDomainException;
use App\Exceptions\ClienteException;
use App\Models\Cliente;
use App\Repositories\ClienteEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClienteService
{
    public function __construct(
        private readonly ClienteEloquentRepository $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(int $usuarioId, array $data, ?int $contaId = null): void
    {
        $timeId = current_time_id();

        if ($timeId === null) {
            throw ClienteException::createFailed();
        }

        try {
            DB::transaction(function () use ($usuarioId, $timeId, $data): void {
                $domain = ClienteDomain::criar(
                    nome: (string) $data['nome'],
                    usuarioId: $usuarioId,
                    timeId: $timeId,
                    email: isset($data['email']) ? (string) $data['email'] : null,
                    telefone: isset($data['telefone']) ? (string) $data['telefone'] : null,
                );

                $this->repository->create($domain->toPersistenceArray());
            });

            Log::info('cliente_created', [
                'conta_id' => $contaId ?? current_conta_id(),
                'usuario_id' => $usuarioId,
                'time_id' => $timeId,
                'action' => 'create',
            ]);
        } catch (ClienteDomainException $exception) {
            throw $exception;
        } catch (ClienteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('cliente_create_failed', [
                'conta_id' => $contaId ?? current_conta_id(),
                'time_id' => $timeId,
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw ClienteException::createFailed();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): void
    {
        try {
            DB::transaction(function () use ($id, $data): void {
                $cliente = $this->repository->find($id);

                if ($cliente === null) {
                    throw ClienteException::notFound();
                }

                $domain = $this->convertRecordToDomain($cliente);
                $domain->aplicarAtualizacao($data);

                $this->repository->update($cliente, $domain->toPersistenceArray());
            });

            Log::info('cliente_updated', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'cliente_id' => $id,
                'action' => 'update',
            ]);
        } catch (ClienteDomainException $exception) {
            throw $exception;
        } catch (ClienteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('cliente_update_failed', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'cliente_id' => $id,
                'action' => 'update',
                'error' => $exception->getMessage(),
            ]);

            throw ClienteException::updateFailed();
        }
    }

    public function delete(int $id): void
    {
        try {
            DB::transaction(function () use ($id): void {
                $cliente = $this->repository->find($id);

                if ($cliente === null) {
                    throw ClienteException::notFound();
                }

                $this->repository->delete($cliente);
            });

            Log::info('cliente_deleted', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'cliente_id' => $id,
                'action' => 'delete',
            ]);
        } catch (ClienteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('cliente_delete_failed', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'cliente_id' => $id,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw ClienteException::deleteFailed();
        }
    }

    private function convertRecordToDomain(Cliente $cliente): ClienteDomain
    {
        return ClienteDomain::reconstituir(
            id: (int) $cliente->id,
            nome: $cliente->nome,
            usuarioId: (int) $cliente->usuario_id,
            timeId: (int) $cliente->time_id,
            email: $cliente->email,
            telefone: $cliente->telefone,
        );
    }
}
