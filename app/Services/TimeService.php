<?php

namespace App\Services;

use App\Domain\TimeDomain;
use App\Exceptions\ContaDomainException;
use App\Exceptions\ContaException;
use App\Exceptions\TimeDomainException;
use App\Exceptions\TimeException;
use App\Models\Time;
use App\Models\User;
use App\Repositories\ContaEloquentRepository;
use App\Repositories\TimeEloquentRepository;
use App\Support\CurrentTeam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TimeService
{
    private const NIVEL_ADMIN = 1;

    public function __construct(
        private readonly TimeEloquentRepository $repository,
        private readonly ContaEloquentRepository $contaRepository,
        private readonly ContaService $contaService,
    ) {}

    public function criar(int $usuarioId, string $nome): void
    {
        try {
            $timeId = DB::transaction(function () use ($usuarioId, $nome): int {
                $user = User::query()->find($usuarioId);

                if ($user === null) {
                    throw TimeException::createFailed();
                }

                $conta = $this->contaRepository->findByUsuarioId($usuarioId);

                if ($conta === null) {
                    $this->contaService->criar($usuarioId, 'Conta de '.$user->name);
                    $conta = $this->contaRepository->findByUsuarioId($usuarioId);
                }

                if ($conta === null) {
                    throw TimeException::createFailed();
                }

                $domain = TimeDomain::criar(
                    nome: $nome,
                    contaId: (int) $conta->id,
                    criadorId: $usuarioId,
                );

                $time = $this->repository->create($domain->toPersistenceArray());
                $this->repository->addMembro($time, $usuarioId, self::NIVEL_ADMIN);

                return (int) $time->id;
            });

            CurrentTeam::set($timeId);

            Log::info('time_created', [
                'conta_id' => current_conta_id(),
                'usuario_id' => $usuarioId,
                'time_id' => $timeId,
                'action' => 'create',
            ]);
        } catch (TimeDomainException|ContaDomainException $exception) {
            throw $exception;
        } catch (TimeException|ContaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('time_create_failed', [
                'usuario_id' => $usuarioId,
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw TimeException::createFailed();
        }
    }

    public function excluir(int $timeId, int $usuarioId): void
    {
        try {
            DB::transaction(function () use ($timeId, $usuarioId): void {
                $time = $this->repository->find($timeId);

                if ($time === null) {
                    throw TimeException::notFound();
                }

                $user = User::query()->find($usuarioId);

                if ($user === null) {
                    throw TimeException::deleteFailed();
                }

                $domain = $this->convertRecordToDomain($time, $user->isAdminOf($timeId));
                $domain->excluir();

                $this->repository->delete($time);
            });

            if (CurrentTeam::id() === $timeId) {
                $user = User::query()->find($usuarioId);
                $proximo = $user?->times()->orderBy('nome')->first();
                CurrentTeam::set($proximo?->id);
            }

            Log::info('time_deleted', [
                'time_id' => $timeId,
                'usuario_id' => $usuarioId,
                'novo_time_id' => CurrentTeam::id(),
                'action' => 'delete',
            ]);
        } catch (TimeDomainException $exception) {
            throw $exception;
        } catch (TimeException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('time_delete_failed', [
                'time_id' => $timeId,
                'usuario_id' => $usuarioId,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw TimeException::deleteFailed();
        }
    }

    private function convertRecordToDomain(Time $time, bool $atorEhAdmin): TimeDomain
    {
        return TimeDomain::reconstituir(
            id: (int) $time->id,
            nome: $time->nome,
            contaId: (int) $time->conta_id,
            criadorId: (int) $time->usuario_id,
            atorEhAdmin: $atorEhAdmin,
            excluido: $time->trashed(),
        );
    }
}
