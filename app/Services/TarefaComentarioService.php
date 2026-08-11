<?php

namespace App\Services;

use App\Domain\TarefaComentarioDomain;
use App\DTO\Tarefa\NestedUsuarioDTO;
use App\DTO\TarefaComentario\TarefaComentarioResponseDTO;
use App\Exceptions\TarefaComentarioDomainException;
use App\Exceptions\TarefaComentarioException;
use App\Models\TarefaComentario;
use App\Repositories\TarefaComentarioEloquentRepository;
use App\Repositories\TarefaEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TarefaComentarioService
{
    public function __construct(
        private readonly TarefaComentarioEloquentRepository $repository,
        private readonly TarefaEloquentRepository $tarefaRepository,
    ) {}

    /**
     * @return list<TarefaComentarioResponseDTO>
     */
    public function list(int $tarefaId): array
    {
        $this->assertTarefaExists($tarefaId);

        $items = [];

        foreach ($this->repository->listByTarefa($tarefaId) as $comentario) {
            $items[] = $this->convertToDTO($this->convertRecordToDomain($comentario));
        }

        Log::info('api_tarefa_comentario_listed', [
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'tarefa_id' => $tarefaId,
            'action' => 'list',
            'count' => count($items),
        ]);

        return $items;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(int $usuarioId, int $tarefaId, array $data, ?int $contaId = null): TarefaComentarioResponseDTO
    {
        $this->assertTarefaExists($tarefaId);

        try {
            $result = DB::transaction(function () use ($usuarioId, $tarefaId, $data): TarefaComentarioResponseDTO {
                $domain = TarefaComentarioDomain::criar(
                    tarefaId: $tarefaId,
                    usuarioId: $usuarioId,
                    comentario: (string) $data['comentario'],
                );

                $comentario = $this->repository->create($domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($comentario));
            });

            Log::info('api_tarefa_comentario_created', [
                'conta_id' => $contaId ?? auth()->id(),
                'usuario_id' => $usuarioId,
                'time_id' => current_time_id(),
                'tarefa_id' => $tarefaId,
                'comentario_id' => $result->id,
                'action' => 'create',
            ]);

            return $result;
        } catch (TarefaComentarioDomainException $exception) {
            throw $exception;
        } catch (TarefaComentarioException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_comentario_create_failed', [
                'conta_id' => $contaId ?? auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $tarefaId,
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaComentarioException::createFailed();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $tarefaId, int $comentarioId, array $data): TarefaComentarioResponseDTO
    {
        $this->assertTarefaExists($tarefaId);

        try {
            $result = DB::transaction(function () use ($tarefaId, $comentarioId, $data): TarefaComentarioResponseDTO {
                $comentario = $this->repository->findForTarefa($tarefaId, $comentarioId);

                if ($comentario === null) {
                    throw TarefaComentarioException::notFound();
                }

                $domain = $this->convertRecordToDomain($comentario);
                $domain->editarTexto((string) $data['comentario']);

                $updated = $this->repository->update($comentario, $domain->toPersistenceArray());

                return $this->convertToDTO($this->convertRecordToDomain($updated));
            });

            Log::info('api_tarefa_comentario_updated', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $tarefaId,
                'comentario_id' => $result->id,
                'action' => 'update',
            ]);

            return $result;
        } catch (TarefaComentarioDomainException $exception) {
            throw $exception;
        } catch (TarefaComentarioException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_comentario_update_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $tarefaId,
                'comentario_id' => $comentarioId,
                'action' => 'update',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaComentarioException::updateFailed();
        }
    }

    public function delete(int $tarefaId, int $comentarioId): void
    {
        $this->assertTarefaExists($tarefaId);

        try {
            DB::transaction(function () use ($tarefaId, $comentarioId): void {
                $comentario = $this->repository->findForTarefa($tarefaId, $comentarioId);

                if ($comentario === null) {
                    throw TarefaComentarioException::notFound();
                }

                $this->repository->delete($comentario);
            });

            Log::info('api_tarefa_comentario_deleted', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $tarefaId,
                'comentario_id' => $comentarioId,
                'action' => 'delete',
            ]);
        } catch (TarefaComentarioException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_comentario_delete_failed', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $tarefaId,
                'comentario_id' => $comentarioId,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw TarefaComentarioException::deleteFailed();
        }
    }

    private function assertTarefaExists(int $tarefaId): void
    {
        if (! $this->tarefaRepository->exists($tarefaId)) {
            throw TarefaComentarioException::tarefaNotFound();
        }
    }

    private function convertRecordToDomain(TarefaComentario $comentario): TarefaComentarioDomain
    {
        return TarefaComentarioDomain::reconstituir(
            id: (int) $comentario->id,
            tarefaId: (int) $comentario->tarefa_id,
            usuarioId: (int) $comentario->usuario_id,
            comentario: $comentario->comentario,
            usuarioLookup: $comentario->usuario !== null
                ? ['id' => $comentario->usuario->id, 'name' => $comentario->usuario->name]
                : null,
            createdAt: $comentario->created_at?->toIso8601String(),
            updatedAt: $comentario->updated_at?->toIso8601String(),
        );
    }

    private function convertToDTO(TarefaComentarioDomain $domain): TarefaComentarioResponseDTO
    {
        $usuario = $domain->getUsuario();

        return new TarefaComentarioResponseDTO(
            id: (int) $domain->getId(),
            tarefaId: $domain->getTarefaId(),
            comentario: $domain->getComentario(),
            usuario: $usuario !== null ? new NestedUsuarioDTO($usuario['id'], $usuario['name']) : null,
            createdAt: $domain->getCreatedAt(),
            updatedAt: $domain->getUpdatedAt(),
        );
    }
}
