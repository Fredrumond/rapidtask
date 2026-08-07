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

    public function create(Conta $conta): NewAccessToken
    {
        return $conta->createToken(TokenDomain::TOKEN_NAME);
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
