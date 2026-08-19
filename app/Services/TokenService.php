<?php

namespace App\Services;

use App\Domain\TokenDomain;
use App\DTO\Token\TokenResponseDTO;
use App\Enums\TokenStatus;
use App\Exceptions\TokenDomainException;
use App\Exceptions\TokenException;
use App\Models\Conta;
use App\Repositories\TokenEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

class TokenService
{
    public function __construct(
        private readonly TokenEloquentRepository $repository,
    ) {}

    public function issue(Conta $conta): TokenResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($conta): TokenResponseDTO {
                $existing = $this->repository->getActive($conta);

                if ($existing !== null) {
                    $this->convertRecordToDomain($existing)->revogar();
                }

                $this->repository->revokeAll($conta);

                $domain = TokenDomain::criar();
                $accessToken = $this->repository->create($conta, $domain->toPersistenceArray());
                $domain = $this->convertRecordToDomain($accessToken->accessToken);
                $domain->anexarTextoPlano($accessToken->plainTextToken);

                return $this->convertToDTO($domain);
            });

            Log::info('api_token_issued', [
                'conta_id' => $conta->id,
                'action' => 'issue',
            ]);

            return $result;
        } catch (TokenDomainException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_token_issue_failed', [
                'conta_id' => $conta->id,
                'action' => 'issue',
                'error' => $exception->getMessage(),
            ]);

            throw TokenException::issueFailed();
        }
    }

    public function revoke(Conta $conta): void
    {
        try {
            DB::transaction(function () use ($conta): void {
                $token = $this->repository->getActive($conta);

                if ($token !== null) {
                    $this->convertRecordToDomain($token)->revogar();
                }

                $this->repository->revokeAll($conta);
            });

            Log::info('api_token_revoked', [
                'conta_id' => $conta->id,
                'action' => 'revoke',
            ]);
        } catch (TokenDomainException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('api_token_revoke_failed', [
                'conta_id' => $conta->id,
                'action' => 'revoke',
                'error' => $exception->getMessage(),
            ]);

            throw TokenException::revokeFailed();
        }
    }

    public function hasActiveToken(Conta $conta): bool
    {
        return $this->repository->hasActive($conta);
    }

    public function status(Conta $conta): TokenResponseDTO
    {
        $token = $this->repository->getActive($conta);

        if ($token === null) {
            return $this->convertToDTO(TokenDomain::inactive());
        }

        return $this->convertToDTO($this->convertRecordToDomain($token));
    }

    private function convertRecordToDomain(PersonalAccessToken $token): TokenDomain
    {
        return TokenDomain::reconstituir(
            status: TokenStatus::Ativo,
            createdAt: $token->created_at?->toIso8601String(),
        );
    }

    private function convertToDTO(TokenDomain $domain): TokenResponseDTO
    {
        return new TokenResponseDTO(
            active: $domain->isActive(),
            name: $domain->getTokenName(),
            plainTextToken: $domain->getPlainTextToken(),
            createdAt: $domain->getCreatedAt(),
        );
    }
}
