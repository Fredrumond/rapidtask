<?php

namespace App\Services;

use App\Domain\ConviteDomain;
use App\Exceptions\ConviteDomainException;
use App\Exceptions\ConviteException;
use App\Mail\ConviteTimeMail;
use App\Models\TimeMembroConvite;
use App\Models\User;
use App\Repositories\ConviteEloquentRepository;
use App\Support\CurrentTeam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class ConviteService
{
    private const NIVEL_MEMBRO = 2;

    private const VALIDADE_DIAS = 7;

    public function __construct(
        private readonly ConviteEloquentRepository $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function emitir(int $timeId, int $adminId, array $data): void
    {
        try {
            $convite = DB::transaction(function () use ($timeId, $data): TimeMembroConvite {
                if (! $this->repository->timeExists($timeId)) {
                    throw ConviteException::createFailed();
                }

                $contaId = $this->repository->findTimeContaId($timeId);
                $email = (string) $data['email'];
                $convidado = User::query()->where('email', $email)->first();
                $emailJaPertenceAOutraConta = $convidado !== null
                    && $contaId !== null
                    && $convidado->belongsToOtherConta($contaId);

                $domain = ConviteDomain::emitir(
                    nome: (string) $data['nome'],
                    email: $email,
                    token: Str::random(64),
                    timeId: $timeId,
                    emailJaPertenceAOutraConta: $emailJaPertenceAOutraConta,
                );

                return $this->repository->create($domain->toPersistenceArray());
            });

            $aceitarUrl = URL::temporarySignedRoute(
                'convites.aceitar',
                now()->addDays(self::VALIDADE_DIAS),
                ['convite' => $convite->id]
            );

            Mail::to($convite->email)->queue(new ConviteTimeMail($convite, $aceitarUrl));
        } catch (ConviteDomainException $exception) {
            throw $exception;
        } catch (ConviteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('convite_emit_failed', [
                'time_id' => $timeId,
                'usuario_id' => $adminId,
                'action' => 'emitir',
                'error' => $exception->getMessage(),
            ]);

            throw ConviteException::createFailed();
        }
    }

    public function aceitar(int $conviteId, int $usuarioId): void
    {
        try {
            $timeId = DB::transaction(function () use ($conviteId, $usuarioId): int {
                [$convite, $domain, $user] = $this->reconstituirParaAtor($conviteId, $usuarioId);

                $domain->aceitar($user->email, $this->contaIdDoUsuarioParaInvariante($user, $domain->getContaId()));

                $this->repository->update($convite, $domain->toPersistenceArray());
                $this->repository->addMembroIfMissing(
                    $domain->getTimeId(),
                    $usuarioId,
                    self::NIVEL_MEMBRO,
                );

                return $domain->getTimeId();
            });

            CurrentTeam::set($timeId);

            Log::info('convite_aceito', [
                'convite_id' => $conviteId,
                'time_id' => $timeId,
                'action' => 'aceitar',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (ConviteDomainException $exception) {
            throw $exception;
        } catch (ConviteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('convite_accept_failed', [
                'convite_id' => $conviteId,
                'usuario_id' => $usuarioId,
                'action' => 'aceitar',
                'error' => $exception->getMessage(),
            ]);

            throw ConviteException::updateFailed();
        }
    }

    public function recusar(int $conviteId, int $usuarioId): void
    {
        try {
            $timeId = DB::transaction(function () use ($conviteId, $usuarioId): int {
                [$convite, $domain, $user] = $this->reconstituirParaAtor($conviteId, $usuarioId);

                $domain->recusar($user->email);

                $this->repository->update($convite, $domain->toPersistenceArray());

                return $domain->getTimeId();
            });

            Log::info('convite_recusado', [
                'convite_id' => $conviteId,
                'time_id' => $timeId,
                'action' => 'recusar',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (ConviteDomainException $exception) {
            throw $exception;
        } catch (ConviteException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('convite_decline_failed', [
                'convite_id' => $conviteId,
                'usuario_id' => $usuarioId,
                'action' => 'recusar',
                'error' => $exception->getMessage(),
            ]);

            throw ConviteException::updateFailed();
        }
    }

    /**
     * @return array{0: TimeMembroConvite, 1: ConviteDomain, 2: User}
     */
    private function reconstituirParaAtor(int $conviteId, int $usuarioId): array
    {
        $convite = $this->repository->find($conviteId);

        if ($convite === null) {
            throw ConviteException::notFound();
        }

        $user = User::query()->find($usuarioId);

        if ($user === null) {
            throw ConviteException::notFound();
        }

        $domain = $this->convertRecordToDomain($convite);

        return [$convite, $domain, $user];
    }

    private function convertRecordToDomain(TimeMembroConvite $convite): ConviteDomain
    {
        return ConviteDomain::reconstituir(
            id: (int) $convite->id,
            nome: $convite->nome,
            email: $convite->email,
            timeId: (int) $convite->time_id,
            token: (string) ($convite->token ?? ''),
            status: (int) $convite->status,
            contaId: $this->repository->findTimeContaId((int) $convite->time_id),
        );
    }

    private function contaIdDoUsuarioParaInvariante(User $user, ?int $contaDoConvite): ?int
    {
        foreach ($user->linkedContaIds() as $linkedId) {
            if ($contaDoConvite === null || $linkedId !== $contaDoConvite) {
                return $linkedId;
            }
        }

        return $contaDoConvite;
    }
}
