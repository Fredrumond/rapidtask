<?php

namespace App\Services;

use App\Domain\ProjetoDomain;
use App\DTO\Projeto\ProjetoResponseDTO;
use App\DTO\Tarefa\NestedLookupDTO;
use App\DTO\Tarefa\NestedUsuarioDTO;
use App\Exceptions\ProjetoDomainException;
use App\Exceptions\ProjetoException;
use App\Models\Projeto;
use App\Repositories\ProjetoEloquentRepository;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProjetoService
{
    public function __construct(
        private readonly ProjetoEloquentRepository $repository,
    ) {}

    /**
     * @return list<ProjetoResponseDTO>
     */
    public function list(): array
    {
        $items = [];

        foreach ($this->repository->list() as $projeto) {
            $items[] = $this->convertToDTO($this->convertRecordToDomain($projeto));
        }

        Log::info('api_projeto_listed', [
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'action' => 'list',
            'count' => count($items),
        ]);

        return $items;
    }

    public function find(int $id): ProjetoResponseDTO
    {
        $projeto = $this->repository->find($id);

        if ($projeto === null) {
            throw ProjetoException::notFound();
        }

        Log::info('api_projeto_shown', [
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'projeto_id' => $projeto->id,
            'action' => 'show',
        ]);

        return $this->convertToDTO($this->convertRecordToDomain($projeto));
    }

    public function findModel(int $id): Projeto
    {
        $projeto = $this->repository->find($id);

        if ($projeto === null) {
            throw ProjetoException::notFound();
        }

        return $projeto;
    }

    public function present(Projeto $projeto): ProjetoResponseDTO
    {
        Log::info('api_projeto_shown', [
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'projeto_id' => $projeto->id,
            'action' => 'show',
        ]);

        return $this->convertToDTO($this->convertRecordToDomain($projeto));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(int $usuarioId, array $data, ?int $contaId = null): ProjetoResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($usuarioId, $data): ProjetoResponseDTO {
                $domain = ProjetoDomain::criar(
                    nome: (string) $data['nome'],
                    sigla: (string) $data['sigla'],
                    clienteId: (int) $data['cliente_id'],
                    usuarioId: $usuarioId,
                    timeId: (int) current_time_id(),
                    descricao: isset($data['descricao']) ? (string) $data['descricao'] : null,
                    dtInicio: $this->parseOptionalDate($data['dt_inicio'] ?? null),
                    dtPrevista: $this->parseOptionalDate($data['dt_prevista'] ?? null),
                    dtFim: $this->parseOptionalDate($data['dt_fim'] ?? null),
                );

                $projeto = $this->repository->create($domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($projeto));
            });

            Log::info('api_projeto_created', [
                'conta_id' => $contaId ?? auth()->id(),
                'usuario_id' => $usuarioId,
                'time_id' => current_time_id(),
                'projeto_id' => $result->id,
                'action' => 'create',
            ]);

            return $result;
        } catch (ProjetoDomainException $exception) {
            throw $exception;
        } catch (ProjetoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_projeto_create_failed', [
                'conta_id' => $contaId ?? auth()->id(),
                'time_id' => current_time_id(),
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoException::createFailed();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ProjetoResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($id, $data): ProjetoResponseDTO {
                $projeto = $this->repository->find($id);

                if ($projeto === null) {
                    throw ProjetoException::notFound();
                }

                $domain = $this->convertRecordToDomain($projeto);
                $domain->aplicarAtualizacao($data);

                $updated = $this->repository->update($projeto, $domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($updated));
            });

            Log::info('api_projeto_updated', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'projeto_id' => $result->id,
                'action' => 'update',
            ]);

            return $result;
        } catch (ProjetoDomainException $exception) {
            throw $exception;
        } catch (ProjetoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_projeto_update_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'projeto_id' => $id,
                'action' => 'update',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoException::updateFailed();
        }
    }

    public function delete(int $id): void
    {
        try {
            DB::transaction(function () use ($id): void {
                $projeto = $this->repository->find($id);

                if ($projeto === null) {
                    throw ProjetoException::notFound();
                }

                $this->repository->delete($projeto);
            });

            Log::info('api_projeto_deleted', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'projeto_id' => $id,
                'action' => 'delete',
            ]);
        } catch (ProjetoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_projeto_delete_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'projeto_id' => $id,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoException::deleteFailed();
        }
    }

    private function convertRecordToDomain(Projeto $projeto): ProjetoDomain
    {
        return ProjetoDomain::reconstituir(
            id: (int) $projeto->id,
            nome: $projeto->nome,
            sigla: $projeto->sigla,
            clienteId: (int) $projeto->cliente_id,
            usuarioId: (int) $projeto->usuario_id,
            timeId: (int) $projeto->time_id,
            descricao: $projeto->descricao,
            dtInicio: $projeto->dt_inicio !== null
                ? new DateTimeImmutable($projeto->dt_inicio->format('Y-m-d'))
                : null,
            dtPrevista: $projeto->dt_prevista !== null
                ? new DateTimeImmutable($projeto->dt_prevista->format('Y-m-d'))
                : null,
            dtFim: $projeto->dt_fim !== null
                ? new DateTimeImmutable($projeto->dt_fim->format('Y-m-d'))
                : null,
            clienteLookup: $projeto->cliente !== null
                ? ['id' => $projeto->cliente->id, 'nome' => $projeto->cliente->nome]
                : null,
            timeLookup: $projeto->time !== null
                ? ['id' => $projeto->time->id, 'nome' => $projeto->time->nome]
                : null,
            usuarioLookup: $projeto->usuario !== null
                ? ['id' => $projeto->usuario->id, 'name' => $projeto->usuario->name]
                : null,
            createdAt: $projeto->created_at?->toIso8601String(),
            updatedAt: $projeto->updated_at?->toIso8601String(),
        );
    }

    private function convertToDTO(ProjetoDomain $domain): ProjetoResponseDTO
    {
        $cliente = $domain->getCliente();
        $time = $domain->getTime();
        $usuario = $domain->getUsuario();

        return new ProjetoResponseDTO(
            id: (int) $domain->getId(),
            nome: $domain->getNome(),
            sigla: $domain->getSigla(),
            descricao: $domain->getDescricao(),
            dtInicio: $domain->getDtInicio(),
            dtPrevista: $domain->getDtPrevista(),
            dtFim: $domain->getDtFim(),
            cliente: $cliente !== null ? new NestedLookupDTO($cliente['id'], $cliente['nome']) : null,
            time: $time !== null ? new NestedLookupDTO($time['id'], $time['nome']) : null,
            usuario: $usuario !== null ? new NestedUsuarioDTO($usuario['id'], $usuario['name']) : null,
            createdAt: $domain->getCreatedAt(),
            updatedAt: $domain->getUpdatedAt(),
        );
    }

    private function parseOptionalDate(mixed $value): ?DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new DateTimeImmutable((string) $value);
    }
}
