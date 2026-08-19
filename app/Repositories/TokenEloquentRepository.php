<?php

namespace App\Repositories;

use App\Domain\TokenDomain;
use App\Models\Conta;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class TokenEloquentRepository
{
    public function revokeAll(Conta $conta): void
    {
        $conta->tokens()->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Conta $conta, array $data): NewAccessToken
    {
        return $conta->createToken((string) $data['name']);
    }

    public function hasActive(Conta $conta): bool
    {
        return $conta->tokens()
            ->where('name', TokenDomain::TOKEN_NAME)
            ->exists();
    }

    public function getActive(Conta $conta): ?PersonalAccessToken
    {
        return $conta->tokens()
            ->where('name', TokenDomain::TOKEN_NAME)
            ->latest('id')
            ->first();
    }
}
