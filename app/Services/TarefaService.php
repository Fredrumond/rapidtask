<?php

namespace App\Services;

use App\Exceptions\TarefaDomainException;
use App\Enums\TarefaSituacao;
use App\Enums\TarefaStatus;
use App\Domain\TarefaDomain;
use App\DTO\Tarefa\NestedLookupDTO;
use App\DTO\Tarefa\NestedUsuarioDTO;
use App\DTO\Tarefa\TarefaResponseDTO;
use App\Exceptions\TarefaException;
use App\Models\Tarefa;
use App\Repositories\TarefaEloquentRepository;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TarefaService
{
    public function __construct(
        private readonly TarefaEloquentRepository $repository,
    ) {}

    /**
     * @return list<TarefaResponseDTO>
     */
    public function list(): array
    {
        $items = [];

        foreach ($this->repository->list() as $tarefa) {
            $items[] = $this->convertToDTO($this->convertRecordToDomain($tarefa));
        }

        Log::info('api_tarefa_listed', [
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'action' => 'list',
            'count' => count($items),
        ]);

        return $items;
    }

    public function find(int $id): TarefaResponseDTO
    {
        $tarefa = $this->repository->find($id);

        if ($tarefa === null) {
            throw TarefaException::notFound();
        }

        Log::info('api_tarefa_shown', [
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'tarefa_id' => $tarefa->id,
            'action' => 'show',
        ]);

        return $this->convertToDTO($this->convertRecordToDomain($tarefa));
    }

    public function findModel(int $id): Tarefa
    {
        $tarefa = $this->repository->find($id);

        if ($tarefa === null) {
            throw TarefaException::notFound();
        }

        return $tarefa;
    }

    public function present(Tarefa $tarefa): TarefaResponseDTO
    {
        return $this->convertToDTO($this->convertRecordToDomain($tarefa));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(int $usuarioId, array $data, ?int $contaId = null): TarefaResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($usuarioId, $data): TarefaResponseDTO {
                $domain = TarefaDomain::criar(
                    titulo: (string) $data['titulo'],
                    projetoId: (int) $data['projeto_id'],
                    usuarioId: $usuarioId,
                    tipoId: (int) $data['tipo_id'],
                    prioridadeId: (int) $data['prioridade_id'],
                    situacao: isset($data['situacao_id'])
                        ? TarefaSituacao::from((int) $data['situacao_id'])
                        : TarefaSituacao::Novo,
                    descricao: isset($data['descricao']) ? (string) $data['descricao'] : null,
                    dtInicio: $this->parseOptionalDate($data['dt_inicio'] ?? null),
                    dtPrevista: $this->parseOptionalDate($data['dt_prevista'] ?? null),
                    dtFim: $this->parseOptionalDate($data['dt_fim'] ?? null),
                    tempoEstimado: array_key_exists('tempo_estimado', $data) && $data['tempo_estimado'] !== null && $data['tempo_estimado'] !== ''
                        ? (int) $data['tempo_estimado']
                        : null,
                );

                $tarefa = $this->repository->create($domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($tarefa));
            });

            Log::info('api_tarefa_created', [
                'conta_id' => $contaId ?? auth()->id(),
                'usuario_id' => $usuarioId,
                'time_id' => current_time_id(),
                'tarefa_id' => $result->id,
                'action' => 'create',
            ]);

            return $result;
        } catch (TarefaDomainException $exception) {
            throw $exception;
        } catch (TarefaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_create_failed', [
                'conta_id' => $contaId ?? auth()->id(),
                'time_id' => current_time_id(),
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaException::createFailed();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): TarefaResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($id, $data): TarefaResponseDTO {
                $tarefa = $this->repository->find($id);

                if ($tarefa === null) {
                    throw TarefaException::notFound();
                }

                $domain = $this->convertRecordToDomain($tarefa);
                $domain->aplicarAtualizacao($data);

                $updated = $this->repository->update($tarefa, $domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($updated));
            });

            Log::info('api_tarefa_updated', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $result->id,
                'action' => 'update',
            ]);

            return $result;
        } catch (TarefaDomainException $exception) {
            throw $exception;
        } catch (TarefaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_update_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $id,
                'action' => 'update',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaException::updateFailed();
        }
    }

    public function arquivar(int $id): TarefaResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($id): TarefaResponseDTO {
                $tarefa = $this->repository->find($id);

                if ($tarefa === null) {
                    throw TarefaException::notFound();
                }

                $domain = $this->convertRecordToDomain($tarefa);
                $domain->arquivar();

                $updated = $this->repository->update($tarefa, $domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($updated));
            });

            Log::info('api_tarefa_archived', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $result->id,
                'action' => 'arquivar',
            ]);

            return $result;
        } catch (TarefaDomainException $exception) {
            throw $exception;
        } catch (TarefaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_archive_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $id,
                'action' => 'arquivar',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaException::updateFailed();
        }
    }

    public function recuperar(int $id): TarefaResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($id): TarefaResponseDTO {
                $tarefa = $this->repository->find($id);

                if ($tarefa === null) {
                    throw TarefaException::notFound();
                }

                $domain = $this->convertRecordToDomain($tarefa);
                $domain->recuperar();

                $updated = $this->repository->update($tarefa, $domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($updated));
            });

            Log::info('api_tarefa_recovered', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $result->id,
                'action' => 'recuperar',
            ]);

            return $result;
        } catch (TarefaDomainException $exception) {
            throw $exception;
        } catch (TarefaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_recover_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $id,
                'action' => 'recuperar',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaException::updateFailed();
        }
    }

    public function delete(int $id): void
    {
        try {
            DB::transaction(function () use ($id): void {
                $tarefa = $this->repository->find($id);

                if ($tarefa === null) {
                    throw TarefaException::notFound();
                }

                $this->repository->delete($tarefa);
            });

            Log::info('api_tarefa_deleted', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $id,
                'action' => 'delete',
            ]);
        } catch (TarefaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_delete_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $id,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaException::deleteFailed();
        }
    }

    private function convertRecordToDomain(Tarefa $tarefa): TarefaDomain
    {
        return TarefaDomain::reconstituir(
            id: (int) $tarefa->id,
            titulo: $tarefa->titulo,
            descricao: $tarefa->descricao,
            projetoId: (int) $tarefa->projeto_id,
            usuarioId: (int) $tarefa->usuario_id,
            tipoId: (int) $tarefa->tipo_id,
            prioridadeId: (int) $tarefa->prioridade_id,
            situacao: TarefaSituacao::from((int) $tarefa->situacao_id),
            status: TarefaStatus::from((int) $tarefa->status),
            dtInicio: $tarefa->dt_inicio !== null
                ? new DateTimeImmutable($tarefa->dt_inicio->format('Y-m-d'))
                : null,
            dtPrevista: $tarefa->dt_prevista !== null
                ? new DateTimeImmutable($tarefa->dt_prevista->format('Y-m-d'))
                : null,
            dtFim: $tarefa->dt_fim !== null
                ? new DateTimeImmutable($tarefa->dt_fim->format('Y-m-d'))
                : null,
            tempoEstimado: $tarefa->tempo_estimado,
            tipoLookup: $tarefa->tipo !== null
                ? ['id' => $tarefa->tipo->id, 'nome' => $tarefa->tipo->nome]
                : null,
            situacaoLookup: $tarefa->situacao !== null
                ? ['id' => $tarefa->situacao->id, 'nome' => $tarefa->situacao->nome]
                : null,
            prioridadeLookup: $tarefa->prioridade !== null
                ? ['id' => $tarefa->prioridade->id, 'nome' => $tarefa->prioridade->nome]
                : null,
            projetoLookup: $tarefa->projeto !== null
                ? ['id' => $tarefa->projeto->id, 'nome' => $tarefa->projeto->nome]
                : null,
            usuarioLookup: $tarefa->usuario !== null
                ? ['id' => $tarefa->usuario->id, 'name' => $tarefa->usuario->name]
                : null,
            createdAt: $tarefa->created_at?->toIso8601String(),
            updatedAt: $tarefa->updated_at?->toIso8601String(),
        );
    }

    private function convertToDTO(TarefaDomain $domain): TarefaResponseDTO
    {
        $tipo = $domain->getTipo();
        $situacao = $domain->getSituacao();
        $prioridade = $domain->getPrioridade();
        $projeto = $domain->getProjeto();
        $usuario = $domain->getUsuario();

        return new TarefaResponseDTO(
            id: (int) $domain->getId(),
            titulo: $domain->getTitulo(),
            descricao: $domain->getDescricao(),
            dtInicio: $domain->getDtInicio(),
            dtPrevista: $domain->getDtPrevista(),
            dtFim: $domain->getDtFim(),
            tempoEstimado: $domain->getTempoEstimado(),
            status: $domain->getStatus(),
            tipo: $tipo !== null ? new NestedLookupDTO($tipo['id'], $tipo['nome']) : null,
            situacao: $situacao !== null ? new NestedLookupDTO($situacao['id'], $situacao['nome']) : null,
            prioridade: $prioridade !== null ? new NestedLookupDTO($prioridade['id'], $prioridade['nome']) : null,
            projeto: $projeto !== null ? new NestedLookupDTO($projeto['id'], $projeto['nome']) : null,
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
