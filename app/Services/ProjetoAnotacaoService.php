<?php

namespace App\Services;

use App\Domain\ProjetoAnotacaoDomain;
use App\Exceptions\ProjetoAnotacaoDomainException;
use App\Exceptions\ProjetoAnotacaoException;
use App\Models\ProjetoAnotacao;
use App\Repositories\ProjetoAnotacaoEloquentRepository;
use App\Repositories\ProjetoEloquentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProjetoAnotacaoService
{
    public function __construct(
        private readonly ProjetoAnotacaoEloquentRepository $repository,
        private readonly ProjetoEloquentRepository $projetoRepository,
    ) {}

    /**
     * @return Collection<int, ProjetoAnotacao>
     */
    public function list(int $projetoId): Collection
    {
        $this->assertProjetoExists($projetoId);

        return $this->repository->listByProjeto($projetoId);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(int $usuarioId, int $projetoId, array $data, ?int $contaId = null): ProjetoAnotacaoDomain
    {
        $this->assertProjetoExists($projetoId);

        try {
            $result = DB::transaction(function () use ($usuarioId, $projetoId, $data): ProjetoAnotacaoDomain {
                $domain = ProjetoAnotacaoDomain::criar(
                    projetoId: $projetoId,
                    usuarioId: $usuarioId,
                    anotacao: (string) $data['anotacao'],
                );

                $anotacao = $this->repository->create($domain->toPersistenceArray());

                return $this->convertRecordToDomain($anotacao);
            });

            Log::info('projeto_anotacao_created', [
                'conta_id' => $contaId ?? current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'anotacao_id' => $result->getId(),
                'action' => 'create',
            ]);

            return $result;
        } catch (ProjetoAnotacaoDomainException $exception) {
            throw $exception;
        } catch (ProjetoAnotacaoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('projeto_anotacao_create_failed', [
                'conta_id' => $contaId ?? current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoAnotacaoException::createFailed();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $projetoId, int $anotacaoId, array $data): ProjetoAnotacaoDomain
    {
        $this->assertProjetoExists($projetoId);

        try {
            $result = DB::transaction(function () use ($projetoId, $anotacaoId, $data): ProjetoAnotacaoDomain {
                $anotacao = $this->repository->findForProjeto($projetoId, $anotacaoId);

                if ($anotacao === null) {
                    throw ProjetoAnotacaoException::notFound();
                }

                $domain = $this->convertRecordToDomain($anotacao);
                $domain->editarTexto((string) $data['anotacao']);

                $updated = $this->repository->update($anotacao, $domain->toPersistenceArray());

                return $this->convertRecordToDomain($updated);
            });

            Log::info('projeto_anotacao_updated', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'anotacao_id' => $result->getId(),
                'action' => 'update',
            ]);

            return $result;
        } catch (ProjetoAnotacaoDomainException $exception) {
            throw $exception;
        } catch (ProjetoAnotacaoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('projeto_anotacao_update_failed', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'anotacao_id' => $anotacaoId,
                'action' => 'update',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoAnotacaoException::updateFailed();
        }
    }

    public function delete(int $projetoId, int $anotacaoId): void
    {
        $this->assertProjetoExists($projetoId);

        try {
            DB::transaction(function () use ($projetoId, $anotacaoId): void {
                $anotacao = $this->repository->findForProjeto($projetoId, $anotacaoId);

                if ($anotacao === null) {
                    throw ProjetoAnotacaoException::notFound();
                }

                $this->repository->delete($anotacao);
            });

            Log::info('projeto_anotacao_deleted', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'anotacao_id' => $anotacaoId,
                'action' => 'delete',
            ]);
        } catch (ProjetoAnotacaoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('projeto_anotacao_delete_failed', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'anotacao_id' => $anotacaoId,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoAnotacaoException::deleteFailed();
        }
    }

    private function assertProjetoExists(int $projetoId): void
    {
        if ($this->projetoRepository->find($projetoId) === null) {
            throw ProjetoAnotacaoException::projetoNotFound();
        }
    }

    private function convertRecordToDomain(ProjetoAnotacao $anotacao): ProjetoAnotacaoDomain
    {
        return ProjetoAnotacaoDomain::reconstituir(
            id: (int) $anotacao->id,
            projetoId: (int) $anotacao->projeto_id,
            usuarioId: (int) $anotacao->usuario_id,
            anotacao: $anotacao->anotacao,
            usuarioLookup: $anotacao->usuario !== null
                ? ['id' => $anotacao->usuario->id, 'name' => $anotacao->usuario->name]
                : null,
            createdAt: $anotacao->created_at?->toIso8601String(),
            updatedAt: $anotacao->updated_at?->toIso8601String(),
        );
    }
}
