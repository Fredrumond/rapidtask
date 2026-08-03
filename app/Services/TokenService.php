<?php

namespace App\Services;

use App\Domain\TokenDomain;
use App\DTO\Token\TokenResponseDTO;
use App\Exceptions\TokenException;
use App\Models\User;
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

    public function issue(User $user): TokenResponseDTO
    {
        try {
            $result = DB::transaction(function () use ($user): TokenResponseDTO {
                $this->repository->revokeAll($user);

                $accessToken = $this->repository->create($user);
                $domain = $this->convertRecordToDomain($accessToken->accessToken)
                    ->withPlainTextToken($accessToken->plainTextToken);

                return $this->convertToDTO($domain);
            });

            Log::info('api_token_issued', [
                'user_id' => $user->id,
                'action' => 'issue',
            ]);

            return $result;
        } catch (Throwable $exception) {
            Log::error('api_token_issue_failed', [
                'user_id' => $user->id,
                'action' => 'issue',
                'error' => $exception->getMessage(),
            ]);

            throw TokenException::issueFailed();
        }
    }

    public function revoke(User $user): void
    {
        try {
            DB::transaction(function () use ($user): void {
                $this->repository->revokeAll($user);
            });

            Log::info('api_token_revoked', [
                'user_id' => $user->id,
                'action' => 'revoke',
            ]);
        } catch (Throwable $exception) {
            Log::error('api_token_revoke_failed', [
                'user_id' => $user->id,
                'action' => 'revoke',
                'error' => $exception->getMessage(),
            ]);

            throw TokenException::revokeFailed();
        }
    }

    public function hasActiveToken(User $user): bool
    {
        return $this->repository->hasActive($user);
    }

    public function status(User $user): TokenResponseDTO
    {
        $token = $this->repository->getActive($user);

        if ($token === null) {
            return $this->convertToDTO(TokenDomain::inactive());
        }

        return $this->convertToDTO($this->convertRecordToDomain($token));
    }

    private function convertRecordToDomain(PersonalAccessToken $token): TokenDomain
    {
        return TokenDomain::active($token->created_at?->toIso8601String());
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
