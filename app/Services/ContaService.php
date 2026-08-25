<?php

namespace App\Services;

use App\Domain\ContaDomain;
use App\Exceptions\ContaDomainException;
use App\Exceptions\ContaException;
use App\Models\Conta;
use App\Repositories\ContaEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContaService
{
    public function __construct(
        private readonly ContaEloquentRepository $repository,
    ) {}

    public function criar(int $usuarioId, string $nome): void
    {
        try {
            DB::transaction(function () use ($usuarioId, $nome): void {
                $domain = ContaDomain::criar($nome, $usuarioId);

                $this->repository->create($domain->toPersistenceArray());
            });

            Log::info('conta_created', [
                'usuario_id' => $usuarioId,
                'action' => 'create',
            ]);
        } catch (ContaDomainException $exception) {
            throw $exception;
        } catch (ContaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('conta_create_failed', [
                'usuario_id' => $usuarioId,
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw ContaException::createFailed();
        }
    }

    public function renomear(int $contaId, string $nome, int $usuarioId): void
    {
        try {
            DB::transaction(function () use ($contaId, $nome, $usuarioId): void {
                $conta = $this->repository->find($contaId);

                if ($conta === null) {
                    throw ContaException::notFound();
                }

                $domain = $this->convertRecordToDomain($conta);
                $domain->renomear($nome, $usuarioId);

                $this->repository->update($conta, $domain->toPersistenceArray());
            });

            Log::info('conta_renamed', [
                'conta_id' => $contaId,
                'usuario_id' => $usuarioId,
                'action' => 'update',
            ]);
        } catch (ContaDomainException $exception) {
            throw $exception;
        } catch (ContaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('conta_rename_failed', [
                'conta_id' => $contaId,
                'usuario_id' => $usuarioId,
                'action' => 'update',
                'error' => $exception->getMessage(),
            ]);

            throw ContaException::updateFailed();
        }
    }

    private function convertRecordToDomain(Conta $conta): ContaDomain
    {
        return ContaDomain::reconstituir(
            id: (int) $conta->id,
            nome: $conta->nome,
            usuarioId: (int) $conta->usuario_id,
        );
    }
}
