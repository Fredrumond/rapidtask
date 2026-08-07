<?php

namespace App\Services;

use App\Domain\TarefaDomain;
use App\DTO\Tarefa\NestedLookupDTO;
use App\DTO\Tarefa\NestedUsuarioDTO;
use App\DTO\Tarefa\TarefaResponseDTO;
use App\Exceptions\TarefaException;
use App\Models\Conta;
use App\Models\Tarefa;
use App\Repositories\TarefaEloquentRepository;
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

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Conta $conta, array $data): TarefaResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($conta, $data): TarefaResponseDTO {
                $tarefa = $this->repository->create([
                    ...$data,
                    'usuario_id' => $conta->usuario_id,
                    'status' => 0,
                ]);

                return $this->convertToDTO($this->convertRecordToDomain($tarefa));
            });

            Log::info('api_tarefa_created', [
                'conta_id' => $conta->id,
                'usuario_id' => $conta->usuario_id,
                'time_id' => current_time_id(),
                'tarefa_id' => $result->id,
                'action' => 'create',
            ]);

            return $result;
        } catch (TarefaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_tarefa_create_failed', [
                'conta_id' => $conta->id,
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

                $updated = $this->repository->update($tarefa, $data);

                return $this->convertToDTO($this->convertRecordToDomain($updated));
            });

            Log::info('api_tarefa_updated', [
                'conta_id' => auth()->id(),
                'time_id' => current_time_id(),
                'tarefa_id' => $result->id,
                'action' => 'update',
            ]);

            return $result;
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
        return new TarefaDomain(
            id: $tarefa->id,
            titulo: $tarefa->titulo,
            descricao: $tarefa->descricao,
            dtInicio: $tarefa->dt_inicio?->format('Y-m-d'),
            dtPrevista: $tarefa->dt_prevista?->format('Y-m-d'),
            dtFim: $tarefa->dt_fim?->format('Y-m-d'),
            tempoEstimado: $tarefa->tempo_estimado,
            status: (int) $tarefa->status,
            tipo: $tarefa->tipo !== null
                ? ['id' => $tarefa->tipo->id, 'nome' => $tarefa->tipo->nome]
                : null,
            situacao: $tarefa->situacao !== null
                ? ['id' => $tarefa->situacao->id, 'nome' => $tarefa->situacao->nome]
                : null,
            prioridade: $tarefa->prioridade !== null
                ? ['id' => $tarefa->prioridade->id, 'nome' => $tarefa->prioridade->nome]
                : null,
            projeto: $tarefa->projeto !== null
                ? ['id' => $tarefa->projeto->id, 'nome' => $tarefa->projeto->nome]
                : null,
            usuario: $tarefa->usuario !== null
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
}
