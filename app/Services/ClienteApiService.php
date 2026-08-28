<?php

namespace App\Services;

use App\Domain\ClienteDomain;
use App\DTO\Cliente\ClienteResponseDTO;
use App\DTO\Tarefa\NestedLookupDTO;
use App\DTO\Tarefa\NestedUsuarioDTO;
use App\Exceptions\ClienteDomainException;
use App\Exceptions\ClienteException;
use App\Models\Cliente;
use App\Repositories\ClienteEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClienteApiService
{
    public function __construct(
        private readonly ClienteEloquentRepository $repository,
    ) {}

    /**
     * @return list<ClienteResponseDTO>
     */
    public function list(int $timeId): array
    {
        $items = [];

        foreach ($this->repository->findByTime($timeId) as $cliente) {
            $items[] = $this->present($cliente);
        }

        Log::info('api_cliente_listed', [
            'conta_id' => auth()->id(),
            'time_id' => $timeId,
            'action' => 'list',
            'count' => count($items),
        ]);

        return $items;
    }

    public function find(int $id, int $timeId): ClienteResponseDTO
    {
        $cliente = $this->repository->findByIdAndTime($id, $timeId);

        if ($cliente === null) {
            throw ClienteException::notFound();
        }

        Log::info('api_cliente_shown', [
            'conta_id' => auth()->id(),
            'time_id' => $timeId,
            'cliente_id' => $cliente->id,
            'action' => 'show',
        ]);

        return $this->present($cliente);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ClienteResponseDTO
    {
        $timeId = (int) ($data['time_id'] ?? current_time_id());

        try {
            $result = DB::transaction(function () use ($data, $timeId): ClienteResponseDTO {
                $domain = ClienteDomain::criar(
                    nome: (string) $data['nome'],
                    usuarioId: (int) $data['usuario_id'],
                    timeId: $timeId,
                    email: isset($data['email']) ? (string) $data['email'] : null,
                    telefone: isset($data['telefone']) ? (string) $data['telefone'] : null,
                );

                $cliente = $this->repository->create($domain->toPersistenceArray());

                return $this->present($cliente);
            });

            Log::info('api_cliente_created', [
                'conta_id' => auth()->id(),
                'usuario_id' => $data['usuario_id'] ?? null,
                'time_id' => $timeId,
                'cliente_id' => $result->id,
                'action' => 'create',
            ]);

            return $result;
        } catch (ClienteDomainException $exception) {
            throw $exception;
        } catch (ClienteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_cliente_create_failed', [
                'conta_id' => auth()->id(),
                'time_id' => $timeId,
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw ClienteException::operationFailed();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data, int $timeId): ClienteResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($id, $data, $timeId): ClienteResponseDTO {
                $cliente = $this->repository->findByIdAndTime($id, $timeId);

                if ($cliente === null) {
                    throw ClienteException::notFound();
                }

                $domain = $this->convertRecordToDomain($cliente);
                $domain->aplicarAtualizacao($data);

                $updated = $this->repository->update($cliente, $domain->toPersistenceArray());

                return $this->present($updated);
            });

            Log::info('api_cliente_updated', [
                'conta_id' => auth()->id(),
                'time_id' => $timeId,
                'cliente_id' => $result->id,
                'action' => 'update',
            ]);

            return $result;
        } catch (ClienteDomainException $exception) {
            throw $exception;
        } catch (ClienteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_cliente_update_failed', [
                'conta_id' => auth()->id(),
                'time_id' => $timeId,
                'cliente_id' => $id,
                'action' => 'update',
                'error' => $exception->getMessage(),
            ]);

            throw ClienteException::operationFailed();
        }
    }

    public function delete(int $id, int $timeId): void
    {
        try {
            DB::transaction(function () use ($id, $timeId): void {
                $cliente = $this->repository->findByIdAndTime($id, $timeId);

                if ($cliente === null) {
                    throw ClienteException::notFound();
                }

                $this->repository->delete($cliente);
            });

            Log::info('api_cliente_deleted', [
                'conta_id' => auth()->id(),
                'time_id' => $timeId,
                'cliente_id' => $id,
                'action' => 'delete',
            ]);
        } catch (ClienteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_cliente_delete_failed', [
                'conta_id' => auth()->id(),
                'time_id' => $timeId,
                'cliente_id' => $id,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw ClienteException::operationFailed();
        }
    }

    private function present(Cliente $cliente): ClienteResponseDTO
    {
        return $this->convertToDTO($this->convertRecordToDomain($cliente), $cliente);
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

    private function convertToDTO(ClienteDomain $domain, Cliente $cliente): ClienteResponseDTO
    {
        return new ClienteResponseDTO(
            id: (int) $domain->getId(),
            nome: $domain->getNome(),
            email: $domain->getEmail(),
            telefone: $domain->getTelefone(),
            time: $cliente->time !== null
                ? new NestedLookupDTO((int) $cliente->time->id, $cliente->time->nome)
                : null,
            usuario: $cliente->usuario !== null
                ? new NestedUsuarioDTO((int) $cliente->usuario->id, $cliente->usuario->name)
                : null,
            createdAt: $cliente->created_at?->toIso8601String(),
            updatedAt: $cliente->updated_at?->toIso8601String(),
        );
    }
}
